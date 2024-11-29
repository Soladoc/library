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
        new.age_requis,
        new.prestations_incluses,
        new.prestations_non_incluses
    );
    return new;
end
$$ language plpgsql;

create trigger tg_activite_insert instead of insert on activite for each row
execute function activite_insert ();

-- Update
create trigger tg_activite_after_update after update on _activite for each row
execute function _offre_after_update ();
