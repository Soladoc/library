begin;

set schema 'pact';

set
    plpgsql.extra_errors to 'all';

-- Create
create function visite_insert () returns trigger as $$
begin
    new.id = insert_offre(new);
    insert into pact._visite (
        id,
        indication_duree
    ) values (
        new.id,
        new.indication_duree
    );
    return new;
end
$$ language plpgsql;

create trigger tg_visite_insert instead of insert on visite for each row
execute function visite_insert ();

commit;