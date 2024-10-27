begin;

set schema 'pact';
 
set plpgsql.extra_errors to 'all';

-- Insère un compte et retourne son id.
create or replace function insert_compte(new record)
    returns int
    as $$
declare
    identite int;
    signalable int;
    compte int;
begin
    insert into pact._identite default
        values
        returning
            id_identite into identite;
    insert into pact._signalable default
        values
        returning
            id_signalable into signalable;
    insert into pact._compte(id_compte, id_signalable, email, mdp_hash, nom, prenom, telephone)
        values (identite, signalable, new.email, new.mdp_hash, new.nom, new.prenom, new.telephone)
        returning id_compte into compte;
    return compte;
end;
$$
language 'plpgsql';

-- Membre -> Insert
create or replace function membres_insert()
    returns trigger
    as $$
declare
    compte constant int = insert_compte(new);
begin
    insert into pact._membre(id_membre, pseudo)
        values (compte, new.pseudo);
    return new;
end;
$$
language 'plpgsql';

create or replace trigger tg_membres_insert
    instead of insert on membres for each row
    execute function membres_insert();

-- Privé -> Insert
create or replace function pro_prive_insert()
    returns trigger
    as $$
declare
    compte constant int = insert_compte(new);
begin
    insert into pact._professionnel(id_professionnel, denomination)
        values (compte, new.denomination);
    insert into pact._prive(id_prive, siren)
        values (compte, new.siren);
    return new;
end;
$$
language 'plpgsql';

create or replace trigger tg_pro_prive_insert
    instead of insert on pro_prive for each row
    execute function pro_prive_insert();

-- Public -> Insert
create or replace function pro_public_insert()
    returns trigger
    as $$
declare
    compte constant int = insert_compte(new);
begin
    insert into pact._professionnel(id_professionnel, denomination)
        values (compte, new.denomination);
    insert into pact._public(id_public)
        values (compte);
    return new;
end;
$$
language 'plpgsql';

create or replace trigger tg_pro_public_insert
    instead of insert on pro_public for each row
    execute function pro_public_insert();

commit;