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

    perform pg_catalog.pg_file_write(v_path, v_contenu, false);

    raise notice 'Fichier auteurs généré : %', v_path;
end;
$$ language plpgsql;
