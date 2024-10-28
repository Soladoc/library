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

-- spectacle -> insert
create function spectacle_insert()
    returns trigger
    as $$
begin
    insert into pact._spectacle
        (id, indication_duree, capacite_accueil)
    values
        ((select insert_offre(new)), new.indication_duree, new.capacite_accueil);
    return new;
end
$$
language 'plpgsql';

create trigger tg_spectacle_insert
    instead of insert on spectacle for each row
    execute function spectacle_insert();

-- visite -> insert
create function visite_insert()
    returns trigger
    as $$
begin
    insert into pact._visite
        (id, indication_duree)
    values
        ((select insert_offre(new)), new.indication_duree);
    return new;
end
$$
language 'plpgsql';

create trigger tg_visite_insert
    instead of insert on visite for each row
    execute function visite_insert();

-- parc_attractions -> insert
create function parc_attractions_insert()
    returns trigger
    as $$
begin
    insert into pact._parc_attractions
        (id, id_image_plan)
    values
        ((select insert_offre(new)), new.id_image_plan);
    return new;
end
$$
language 'plpgsql';

create trigger tg_parc_attractions_insert
    instead of insert on parc_attractions for each row
    execute function parc_attractions_insert();

-- restaurant -> insert
create function restaurant_insert()
    returns trigger
    as $$
begin
    insert into pact._restaurant
        (id, carte, richesse, sert_petit_dejeuner, sert_brunch, sert_dejeuner, sert_diner, sert_boissons)
    values
        ((select insert_offre(new)), new.carte, new.richesse, new.sert_petit_dejeuner, new.sert_brunch, new.sert_dejeuner, new.sert_diner, new.sert_boissons);
    return new;
end
$$
language 'plpgsql';

create trigger tg_restaurant_insert
    instead of insert on restaurant for each row
    execute function restaurant_insert();

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