set schema 'pact';

set plpgsql.extra_errors to 'all';

-- utils

create function rmod(x numeric, y numeric) returns numeric as $$
    select (y + x % y) % y;
$$ language sql;
comment on function rmod is
'Retourne le modulo de deux entiers.
@param x La première opérande
@param y La seconde opérande
@return @p x % @p y, mais du signe de @p y au lieu de l''opérateur % ou la fonction mod, qui returnent @p x mod @p y du signe de @p x.';

-- Membre

create function id_membre(p_pseudo pseudonyme) returns int as $$
    select id from _membre where pseudo = p_pseudo;
$$ language sql;
comment on function id_membre (pseudonyme) is
'Retourne l''ID d''un membre à partir de son pseudo.
@param p_pseudo le pseudo du membre
@returns L''ID du membre, ou `null` si il n''existe pas de membre ayant le pseudo donné.

Comme le pseudo est `unique`, on peut garantir qu''il n''existe qu''un seul membre pour un pseudo donné.';

-- Offres

create function offre_categorie (p_id_offre int) returns mot_minuscule as $$
begin
    if p_id_offre in (select id from pact._restaurant) then return 'restaurant'; end if;
    if p_id_offre in (select id from pact._activite) then return 'activité'; end if;
    if p_id_offre in (select id from pact._visite) then return 'visite'; end if;
    if p_id_offre in (select id from pact._spectacle) then return 'spectacle'; end if;
    if p_id_offre in (select id from pact._parc_attractions) then return 'parc d''attractions'; end if;
    raise 'incohérence: offre non catégorisée';
end;
$$ language plpgsql;
comment on function offre_categorie (int) is
'Retourne la catégorie d''une offre.
@param p_id_offre l''ID de l''offre
@returns La catégorie de l''offre d''ID @p p_id_offre.';

create function offre_est_ouverte (p_id_offre int, p_le timestamp) returns boolean as $$
    select p_id_offre in (select id_offre from _horaire_ouverture)
       and p_id_offre in (select id_offre from _periode_ouverture)
        or p_id_offre in (select id_offre from _periode_ouverture
                           where debut_le <= p_le and p_le < fin_le)
        or p_id_offre in (select id_offre from _horaire_ouverture
                           where dow = extract(dow from p_le)
                             and heure_debut <= p_le::time
                             and p_le::time < heure_fin);
$$ language sql;
comment on function offre_est_ouverte (int, timestamp) is
'Une offre est-elle ouverte à un timestamp donné ?
@param p_id_offre l''ID de l''offre
@param p_le le timestamp à tester
@returns L''ouverte est ouverte le @p p_le.';

create function offre_changement_ouverture_suivant_le (p_id_offre int, p_apres_le timestamp) returns timestamp as $$
select least(
    (select date_trunc('day', p_apres_le)
        + dans_jours * interval '1 day'
        + heure
    from (select rmod(dow - extract(dow from p_apres_le), 7) dans_jours, heure from (
        select dow, heure_debut heure
        from _horaire_ouverture
        where id_offre = p_id_offre
        union all
        select dow, heure_fin
        from _horaire_ouverture
        where id_offre = p_id_offre
    ) c) c
    where dans_jours <> 0 or heure > p_apres_le::time
    order by dans_jours
    limit 1),
    (select * from (
        select debut_le le from _periode_ouverture
        where id_offre = p_id_offre
        and debut_le > p_apres_le
        union all
        select fin_le from _periode_ouverture
        where id_offre = p_id_offre
        and fin_le > p_apres_le
    ) changements_ouverture_periode
    order by le
    limit 1)
);
$$ language sql;
comment on function offre_changement_ouverture_suivant_le (int, timestamp) is
'Retourne un timestamp indiquant quand a lieu le prochain changement d''ouverture d''une offre après une date.
@param p_id_offre l''ID de l''offre
@param p_apres_le le timpestamp auquel la valeur de retour doit être postérieur.
@returns Le premier changement d''ouverture de l''offre d''ID @p p_id_offre postérieur à @p p_apres_le, ou `null` si aucun changement d''ouverture n''a lieu après le timsetamp spécifé

Prend uniquement en compte les changement d''ouverture strictement postérieurs à @p p_apres_le.
Ainsi, `offre_changement_ouverture_suivant_le(5, ''2024-11-20'') < offre_changement_ouverture_suivant_le(5, offre_changement_ouverture_suivant_le(5, ''2024-11-20''))`
On ne peut pas utiliser une vue pour les changements d''ouverture car il y en ait une infinité (puisque les horaires se répentent chaque semaine)';


create function offre_en_ligne_pendant (
    p_id_offre int,
    p_debut timestamp,
    p_duree interval
) returns interval as $$
declare
    fait_le timestamp;
    derniere_mise_en_ligne timestamp;
    en_ligne boolean not null = false;
    en_ligne_pendant interval not null = '0';
    fin constant timestamp not null = p_debut + p_duree;
    -- le premier changement d'état représente toujours la création.
    creee_le constant timestamp not null = c.fait_le from _changement_etat c where id_offre = p_id_offre order by fait_le limit 1;
begin
    if p_duree <= interval '0' then
        raise 'La durée doit être positive';
    end if;

    for fait_le in
        select c.fait_le from _changement_etat c where id_offre = p_id_offre order by fait_le offset 1
    loop
        en_ligne = not en_ligne;

        if fin <= fait_le then
            exit;
        elseif p_debut <= fait_le then
            if en_ligne then -- mise en ligne
                -- donc l'offre a été en hors-ligne depuis p_dpebut
                derniere_mise_en_ligne = fait_le;
            else -- mise hors-ligne
                -- donc l'offre a été en ligne depuis p_debut
                en_ligne_pendant = en_ligne_pendant + (fait_le - coalesce(derniere_mise_en_ligne, greatest(p_debut, creee_le)));
            end if;
        end if;
    end loop;

    if en_ligne then
        en_ligne_pendant = en_ligne_pendant + (fin - coalesce(derniere_mise_en_ligne, greatest(p_debut, creee_le)));
    end if;

    return en_ligne_pendant;
end;
$$ language plpgsql;
comment on function offre_en_ligne_pendant (int, timestamp, interval) is
'Retourne la durée pendant laquelle un offre a été en ligne sur une période donnée.
@param p_id_offre l''ID de l''offre
@param p_debut début de la période d''observation
@param p_duree durée de la période d''observation
@returns La valeur de retour est inférieure ou égale à @p p_duree';
