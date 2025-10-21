set schema 'bibliotheque';

set
    plpgsql.extra_errors to 'all';

create or replace function regenerer_fichier_auteurs()
returns void as $$
declare
    v_path text := '/library/sql/instances/main/pact/authors.sql';
    v_contenu text := '';
    r_auteur record;
begin
    -- En-tête du fichier
    v_contenu := '-- =====================================' || E'\n';
    v_contenu := v_contenu || '--  Sauvegarde complète des auteurs' || E'\n';
    v_contenu := v_contenu || '-- =====================================' || E'\n\n';

    -- Génération des instructions INSERT avec ID explicite
    for r_auteur in
        select id, prenom, nom
        from _auteur
        order by id
    loop
        v_contenu := v_contenu || format(
            'insert into _auteur (id, prenom, nom) values (%s, %L, %L);' || E'\n',
            r_auteur.id,
            r_auteur.prenom,
            r_auteur.nom
        );
    end loop;

    -- Écriture dans le fichier
    perform pg_catalog.pg_file_write(v_path, v_contenu, false);

    raise notice 'Fichier auteurs généré : %', v_path;
end;
$$ language plpgsql;

create or replace function auteur_insert()
returns trigger as $$
begin
    insert into _auteur (prenom, nom)
    values (new.prenom, new.nom)
    returning id into new.id;

    -- Mettre à jour le fichier auteurs.sql
    perform regenerer_fichier_auteurs();

    return new;
end;
$$ language plpgsql;

create or replace function auteur_update()
returns trigger as $$
begin
    update _auteur
    set prenom = new.prenom,
        nom = new.nom
    where id = old.id;

    -- Mettre à jour le fichier auteurs.sql
    perform regenerer_fichier_auteurs();

    return new;
end;
$$ language plpgsql;

create or replace function auteur_delete()
returns trigger as $$
begin
    delete from _auteur
    where id = old.id;

    -- Mettre à jour le fichier auteurs.sql
    perform regenerer_fichier_auteurs();

    return old;
end;
$$ language plpgsql;

-- Trigger pour INSERT
create trigger tg_auteur_insert
instead of insert on _auteur
for each row
execute function auteur_insert();

-- Trigger pour UPDATE
create trigger tg_auteur_update
instead of update on _auteur
for each row
execute function auteur_update();

-- Trigger pour DELETE
create trigger tg_auteur_delete
instead of delete on _auteur
for each row
execute function auteur_delete();
