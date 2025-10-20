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
    v_contenu := '-- =====================================' || E'\n';
    v_contenu := v_contenu || '--  Tous les auteurs' || E'\n';
    v_contenu := v_contenu || '-- =====================================' || E'\n\n';

    for r_auteur in
        select prenom, nom
        from _auteur
        order by id
    loop
        v_contenu := v_contenu || format(
            'insert into _auteur (prenom, nom) values (%L, %L);' || E'\n',
            r_auteur.prenom,
            r_auteur.nom
        );
    end loop;

    perform pg_catalog.pg_file_write(v_path, v_contenu, false);

    raise notice 'Fichier auteurs généré : %', v_path;
end;
$$ language plpgsql;

create or replace function v_auteur_insert()
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

create or replace function v_auteur_update()
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

create or replace function v_auteur_delete()
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
create trigger tg_v_auteur_insert
instead of insert on v_auteur
for each row
execute function v_auteur_insert();

-- Trigger pour UPDATE
create trigger tg_v_auteur_update
instead of update on v_auteur
for each row
execute function v_auteur_update();

-- Trigger pour DELETE
create trigger tg_v_auteur_delete
instead of delete on v_auteur
for each row
execute function v_auteur_delete();
