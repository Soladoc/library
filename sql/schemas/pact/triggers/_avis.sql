set schema 'pact';

set
    plpgsql.extra_errors to 'all';

create or replace function regenerer_fichier_compte(p_numero int)
returns void as $$
declare
    v_path text := '/library/sql/instances/main/pact/compte_' || p_numero || '.sql';
    v_contenu text := '';
    r_compte record;
    r_livre record;
    r_avis record;
    r_image record;
begin
    -- ===============================
    -- Début du contenu du fichier
    -- ===============================
    v_contenu := v_contenu || '-- =====================================' || E'\n';
    v_contenu := v_contenu || '--  Données du compte n°' || p_numero || E'\n';
    v_contenu := v_contenu || '-- =====================================' || E'\n\n';

    -- ===============================
    -- Informations du compte
    -- ===============================
    select email, mdp_hash
    into r_compte
    from _compte
    where numero_compte = p_numero;

    if not found then
        raise notice 'Aucun compte trouvé pour le numéro %', p_numero;
        return;
    end if;

    v_contenu := v_contenu || format(
        '-- Compte' || E'\n' ||
        'insert into _compte (email, mdp_hash, numero_compte) values (%L, %L, %s);' || E'\n\n',
        r_compte.email,
        r_compte.mdp_hash,
        p_numero
    );

    -- ===============================
    -- Livres associés au compte
    -- ===============================
    for r_livre in
        select id, titre, auteurs, nom_image, numero_compte
        from _livre
        where numero_compte = p_numero
    loop
        v_contenu := v_contenu || format(
            '-- Livre : %s' || E'\n' ||
            'insert into _livre (id, titre, auteurs, nom_image, numero_compte) ' ||
            'values (%s, %L, %L, %s, %s);' || E'\n',
            r_livre.titre,
            r_livre.id,
            r_livre.titre,
            r_livre.auteurs,
            coalesce(r_livre.nom_image::text, 'null'),
            r_livre.numero_compte
        );

        -- ===============================
        -- Avis associé (un seul par livre)
        -- ===============================
        for r_avis in
            select id, titre_avis, commentaire, note, note_ecriture, note_intrigue, note_personnages
            from _avis
            where id_livre = r_livre.id
        loop
            v_contenu := v_contenu || format(
                'insert into _avis (id, titre_avis, commentaire, note, note_ecriture, note_intrigue, note_personnages, id_livre) ' ||
                'values (%s, %L, %L, %s, %s, %s, %s, %s);' || E'\n',
                r_avis.id,
                r_avis.titre_avis,
                r_avis.commentaire,
                coalesce(r_avis.note::text, 'null'),
                coalesce(r_avis.note_ecriture::text, 'null'),
                coalesce(r_avis.note_intrigue::text, 'null'),
                coalesce(r_avis.note_personnages::text, 'null'),
                r_livre.id
            );
        end loop;

        v_contenu := v_contenu || E'\n';
    end loop;

    -- ===============================
    -- Images utilisées par les livres
    -- ===============================
    for r_image in
        select distinct i.id, i.mime_subtype
        from _image i
        join _livre l on l.nom_image = i.id
        where l.numero_compte = p_numero
    loop
        v_contenu := v_contenu || format(
            '-- Image : %s' || E'\n' ||
            'insert into _image (id, mime_subtype) values (%s, %L);' || E'\n\n',
            coalesce(r_image.mime_subtype, 'inconnue'),
            r_image.id,
            r_image.mime_subtype
        );
    end loop;

    -- ===============================
    -- Écriture du fichier final
    -- ===============================
    perform pg_catalog.pg_file_write(v_path, v_contenu, false);

    raise notice 'Fichier généré : %', v_path;
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
