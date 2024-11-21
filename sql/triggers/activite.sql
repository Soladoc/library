set schema 'pact';

set
    plpgsql.extra_errors to 'all';

-- Create
create function activite_insert () returns trigger as $$
begin
    new.id = insert_offre(new);
    insert into pact._activite (
        id,
        indication_duree,
        age_requis,
        prestations_incluses,
        prestations_non_incluses
    ) values (
        new.id,
        new.indication_duree,
        coalesce(new.age_requis, 0),
        new.prestations_incluses,
        coalesce(new.prestations_non_incluses, '')
    );
    return new;
end
$$ language plpgsql;

create trigger tg_activite_insert instead of insert on activite for each row
execute function activite_insert ();
