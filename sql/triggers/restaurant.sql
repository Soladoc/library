set schema 'pact';

set
    plpgsql.extra_errors to 'all';

-- Create
create function restaurant_insert () returns trigger as $$
begin
    new = insert_offre(new);
    insert into pact._restaurant (
        id,
        carte,
        richesse,
        sert_petit_dejeuner,
        sert_brunch,
        sert_dejeuner,
        sert_diner,
        sert_boissons
    ) values (
        new.id,
        new.carte,
        new.richesse,
        coalesce(new.sert_petit_dejeuner, false),
        coalesce(new.sert_brunch, false),
        coalesce(new.sert_dejeuner, false),
        coalesce(new.sert_diner, false),
        coalesce(new.sert_boissons, false)
    );
    return new;
end
$$ language plpgsql;

create trigger tg_restaurant_insert instead of insert on restaurant for each row
execute function restaurant_insert ();

-- Update
create trigger tg_restaurant_after_update after update on _restaurant for each row
execute function _offre_after_update ();

