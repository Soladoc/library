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

-- Membre

create function id_compte(p_email varchar)
returns int as $$
    select numero_compte
    from _compte
    where email = p_email;
$$ language sql strict stable;

comment on function id_compte(varchar) is
'Retourne le numéro de compte à partir de l''adresse email.
@param p_email L''adresse email du compte.
@returns Le numéro de compte, ou NULL si aucun compte ne correspond à cette adresse.
Comme l''email est unique, un seul compte peut correspondre à une adresse donnée.';