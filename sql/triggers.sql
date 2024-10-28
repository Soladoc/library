begin;

set schema 'pact';
 
set plpgsql.extra_errors to 'all';

-- activite -> insert
create function activite_insert()
    returns trigger
    as $$
begin
    insert into pact._activite
        (id, indication_duree, age_requis, prestations_incluses, prestations_non_incluses)
    values
        ((select insert_offre(new)), new.indication_duree, new.age_requis, new.prestations_incluses, new.prestations_non_incluses);
    return new;
end
$$
language 'plpgsql';

create trigger tg_activite_insert
    instead of insert on activite for each row
    execute function activite_insert();

-- membre -> insert
create function membre_insert()
    returns trigger
    as $$
begin
    insert into pact._membre(id, pseudo)
        values ((select insert_compte(new)), new.pseudo);
    return new;
end
$$
language 'plpgsql';

create trigger tg_membre_insert
    instead of insert on membre for each row
    execute function membre_insert();

-- pro_prive -> Insert
create function pro_prive_insert()
    returns trigger
    as $$
declare
    id_compte constant int = insert_compte(new);
begin
    insert into pact._professionnel(id, denomination)
        values (id_compte, new.denomination);
    insert into pact._prive(id, siren)
        values (id_compte, new.siren);
    return new;
end
$$
language 'plpgsql';

create trigger tg_pro_prive_insert
    instead of insert on pro_prive for each row
    execute function pro_prive_insert();

-- pro_public -> insert
create function pro_public_insert()
    returns trigger
    as $$
declare
    id_compte constant int = insert_compte(new);
begin
    insert into pact._professionnel(id, denomination)
        values (id_compte, new.denomination);
    insert into pact._public(id)
        values (id_compte);
    return new;
end
$$
language 'plpgsql';

create trigger tg_pro_public_insert
    instead of insert on pro_public for each row
    execute function pro_public_insert();

commit;