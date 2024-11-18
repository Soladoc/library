begin;

set schema 'pact';

-- todo: avis_resto with computed attr id_restaurant (based on )
-- todo: insert into tarif: assert that 'gratuit' = (select libelle_abonnement from _offre o where o.id_offre = id_offre)
-- todo: trigger timestamp offre lmt
-- toto: non-instanciation classe abstraite

create view offres as select
    *,
    (select count(*) from _changement_etat where id_offre = id) % 2 = 0 en_ligne,
    (select avg(note) from _avis a where a.id_offre = id) note_moyenne,
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

CREATE VIEW avis as SELECT * FROM _avis
JOIN membre on id_membre_auteur=membre.id as id_membre_auteur;

commit;