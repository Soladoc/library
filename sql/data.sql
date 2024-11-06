begin;

set schema 'pact';

insert into
    _abonnement (libelle, prix)
values
    ('gratuit', 0),
    ('standard', 5),
    ('premium', 10);

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

insert into
    _image(taille, mime_subtype, legende)
values
    (250244, 'webp', '010717 aquarev 045'), -- 1
    (125156, 'jpeg', '1200x680_grenouille'), -- 2
    (320890, 'webp', 'acro'), -- 3
    (264270, 'webp', 'antenne_pb1_c_cite_des_telecoms'), -- 4
    (8370, 'jpeg', 'arche du temps'), -- 5
    (28511, 'jpeg', 'cap-frehel-lighthouse'), -- 6
    (80529, 'jpeg', 'cdt_vueext_tdrone-141_md1-800x600'), -- 7
    (45395, 'jpeg', 'celtique tour'), -- 8
    (45504, 'jpeg', 'char-a-voile'), -- 9
    (216628, 'webp', 'chimair_cite-des-telecoms-82-scaled'), -- 10
    (65546, 'webp', 'Cite_des_telecoms_Parc_du_Radome_Pleumeur_Bodou_9'), -- 11
    (136726, 'jpeg', 'creperie de l''abbaye de beauport'), -- 12
    (348163, 'jpeg', 'creperie les alize'), -- 13
    (134354, 'jpeg', 'facade-cite-telecoms'), -- 14
    (278752, 'jpeg', 'fort-la-latte'), -- 15
    (423934, 'jpeg', 'lasergame-blind-test'), -- 16
    (471851, 'jpeg', 'lasergame-flechettes'), -- 17
    (112259, 'jpeg', 'laser game'), -- 18
    (422847, 'png', 'lasergame-lasergame'), -- 19
    (489219, 'jpeg', 'lasergame-quiz'), -- 20
    (165607, 'jpeg', 'le-de-de-tregastel'), -- 21
    (273188, 'jpeg', 'les-oeuvres'), -- 22
    (301557, 'jpeg', 'loisirs_parc_du_radome_cite_des_telecoms_vue_aerienne_alex_kozel'), -- 23
    (292245, 'jpeg', 'maison-d-ernest-renan'), -- 24
    (76107, 'jpeg', 'parc-indian-forest-morieux'), -- 25
    (47305, 'jpeg', 'poesie'), -- 26
    (252164, 'jpeg', 'reserve-naturelle-des'), -- 27
    (258810, 'jpeg', 'st-brieuc-museum-front'), -- 28
    (141400, 'webp', 'vallee-des-saints'); -- 29

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

with id_adresse as (
    insert into _adresse
        (code_insee_commune, nom_voie)
    values
        ('22360', 'Zone de loisirs Brezillet ouest')
    returning id
), id_offre as (
    insert into activite (
        id_professionnel, id_image_principale, id_adresse, libelle_abonnement, url_site_web, titre, resume, description_detaillee,
        indication_duree, age_requis, prestations_incluses, prestations_non_incluses
    ) values (
        1,
        18,
        (table id_adresse),
        'gratuit',
        'https://saint-brieuc.lasergame-evolution.fr/',
        'Laser Game Evolution Saint-Brieuc',
        'Bienvenue au Laser Game Evolution de Saint-Brieuc ! Réservation conseillée - Vous pouvez jouer à partir de 4 joueurs et à partir de 7 ans minimum.',
        'Equipé de votre PISTOLET 100% LASER, votre précision et votre rapidité sont vos atouts pour surpasser vos adversaires dans nos labyrinthes ! Utilisez les planches pour vous cacher, mais méfiez-vous des meurtrières et des miroirs ! Pour un anniversaire, avec vos amis ou encore vos collaborateurs, venez jouer sur réservation, car nos labyrinthes vous seront entièrement privatisés ! Une équipe dynamique sera à votre disposition pour vous faire passer un moment inoubliable ! Laser Game Evolution, LE JEU LASER 100 % !',
        '0:20:',
        7,
        'Nous vous proposons un laser game, un quiz game, un blindtest, des fléchettes',
        'Nous ne vous proposons pas de repas'
    ) returning id
)
insert into _gallerie
    (id_offre, id_image)
values
    ((table id_offre), 16),
    ((table id_offre), 17),
    ((table id_offre), 19),
    ((table id_offre), 20);

commit;