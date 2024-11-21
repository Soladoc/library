begin;

set schema 'pact';

set plpgsql.extra_errors to 'all';

create function offre_categorie (id_offre int) returns mot as $$
begin
    if id_offre in (select id from pact._restaurant) then return 'restaurant'; end if;
    if id_offre in (select id from pact._activite) then return 'activité'; end if;
    if id_offre in (select id from pact._visite) then return 'visite'; end if;
    if id_offre in (select id from pact._spectacle) then return 'spectacle'; end if;
    if id_offre in (select id from pact._parc_attractions) then return 'parc d''attractions'; end if;
    raise 'incohérence: offre non catégorisée';
end;
$$ language plpgsql;
comment on function offre_categorie (int) is
'Retourne la catégorie d''une offre.
@param id_offre l''ID de l''offre
@returns La catégorie de l''offre d''ID `id_offre`.';


create function offre_est_ouverte (id_offre int, le timestamp) returns boolean as $$
begin
    return exists (select from horaire_ouverture h
        where h.id_offre = id_offre
          and dow = extract(dow from le)
          and le::time >= heure_debut
          and le::time < heure_fin);
    return true;
end;
$$ language plpgsql;
comment on function offre_est_ouverte (int, timestamp) is
'Une offre est elle ouverte à un timestamp donné ?
@param id_offre l''ID de l''offre
@param le le timestamp à tester
@returns L''ouverte est ouverte le `le`.';

/*create function offre_changement_ouverture_suivant_le (id_offre int, apres_le timestamp) returns timestamp as $$
begin

end;
$$ language plpgsql;
comment on function offre_changement_ouverture_suivant_le (int, timestamp) is
'Retourne un timestamp indiquant quand a lieu le prochain changement d''ouverture d''une offre après une date.
Prend uniquement en compte les changement d''ouverture strictement postérieurs à `apres_le`.
Ainsi, `offre_changement_ouverture_suivant_le(5, ''2024-11-20'') < offre_changement_ouverture_suivant_le(5, offre_changement_ouverture_suivant_le(5, ''2024-11-20''))`
@param id_offre l''ID de l''offre
@param apres_le le timpestamp auquel la valeur de retour doit être postérieur
@return Le prochain changement d''ouverture de l''offre d''ID `id_offre` après `apres_le`';
*/

create function offre_duree_en_ligne (
    p_id_offre int,
    debut timestamp,
    duree interval
) returns interval as $$
declare
    fait_le timestamp;
    derniere_mise_en_ligne timestamp;
    en_ligne boolean not null = false;
    duree_en_ligne interval not null = '0';
    fin constant timestamp not null = debut + duree;
    -- le premier changement d'état représente toujours la création.
    creee_le constant timestamp not null = c.fait_le from _changement_etat c where id_offre = p_id_offre order by fait_le limit 1;
begin
    if duree <= interval '0' then
        raise 'La durée doit être positive';
    end if;

    for fait_le in
        select c.fait_le from _changement_etat c where id_offre = p_id_offre order by fait_le offset 1
    loop
        en_ligne = not en_ligne;

        if fin <= fait_le then
            exit;
        elseif debut <= fait_le then
            if en_ligne then -- mise en ligne
                -- donc l'offre a été en hors-ligne depuis début
                derniere_mise_en_ligne = fait_le;
            else -- mise hors-ligne
                -- donc l'offre a été en ligne depuis debut
                duree_en_ligne = duree_en_ligne + (fait_le - coalesce(derniere_mise_en_ligne, greatest(debut, creee_le)));
            end if;
        end if;
    end loop;

    if en_ligne then
        duree_en_ligne = duree_en_ligne + (fin - coalesce(derniere_mise_en_ligne, greatest(debut, creee_le)));
    end if;

    return duree_en_ligne;
end;
$$ language plpgsql;
comment on function offre_duree_en_ligne (int, timestamp, interval) is
'Retourne la durée pendant laquelle un offre a été en ligne sur une période donnée.
@param p_id_offre l''ID de l''offre
@param debut début de la période d''observation
@param duree durée de la période d''observation
@returns La valeur de retour est inférieure ou égale à `duree`';

commit;