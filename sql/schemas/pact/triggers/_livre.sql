set schema 'bibliotheque';

set
    plpgsql.extra_errors to 'all';

create or replace function regenerer_fichier_compte(p_numero int)
returns void as $$
declare
    v_path text := format('/library/sql/instances/main/pact/%s.sql', p_numero);
    v_contenu text := '';
    r_compte record;
    r_image record;
    r_livre record;
    r_auteur record;
    r_genre record;
    r_avis record;
begin
    -- Récupère le compte actuel
    select * into r_compte from _compte where numero_compte = p_numero;
    if not found then
        raise notice 'Aucun compte trouvé avec le numéro %', p_numero;
        return;
    end if;

    -- En-tête
    v_contenu := format('-- Sauvegarde du compte (%s)' || E'\n\n', r_compte.email);

    -- Insertion du compte
    v_contenu := v_contenu || format(
        'insert into _compte (email, mdp_hash) values (%L, %L);' || E'\n' ||
        'do $$ declare new_num int; v_livre_id int; begin ' ||
        'select numero_compte into new_num from _compte where email = %L; ' || E'\n',
        r_compte.email,
        r_compte.mdp_hash,
        r_compte.email
    );

    -- Sauvegarde des images
    for r_image in
        select distinct i.id, i.mime_subtype
        from _image i
        join _livre l on l.nom_image = i.id
        where l.numero_compte = p_numero
        order by i.id
    loop
        v_contenu := v_contenu || format(
            'insert into _image (id, mime_subtype) values (%s, %L);' || E'\n',
            r_image.id,
            r_image.mime_subtype
        );
    end loop;
    v_contenu := v_contenu || E'\n';

    -- Sauvegarde des livres et liaisons auteurs/genres
    for r_livre in
        select l.id, l.titre, l.nom_image, l.genre_principal, l.cote
        from _livre l
        where l.numero_compte = p_numero
        order by l.id
    loop
        -- Insert du livre avec genre_principal et cote
        v_contenu := v_contenu || format(
            'insert into _livre (titre, nom_image, numero_compte, genre_principal, cote) values (%L, %s, new_num, %s, %L) returning id into v_livre_id;' || E'\n',
            r_livre.titre,
            case when r_livre.nom_image is not null then r_livre.nom_image::text else 'null' end,
            coalesce(r_livre.genre_principal::text,'null'),
            r_livre.cote
        );

        -- Liaisons auteurs
        for r_auteur in
            select a.id
            from _auteur a
            join _livre_auteur la on la.id_auteur = a.id
            where la.id_livre = r_livre.id
        loop
            v_contenu := v_contenu || format(
                'insert into _livre_auteur (id_livre, id_auteur) values (v_livre_id, %s);' || E'\n',
                r_auteur.id
            );
        end loop;

        -- Liaisons genres secondaires
        for r_genre in
            select g.id
            from _genre g
            join _livre_genre lg on lg.id_genre = g.id
            where lg.id_livre = r_livre.id
        loop
            v_contenu := v_contenu || format(
                'insert into _livre_genre (id_livre, id_genre) values (v_livre_id, %s);' || E'\n',
                r_genre.id
            );
        end loop;

        -- Avis
        for r_avis in
            select *
            from _avis
            where id_livre = r_livre.id
        loop
            v_contenu := v_contenu || format(
                'insert into _avis (id_livre, titre_avis, commentaire, note, note_ecriture, note_intrigue, note_personnages) ' ||
                'values (v_livre_id, %L, %L, %s, %s, %s, %s);' || E'\n',
                r_avis.titre_avis,
                r_avis.commentaire,
                r_avis.note,
                coalesce(r_avis.note_ecriture::text,'null'),
                coalesce(r_avis.note_intrigue::text,'null'),
                coalesce(r_avis.note_personnages::text,'null')
            );
        end loop;

        v_contenu := v_contenu || E'\n';
    end loop;

    -- Fin du bloc
    v_contenu := v_contenu || 'end $$;' || E'\n';

    -- Écriture du fichier
    perform pg_catalog.pg_file_write(v_path, v_contenu, false);

    raise notice 'Fichier du compte % enregistré : %', r_compte.email, v_path;
end;
$$ language plpgsql;

-- Insertion des auteurs pour un livre donné
create or replace function v_livre_complet_insert_auteurs(p_id_livre int, p_auteurs text)
returns void as $$
declare
    v_auteur text;
    v_prenom text;
    v_nom text;
begin
    foreach v_auteur in array string_to_array(p_auteurs, ',') loop
        v_auteur := trim(v_auteur);
        v_prenom := split_part(v_auteur, ' ', 1);
        v_nom := substring(v_auteur from position(' ' in v_auteur) + 1);
        if not exists(select 1 from _auteur where prenom = v_prenom and nom = v_nom) then
            insert into _auteur (prenom, nom) values (v_prenom, v_nom);
        end if;
        insert into _livre_auteur (id_livre, id_auteur)
        select p_id_livre, id from _auteur where prenom = v_prenom and nom = v_nom;
    end loop;
end;
$$ language plpgsql;

-- Insertion des genres pour un livre donné
create or replace function v_livre_complet_insert_genres(p_id_livre int, p_genres text)
returns void as $$
declare
    v_genre text;
begin
    foreach v_genre in array string_to_array(p_genres, ',') loop
        v_genre := trim(v_genre);
        if not exists(select 1 from _genre where nom = v_genre) then
            insert into _genre (nom) values (v_genre);
        end if;
        insert into _livre_genre (id_livre, id_genre)
        select p_id_livre, id from _genre where nom = v_genre;
    end loop;
end;
$$ language plpgsql;

create or replace function v_livre_complet_insert()
returns trigger as $$
declare
    new_image_id int;
    v_id_livre int;
begin
    -- Si une image est précisée, l’ajouter dans _image
    if new.nom_image is not null then
        insert into _image (mime_subtype)
        values (new.nom_image)
        returning id into new_image_id;
    else
        new_image_id := null;
    end if;

    -- Insère le livre avec genre_principal (et laisse cote générée automatiquement par le trigger)
    insert into _livre (titre, nom_image, numero_compte, genre_principal)
    values (new.titre, new_image_id, new.numero_compte, new.genre_principal)
    returning id into v_id_livre;

    -- Gère les auteurs
    if new.auteurs is not null then
        perform v_livre_complet_insert_auteurs(v_id_livre, new.auteurs);
    end if;

    -- Gère les genres secondaires
    if new.genres is not null then
        perform v_livre_complet_insert_genres(v_id_livre, new.genres);
    end if;

    -- Sauvegarde du compte
    perform regenerer_fichier_compte(new.numero_compte);

    return new;
end;
$$ language plpgsql;

create or replace function v_livre_complet_update()
returns trigger as $$
declare
    new_image_id int;
begin
    -- Interdire le changement de compte
    if new.numero_compte is distinct from old.numero_compte then
        raise exception 'Impossible de changer le compte propriétaire d’un livre.';
    end if;

    -- Si une nouvelle image est fournie, supprimer l’ancienne et insérer la nouvelle
    if new.nom_image is distinct from old.nom_image then
        if old.nom_image is not null then
            delete from _image where id = old.nom_image;
        end if;
        if new.nom_image is not null then
            insert into _image (mime_subtype)
            values (new.nom_image)
            returning id into new_image_id;
        else
            new_image_id := null;
        end if;
    else
        new_image_id := old.nom_image;
    end if;

    -- Met à jour le livre, y compris genre_principal
    update _livre
    set titre = new.titre,
        nom_image = new_image_id,
        genre_principal = new.genre_principal
    where id = old.id;

    -- Met à jour les auteurs
    delete from _livre_auteur where id_livre = old.id;
    if new.auteurs is not null then
        perform v_livre_complet_insert_auteurs(old.id, new.auteurs);
    end if;

    -- Met à jour les genres secondaires
    delete from _livre_genre where id_livre = old.id;
    if new.genres is not null then
        perform v_livre_complet_insert_genres(old.id, new.genres);
    end if;

    -- Sauvegarde du compte
    perform regenerer_fichier_compte(new.numero_compte);

    return new;
end;
$$ language plpgsql;

create or replace function v_livre_complet_delete()
returns trigger as $$
begin
    -- Supprime le livre
    delete from _livre where id = old.id;

    -- Supprime l'image associée si elle existe
    if old.nom_image is not null then
        delete from _image where id = old.nom_image;
    end if;

    -- Sauvegarde du compte
    perform regenerer_fichier_compte(old.numero_compte);

    return old;
end;
$$ language plpgsql;

create trigger tg_v_livre_complet_insert
instead of insert on v_livre_complet
for each row
execute function v_livre_complet_insert();

create trigger tg_v_livre_complet_update
instead of update on v_livre_complet
for each row
execute function v_livre_complet_update();

create trigger tg_v_livre_complet_delete
instead of delete on v_livre_complet
for each row
execute function v_livre_complet_delete();
