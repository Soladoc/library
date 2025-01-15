set schema 'pact';

set
    plpgsql.extra_errors to 'all';

-- Create
create function reponse_insert () returns trigger as $$
    insert into pact._signalable default values returning id into new.id;
    insert into
        pact._reponse (id, id_avis, contenu)
    values
        (new.id, new.id_avis, new.contenu);
    return new;
$$ language plpgsql;

create trigger tg_reponse_insert instead of insert on reponse for each row
execute function reponse_insert ();