set schema 'pact';

set
    plpgsql.extra_errors to 'all';

-- Create
create function avis_restaurant_insert () returns trigger as $$
begin
    if offre_categorie(new.id_restaurant) <> 'restaurant' then
        raise 'impossible d''insérer un avis_restaurant pour une offre qui n''est pas un restaurant';
    end if;
    new = insert_avis(new, new.id_restaurant);
    insert into pact._avis_restaurant (
        id,
        note_cuisine,
        note_service,
        note_ambiance,
        note_qualite_prix
    ) values (
        new.id,
        new.note_cuisine,
        new.note_service,
        new.note_ambiance,
        new.note_qualite_prix
    );
    return new;
end
$$ language plpgsql;

create trigger tg_avis_restaurant_insert instead of insert on avis_restaurant for each row
execute function avis_restaurant_insert ();