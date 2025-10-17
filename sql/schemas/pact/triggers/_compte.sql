set schema 'bibliotheque';

set
    plpgsql.extra_errors to 'all';

-- Create
create or replace function v_compte_insert()
returns trigger as $$
declare
    fichier text;
    insert_cmd text;
begin
    -- Insère le compte dans la table réelle
    insert into _compte (email, mdp_hash)
    values (new.email, new.mdp_hash)
    returning numero_compte into new.numero_compte;

    -- Crée la commande INSERT
    insert_cmd := format(
        'insert into _compte (numero_compte, email, mdp_hash) values (%s, %L, %L);',
        new.numero_compte, new.email, new.mdp_hash
    );

    -- Nom du fichier : par exemple /sauvegardes/compte_<num>.sql
    fichier := format('/library/sql/instances/main/pact/compte_%s.sql', new.numero_compte);

    -- Écrit dans le fichier
    execute format(
        $$COPY (select %L) TO %L;$$,
        insert_cmd, fichier
    );

    return new;
end;
$$ language plpgsql;

create or replace trigger tg_v_compte_insert
instead of insert on v_compte
for each row
execute function v_compte_insert();

-- Update

create or replace function v_compte_update()
returns trigger as $$
declare
    fichier text;
    insert_cmd text;
begin
    -- Interdire toute modification autre que le mot de passe
    if new.email <> old.email or new.numero_compte <> old.numero_compte then
        raise exception 'Modification de l''email ou du numéro de compte interdite.';
    end if;

    -- Mise à jour du mot de passe dans la table réelle
    update _compte
    set mdp_hash = new.mdp_hash
    where numero_compte = old.numero_compte;

    -- Recrée la commande INSERT à jour
    insert_cmd := format(
        'insert into _compte (numero_compte, email, mdp_hash) values (%s, %L, %L);',
        old.numero_compte, old.email, new.mdp_hash
    );

    -- Nom du fichier correspondant
    fichier := format('/library/sql/instances/main/pact/compte_%s.sql', old.numero_compte);

    -- Réécrit le fichier avec la version mise à jour
    execute format(
        $$COPY (select %L) TO %L;$$,
        insert_cmd, fichier
    );

    return new;
end;
$$ language plpgsql;

create or replace trigger tg_v_compte_update
instead of update on v_compte
for each row
execute function v_compte_update();

comment on function v_compte_update() is
'Autorise uniquement la modification du mot de passe (mdp_hash) via la vue v_compte.
Met également à jour le fichier compte_<numero_compte>.sql correspondant.';


-- Delete
create or replace function v_compte_delete()
returns trigger as $$
declare
    fichier text;
begin
    -- Supprime d'abord les livres associés au compte
    delete from _livre
    where numero_compte = old.numero_compte;

    -- Supprime le compte dans la table réelle
    delete from _compte
    where numero_compte = old.numero_compte;

    -- Détermine le chemin du fichier de sauvegarde
    fichier := format('/library/sql/instances/main/pact/compte_%s.sql', old.numero_compte);

    -- Supprime le fichier associé au compte
    execute format($$COPY (select pg_stat_file(%L)) TO '/dev/null';$$, fichier);
    perform pg_catalog.pg_file_unlink(fichier);

    return old;
end;
$$ language plpgsql;

create or replace trigger tg_v_compte_delete
instead of delete on v_compte
for each row
execute function v_compte_delete();

comment on function v_compte_delete() is
'Supprime un compte via la vue v_compte : efface les livres associés, 
supprime le compte de la table _compte et efface le fichier compte_<numero>.sql correspondant.';
