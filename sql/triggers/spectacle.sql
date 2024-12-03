set schema 'pact';

set
    plpgsql.extra_errors to 'all';

-- Create
create function spectacle_insert () returns trigger as $$
begin
    new = insert_offre(new);
    insert into pact._spectacle (
        id,
        indication_duree,
        capacite_accueil
    ) values (
        new.id,
        new.indication_duree,
        new.capacite_accueil
    );
    return new;
end
$$ language plpgsql;

create trigger tg_spectacle_insert instead of insert on spectacle for each row
execute function spectacle_insert ();

-- Update
create trigger tg_spectacle_after_update after update on _spectacle for each row
execute function _offre_after_update ();