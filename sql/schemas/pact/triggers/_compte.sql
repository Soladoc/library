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
        select l.id, l.titre, l.nom_image
        from _livre l
        where l.numero_compte = p_numero
        order by l.id
    loop
        -- Insert du livre
        v_contenu := v_contenu || format(
            'insert into _livre (titre, nom_image, numero_compte) values (%L, %s, new_num) returning id into v_livre_id;' || E'\n',
            r_livre.titre,
            case when r_livre.nom_image is not null then r_livre.nom_image::text else 'null' end
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

        -- Liaisons genres
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

-- =========================================
-- Fonction d'insertion dans la vue v_compte
-- =========================================
create or replace function v_compte_insert()
returns trigger as $$
declare
    v_numero int;
begin
    -- Insertion du compte dans la table _compte
    insert into _compte (email, mdp_hash)
    values (new.email, new.mdp_hash)
    returning numero_compte into v_numero;

    -- Appel de la fonction de génération du fichier
    perform regenerer_fichier_compte(v_numero);

    -- Retourne la ligne complète (y compris le numéro auto-généré)
    new.numero_compte := v_numero;
    return new;
end;
$$ language plpgsql;

-- =========================================
-- Trigger sur la vue v_compte
-- =========================================
create or replace trigger tg_v_compte_insert
instead of insert on v_compte
for each row
execute function v_compte_insert();

-- =========================================
-- Fonction de mise à jour sur la vue v_compte
-- =========================================
create or replace function v_compte_update()
returns trigger as $$
begin
    -- Interdiction de modifier l'email ou le numéro
    if old.email <> new.email or old.numero_compte <> new.numero_compte then
        raise exception 'Seul le mot de passe peut être modifié dans un compte.';
    end if;

    -- Mise à jour du mot de passe dans la table réelle
    update _compte
    set mdp_hash = new.mdp_hash
    where numero_compte = old.numero_compte;

    -- Régénération du fichier du compte après modification
    perform regenerer_fichier_compte(old.numero_compte);

    return new;
end;
$$ language plpgsql;

-- =========================================
-- Trigger sur la vue v_compte
-- =========================================
create or replace trigger tg_v_compte_update
instead of update on v_compte
for each row
execute function v_compte_update();

-- =========================================
-- Fonction de suppression sur la vue v_compte
-- =========================================
create or replace function v_compte_delete()
returns trigger as $$
begin
    -- Suppression du compte
    delete from _compte
    where numero_compte = old.numero_compte;

    -- Régénération du fichier (pour supprimer les livres/avis associés)
    perform regenerer_fichier_compte(old.numero_compte);

    return old;
end;
$$ language plpgsql;

-- =========================================
-- Trigger sur la vue v_compte
-- =========================================
create or replace trigger tg_v_compte_delete
instead of delete on v_compte
for each row
execute function v_compte_delete();

