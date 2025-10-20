set schema 'bibliotheque';

create or replace function regenerer_fichier_compte(p_numero int)
returns void as $$
declare
    v_path text := '/library/sql/instances/main/pact/compte_' || p_numero || '.sql';
    v_contenu text := '';
    r_compte record;
    r_livre record;
    r_avis record;
    r_image record;
    r_la record;
    nouveau_numero int;
    nouveau_id int;
begin
    -- Début du contenu du fichier
    v_contenu := v_contenu || '-- =====================================' || E'\n';
    v_contenu := v_contenu || '--  Données du compte n°' || p_numero || E'\n';
    v_contenu := v_contenu || '-- =====================================' || E'\n\n';

    -- Compte : insertion sans numéro, récupération auto-généré
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
        'insert into _compte (email, mdp_hash) values (%L, %L) returning numero_compte into nouveau_numero;' || E'\n\n',
        r_compte.email,
        r_compte.mdp_hash
    );

    -- Livres
    for r_livre in
        select id, titre, nom_image
        from _livre
        where numero_compte = p_numero
    loop
        v_contenu := v_contenu || format(
            '-- Livre : %s' || E'\n' ||
            'insert into _livre (titre, nom_image, numero_compte) values (%L, %s, nouveau_numero) returning id into nouveau_id;' || E'\n',
            r_livre.titre,
            coalesce(r_livre.nom_image::text, 'null')
        );

        -- Liaison livre ↔ auteur
        for r_la in
            select id_auteur
            from _livre_auteur
            where id_livre = r_livre.id
        loop
            v_contenu := v_contenu || format(
                'insert into _livre_auteur (id_livre, id_auteur) values (nouveau_id, %s);' || E'\n',
                r_la.id_auteur
            );
        end loop;

        -- Avis associé
        for r_avis in
            select titre_avis, commentaire, note, note_ecriture, note_intrigue, note_personnages
            from _avis
            where id_livre = r_livre.id
        loop
            v_contenu := v_contenu || format(
                'insert into _avis (titre_avis, commentaire, note, note_ecriture, note_intrigue, note_personnages, id_livre) ' ||
                'values (%L, %L, %s, %s, %s, %s, nouveau_id);' || E'\n',
                r_avis.titre_avis,
                r_avis.commentaire,
                coalesce(r_avis.note::text, 'null'),
                coalesce(r_avis.note_ecriture::text, 'null'),
                coalesce(r_avis.note_intrigue::text, 'null'),
                coalesce(r_avis.note_personnages::text, 'null')
            );
        end loop;

        v_contenu := v_contenu || E'\n';
    end loop;

    -- Images utilisées
    for r_image in
        select distinct i.id, i.mime_subtype
        from _image i
        join _livre l on l.nom_image = i.id
        where l.numero_compte = p_numero
    loop
        v_contenu := v_contenu || format(
            '-- Image : %s' || E'\n' ||
            'insert into _image (id, mime_subtype) values (%s, %L);' || E'\n\n',
            r_image.id,
            coalesce(r_image.mime_subtype, 'inconnue')
        );
    end loop;

    -- Écriture du fichier final
    perform pg_catalog.pg_file_write(v_path, v_contenu, false);
    raise notice 'Fichier généré : %', v_path;

end;
$$ language plpgsql;

create or replace function _image_after_insert()
returns trigger as $$
begin
    perform regenerer_fichier_images();
    return new;
end;
$$ language plpgsql;

create or replace trigger tg__image_after_insert
after insert on _image
for each row
execute function _image_after_insert();

create or replace function _image_after_delete()
returns trigger as $$
begin
    perform regenerer_fichier_images();
    return old;
end;
$$ language plpgsql;

create or replace trigger tg__image_after_delete
after delete on _image
for each row
execute function _image_after_delete();
