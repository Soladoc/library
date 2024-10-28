begin;

set schema 'pact';

insert into
    _abonnement (libelle, prix)
values
    ('gratuit', 0),
    ('standard', 5),
    ('premium', 10);

insert into
    _image (taille, mime_subtype, legende)
values
    (250244, 'webp', '010717 aquarev 045'),
    (125156, 'jpeg', '1200x680_grenouille'),
    (320890, 'webp', 'acro'),
    (264270, 'webp', 'antenne_pb1_c_cite_des_telecoms'),
    (8370, 'jpeg', 'arche du temps'),
    (28511, 'jpeg', 'cap-frehel-lighthouse'),
    (80529, 'jpeg', 'cdt_vueext_tdrone-141_md1-800x600'),
    (45395, 'jpeg', 'celtique tour'),
    (45504, 'jpeg', 'char-a-voile'),
    (216628, 'webp', 'chimair_cite-des-telecoms-82-scaled'),
    (65546, 'webp', 'Cite_des_telecoms_Parc_du_Radome_Pleumeur_Bodou_9'),
    (136726, 'jpeg', 'creperie de l''abbaye de beauport'),
    (348163, 'jpeg', 'creperie les alize'),
    (134354, 'jpeg', 'facade-cite-telecoms'),
    (278752, 'jpeg', 'fort-la-latte'),
    (112259, 'jpeg', 'laser game'),
    (165607, 'jpeg', 'le-de-de-tregastel'),
    (273188, 'jpeg', 'les-oeuvres'),
    (301557, 'jpeg', 'loisirs_parc_du_radome_cite_des_telecoms_vue_aerienne_alex_kozel'),
    (292245, 'jpeg', 'maison-d-ernest-renan'),
    (76107, 'jpeg', 'parc-indian-forest-morieux'),
    (47305, 'jpeg', 'poesie'),
    (252164, 'jpeg', 'reserve-naturelle-des'),
    (258810, 'jpeg', 'st-brieuc-museum-front'),
    (141400, 'webp', 'vallee-des-saints');

insert into
    pro_prive (siren, denomination, email, mdp_hash, nom, prenom, telephone)
values
    ('123456789', 'MERTREM Solutions', 'contact@mertrem.org', /*toto*/ '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike', 'Dephric', 'Max', '0288776655');

insert into
    pro_public (denomination, email, mdp_hash, nom, prenom, telephone)
values
    ('Commune de Thiercelieux', 'thiercelieux.commune@voila.fr', /*toto*/ '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike', 'Fonct', 'Ionnaire', '1122334455');

insert into
    membre (pseudo, email, mdp_hash, nom, prenom, telephone)
values
    ('5cover', 'the.scover@gmail.co', /*toto*/ '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike', 'Scover', 'NoLastName', '2134657980');

with id_adresse as (
    insert into
        _adresse (code_insee_commune, numero_voie, nom_voie)
    values
        ('22278', 18, 'Rue de l''Église')
    returning id
)
insert into activite (
    id_professionnel, id_image_principale, id_adresse, libelle_abonnement, url_site_web, titre, resume, description_detaillee,
    indication_duree, age_requis, prestations_incluses, prestations_non_incluses
) values (
    1,
    5,
    (table id_adresse),
    'gratuit',
    'https://larchedutemps.com',
    'Escape game À l''Arche du Temps',
    'L''Escape Game Arche Du Temps vous permet de disposer d''un cadre atypique, avec une possibilité de privatisation de l''espace d''accueil (80m²) avec salon modulable, et connexion wifi pour un séminaire, une réunion ou autre.',
    '# Escape Game

## Le Gardien des Reliques

1148 – A la veille de la cérémonie d’inauguration de la Cathédrale, Frère Thomas est retrouvé assassiné dans sa chapelle !

Vous et votre équipe aurez une heure pour retrouver le coupable, son mobile et surtout le trésor qu’il protégeait : les prestigieuses reliques de Saint Lazare.

Dans le décor authentique de la Chapelle des VII Dormants, cet Escape Game vous plonge dans le passé millénaire d’Autun. Cherchez les indices, combinez les pistes et résolvez le mystère du Gardien des Reliques !

De 3 à 6 joueurs, en famille, entre collègues ou entre amis, venez relever nos défis ! Au total, le briefing et l’enquête sont prévus pour une durée d’environ 1h30.',
    '1:30:',
    16,
    'Nous vous proposons un escape game avec énigmes',
    'Nous ne vous proposons pas de repas'
);

commit;