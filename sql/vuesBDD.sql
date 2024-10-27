begin;

set schema 'pact';

-- todo: avis_resto with computed attr id_restaurant (based on )
-- todo: insert into tarif: assert that 'gratuit' = (select libelle_abonnement from _offre o where o.id_offre = id_offre)
-- todo: trigger timestamp offre lmt

create or replace view avis as table _avis;

create or replace function offre_categorie(id_offre int)
    returns ligne
    as $$
begin
    if id_offre in (select id_offre from _restaurant) then return 'restaurant'; end if;
    if id_offre in (select id_offre from _activite) then return 'activité'; end if;
    if id_offre in (select id_offre from _visite) then return 'visite'; end if;
    if id_offre in (select id_offre from _spectacle) then return 'spectacle'; end if;
    if id_offre in (select id_offre from _parc_attractions) then return 'parc d''attractions'; end if;
    raise 'incohérence: ffre non catégorisée';
end;
$$
language 'plpgsql';

create or replace view offres as
select
    *,
    (select count(*) from _changement_etat c where c.id_offre = id_offre) % 2 = 0 en_ligne,
    (select avg(note) from _avis a where a.id_offre = id_offre) note_moyenne,
    (select offre_categorie(id_offre)) categorie
from
    _offre;

create or replace view activite as
select
    *
from
    _activite
    join offres on id_offre = id_activite;

create or replace view spectacle as
select
    *
from
    _spectacle
    join offres on id_offre = id_spectacle;

create or replace view visite as
select
    *
from
    _visite
    join offres on id_offre = id_visite;

create or replace view parc_attractions as
select
    *
from
    _parc_attractions
    join offres on id_offre = id_parc_attractions;

create or replace view restaurant as
select
    *
from
    _restaurant
    join offres on id_offre = id_restaurant;

create or replace view membres as
select
    -- Membre
    id_membre,
    pseudo,
    -- Compte
    id_signalable,
    email,
    mdp_hash,
    nom,
    prenom,
    telephone
from
    _membre
    join _compte on id_compte = id_membre
where
    existe;

create or replace view pro_prive as
select
    -- Privé
    id_prive,
    siren,
    -- Professionnel
    denomination,
    -- Compte
    id_signalable,
    email,
    mdp_hash,
    nom,
    prenom,
    telephone
from
    _prive
    join _professionnel on id_professionnel = id_prive
    join _compte on id_compte = id_prive;

create or replace view pro_public as
select
    -- Public
    id_public,
    -- Professionnel
    denomination,
    -- Compte
    id_signalable,
    email,
    mdp_hash,
    nom,
    prenom,
    telephone
from
    _public
    join _professionnel on id_professionnel = id_public
    join _compte on id_compte = id_public;

create or replace view tous_comptes_pro as
select
    -- Professionnel
    id_professionnel,
    denomination,
    -- Compte
    id_signalable,
    email,
    mdp_hash,
    nom,
    prenom,
    telephone,
    existe
from
    _professionnel
    join _compte on id_compte = id_professionnel;

commit;