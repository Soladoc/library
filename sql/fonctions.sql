begin;

set schema 'pact';

set plpgsql.extra_errors to 'all';

create function offre_categorie (id_offre int) returns ligne as $$
begin
    if id_offre in (select id from pact._restaurant) then return 'restaurant'; end if;
    if id_offre in (select id from pact._activite) then return 'activité'; end if;
    if id_offre in (select id from pact._visite) then return 'visite'; end if;
    if id_offre in (select id from pact._spectacle) then return 'spectacle'; end if;
    if id_offre in (select id from pact._parc_attractions) then return 'parc d''attractions'; end if;
    raise 'incohérence: offre non catégorisée';
end;
$$ language plpgsql;
comment on function offre_categorie (int) is 'Retourne une ligne nommant la catégorie d''une offre.';

/*create function offre_est_ouverte (id_offre int, le timestamp) returns boolean as $$
begin

end;
$$ language plpgsql;
comment on function offre_est_ouverte (int, timestamp) is 'Retourne un booléen indiquant si une offre est ouverte à un moment donné.';

create function offre_changement_ouverture_suivant_le (id_offre int, apres_le timestamp) returns timestamp as $$
begin

end;
$$ language plpgsql;
comment on function offre_changement_ouverture_suivant_le (int, timestamp) is 'Retourne un timestamp indiquant quand a lieu le prochain changement d''ouverture d''une offre après une date.
Prend uniquement en compte les changement d''ouverture strictement postérieurs à `apres_le`.
Ainsi, `offre_changement_ouverture_suivant_le(5, ''2024-11-20'') < offre_changement_ouverture_suivant_le(5, offre_changement_ouverture_suivant_le(5, ''2024-11-20''))`
';

create function offre_duree_en_ligne (
    id_offre int,
    year int,
    month int
) returns ligne as $$
declare
    en_ligne boolean = false
begin
    -- select first to online timestamp in the month
    -- select last to offline timestamp in the month

    for c in select * from _changement_etat where id_offre = loop

    end loop;
end;
$$ language plpgsql;
comment on function offre_duree_en_ligne (int, int, int) is 'Retourne la durée pendant laquelle un offre a été en ligne pendant un mois.';*/

commit;