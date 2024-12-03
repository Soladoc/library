set schema 'pact';

set
    plpgsql.extra_errors to 'all';

-- Create
create function avis_insert () returns trigger as $$
declare
begin
    if offre_categorie(new.id_offre) = 'restaurant' then
        raise 'insérer dans avis_restaurant pour les avis sur les restaurants';
    end if;
    new = insert_avis(new);
    return new;
end
$$ language plpgsql;

create trigger tg_avis_insert instead of insert on avis for each row
execute function avis_insert ();
