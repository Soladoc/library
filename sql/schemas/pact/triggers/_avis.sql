set schema 'pact';

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

-- Update
create function avis_update () returns trigger as $$
begin
    if old.id_offre <> new.id_offre then
        raise 'Ne peut pas modifier id_offre';
    end if;
    new = update_avis(old, new);
    return new;
end
$$ language plpgsql;

create trigger tg_avis_update instead of update on avis for each row
execute function avis_update ();

-- Delete

create function avis_delete () returns trigger as $$
begin
    delete from _signalable where id = old.id;
    return old;
end
$$ language plpgsql;

create trigger tg_avis_delete instead of delete on avis for each row
execute function avis_delete ();
