set schema 'bibliotheque';

set plpgsql.extra_errors to 'all';

-- utils

create function rmodulo(x numeric, y numeric) returns numeric as $$
    select (y + x % y) % y;
$$ language sql strict immutable;
comment on function rmod is
'Retourne le modulo de deux entiers.
@param x La première opérande
@param y La seconde opérande
@return @p x % @p y, mais du signe de @p y au lieu de l''opérateur % ou la fonction mod, qui returnent @p x mod @p y du signe de @p x.';

create function bounds(multirange anymultirange) returns table (
    bound anyelement,
    inclusive bool
) as $$
    with ranges as (select unnest(multirange) m)
    select lower(m) bound, lower_inc(m) inclusive from ranges
    union all
    select upper(m), upper_inc(m) from ranges
$$ language sql strict immutable;

-- Fonction PL/pgSQL pour générer une cote à l’insertion
create or replace function generate_cote_for_livre() returns trigger as $$
begin
    if new.genre_principal is null then
        raise exception 'genre_principal must be set to generate cote';
    end if;

    -- Récupère les 3 premières lettres du genre principal, en majuscules
    declare genre_prefix varchar(3);
    begin
        select upper(substring(nom from 1 for 3)) into genre_prefix from _genre where id = new.genre_principal;
        if genre_prefix is null then
            genre_prefix := 'GEN';
        end if;
    end;

    -- Génère la cote : préfixe + lettre aléatoire + nombre à trois chiffres
    declare random_letter char;
    declare random_number int;
    declare base_cote varchar(10);
    random_letter := chr((random()*26)::int + 65); -- A-Z
    random_number := floor(random()*1000)::int;    -- 000-999
    base_cote := genre_prefix || random_letter || lpad(random_number::text, 3, '0');

    -- Vérifie l'unicité pour le même compte utilisateur, recommence si déjà présent (jusqu'à 10 tentatives)
    for i in 1..10 loop
        if exists(select 1 from bibliotheque._livre where cote = base_cote and numero_compte = new.numero_compte) then
            random_letter := chr((random()*26)::int + 65);
            random_number := floor(random()*1000)::int;
            base_cote := genre_prefix || random_letter || lpad(random_number::text, 3, '0');
        else
            exit;
        end if;
    end loop;

    new.cote := base_cote;
    return new;
end;
$$ language plpgsql;