begin;

set schema 'pact';

-- todo: avis_resto with computed attr id_restaurant (based on )
-- todo: insert into tarif: assert that 'gratuit' = (select libelle_abonnement from _offre o where o.id_offre = id_offre)
-- todo: trigger timestamp offre lmt
-- toto: non-instanciation classe abstraite
-- todo: normalization periodes ouvertures (contrainte pour ne pas avoir de range overlapping -- agrandir les ranges existants dans un trigger) ce sera intéréssant à coder

create view offres as select
    *,
    (select count(*) from _changement_etat where _changement_etat.id_offre = _offre.id) % 2 = 0 en_ligne,
    (select avg(_avis.note) from _avis where _avis.id_offre = _offre.id) note_moyenne,
    (select offre_categorie(id)) categorie
from
    _offre;

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

create view avis as select _avis.*, pseudo from _avis
join membre on id_membre_auteur=membre.id;

commit;