begin;

set schema 'pact';

create view offres as select
    *,
    (select count(*) from _changement_etat where _changement_etat.id_offre = _offre.id) % 2 = 0 en_ligne,
    (select avg(_avis.note) from _avis where _avis.id_offre = _offre.id) note_moyenne,
    (select min(_tarif.montant) from _tarif where _tarif.id_offre = _offre.id) prix_min,
    (select fait_le from _changement_etat where _changement_etat.id_offre = _offre.id order by fait_le limit 1) creee_le,
    offre_categorie(id) categorie,
    --offre_est_ouverte(id, localtimestamp) est_ouverte,
    offre_en_ligne_pendant(id, date_trunc('month', localtimestamp), '1 month') en_ligne_ce_mois_pendant/*,
    offre_changement_ouverture_suivant_le(id, localtimestamp) changement_ouverture_suivant_le*/
from
    _offre;
/*
comment on column offres.est_ouverte is
'Un booléen indiquant si cette offre est actuellement ouverte';
comment on column offres.changement_ouverture_suivant_le is
'Un timestamp indiquant quand aura lieu le prochain changement d''ouverture.
Si l''offre est fermée, c''est la prochaine ouverture, ou infinity si l''offre sera fermée pour toujours.
Si l''offre est ouverte, c''est la prochaine fermeture, ou infinity si l''offre sera ouverte pour toujours.';*/
comment on column offres.en_ligne_ce_mois_pendant is
'La durée pendant laquelle cette offre a été en ligne pour le mois courant. La valeur est inférieure ou égale à 1 mois.'


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

create view professionnel as select * from _professionnel
    join _compte using (id);

create view pro_prive as select * from _prive
    join professionnel using (id);

create view pro_public as select * from _public
    join professionnel using (id);

create view avis as select
    _avis.*,
    membre.pseudo pseudo_auteur
from
    _avis
    join membre on id_membre_auteur = membre.id;

create view horaire_ouverture as table _horaire_ouverture;
create view periode_ouverture as table _periode_ouverture;

commit;