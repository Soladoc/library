set schema 'pact';

create view tarif as table _tarif;

create view offres as select
    *,
    (select count(*) from _changement_etat where _changement_etat.id_offre = _offre.id) % 2 = 0 en_ligne,
    (select round(avg(_avis.note),2) from _avis where _avis.id_offre = _offre.id) note_moyenne,
    (select min(tarif.montant) from tarif where tarif.id_offre = _offre.id) prix_min,
    (select count(*) from _avis where id_offre = id) nb_avis,
    offre_creee_le(id) creee_le,
    offre_categorie(id) categorie,
    offre_en_ligne_pendant(id, date_trunc('month', localtimestamp), '1 month') en_ligne_ce_mois_pendant,
    offre_changement_ouverture_suivant_le(id, localtimestamp, periodes_ouverture) changement_ouverture_suivant_le,
    -- Considérer une offre sans période ou horaire comme ouverte tout le temps
    (with horaire_match as (
        select horaires from _ouverture_hebdomadaire
         where id_offre = id
           and dow = extract(dow from localtimestamp))
     select isempty(periodes_ouverture) and not exists((table horaire_match))
         or localtimestamp <@ periodes_ouverture
         or coalesce(localtime <@ (table horaire_match), false)) est_ouverte 
from
    _offre;

comment on column offres.est_ouverte is
'Un booléen indiquant si cette offre est actuellement ouverte';
comment on column offres.changement_ouverture_suivant_le is
'Un timestamp indiquant quand aura lieu le prochain changement d''ouverture.
Si l''offre est fermée, c''est la prochaine ouverture, ou infinity si l''offre sera fermée pour toujours.
Si l''offre est ouverte, c''est la prochaine fermeture, ou infinity si l''offre sera ouverte pour toujours.';
comment on column offres.en_ligne_ce_mois_pendant is
'La durée pendant laquelle cette offre a été en ligne pour le mois courant. La valeur est inférieure ou égale à 1 mois.';

create view activite as select * from _activite
    join offres using (id);

create view spectacle as select * from _spectacle
    join offres using (id);

create view visite as select * from _visite
    join offres using (id);

create view parc_attractions as select * from _parc_attractions
    join offres using (id);

create view restaurant as select * from _restaurant
    join offres using (id);

create view membre as select * from _membre
    join _compte using (id);

create view
    professionnel as
select
    *,
    professionnel_secteur(id) secteur
from
    _professionnel
    join _compte using (id);

create view pro_prive as select * from _prive
    join professionnel using (id);

create view pro_public as select * from _public
    join professionnel using (id);

create view avis as select
    membre.pseudo pseudo_auteur,
    _avis.*
from
    _avis
    join membre on id_membre_auteur = membre.id;

create view avis_restaurant as select
    membre.pseudo pseudo_auteur,
    commentaire,
    note,
    publie_le,
    date_experience,
    contexte,
    lu,
    blackliste,
    id_membre_auteur,
    id_offre id_restaurant,
    _avis_restaurant.*
from
    _avis_restaurant
    join _avis using (id)
    join membre on id_membre_auteur = membre.id;
