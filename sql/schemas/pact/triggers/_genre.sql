set schema 'bibliotheque';

set
    plpgsql.extra_errors to 'all';

create or replace function regenerer_fichier_genres()
returns void as $$
declare
    v_path text := '/library/sql/instances/main/pact/genres.sql';
    v_contenu text := '';
    r_genre record;
begin
    -- En-tête du fichier
    v_contenu := '-- =====================================' || E'\n';
    v_contenu := v_contenu || '--  Sauvegarde complète des genres' || E'\n';
    v_contenu := v_contenu || '-- =====================================' || E'\n\n';

    -- Génération des instructions INSERT (avec id conservé)
    for r_genre in
        select id, nom
        from _genre
        order by id
    loop
        v_contenu := v_contenu || format(
            'insert into _genre (id, nom) values (%s, %L);' || E'\n',
            r_genre.id,
            r_genre.nom
        );
    end loop;

    -- Écriture du fichier
    perform pg_catalog.pg_file_write(v_path, v_contenu, false);

    raise notice 'Fichier genres généré : %', v_path;
end;
$$ language plpgsql;

-- Fonction de déclenchement générique
create or replace function tg_regenerer_genres()
returns trigger as $$
begin
    perform regenerer_fichier_genres();
    return null;
end;
$$ language plpgsql;

-- Triggers sur les événements de modification
create trigger tg_genre_after_insert
after insert on _genre
for each statement execute function tg_regenerer_genres();

create trigger tg_genre_after_update
after update on _genre
for each statement execute function tg_regenerer_genres();

create trigger tg_genre_after_delete
after delete on _genre
for each statement execute function tg_regenerer_genres();
