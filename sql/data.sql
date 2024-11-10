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
    (65546, 'webp', 'Cite_des_telecoms_Parc_du_Radome_Pleumeur_Bodou_9'), -- 2
    (125156, 'jpeg', '1200x680_grenouille'), -- 3
    (8370, 'jpeg', 'arche du temps'), -- 4
    (28511, 'jpeg', 'cap-frehel-lighthouse'), -- 5
    (264270, 'webp', 'antenne_pb1_c_cite_des_telecoms'), -- 6
    (45504, 'jpeg', 'char-a-voile'), -- 7
    (136726, 'jpeg', 'creperie de l''abbaye de beauport'), -- 8
    (320890, 'webp', 'acro lantic'), -- 9
    (80529, 'jpeg', 'cdt_vueext_tdrone-141_md1-800x600'), -- 10
    (216628, 'webp', 'chimair_cite-des-telecoms-82-scaled'), -- 11
    (45395, 'jpeg', 'celtique tour'), -- 12
    (348163, 'jpeg', 'creperie les alize'), -- 13
    (134354, 'jpeg', 'facade-cite-telecoms'), -- 14
    (292245, 'jpeg', 'maison-d-ernest-renan'), -- 15
    (301557, 'jpeg', 'loisirs_parc_du_radome_cite_des_telecoms_vue_aerienne_alex_kozel'), -- 16
    (76107, 'jpeg', 'parc-indian-forest-morieux'), -- 17
    (273188, 'jpeg', 'les-oeuvres'), -- 18
    (112259, 'jpeg', 'laser game'), -- 19
    (278752, 'jpeg', 'fort-la-latte'), -- 20
    (165607, 'jpeg', 'le-de-de-tregastel'), -- 21
    (47305, 'jpeg', 'poesie'), -- 22
    (141400, 'webp', 'vallee-des-saints'), -- 23
    (252164, 'jpeg', 'reserve-naturelle-des'), -- 24
    (258810, 'jpeg', 'st-brieuc-museum-front'), -- 25
    (422847, 'png', 'lasergame-lasergame'), -- 26
    (489219, 'jpeg', 'lasergame-quiz'), -- 27
    (423934, 'jpeg', 'lasergame-blind-test'), -- 28
    (471851, 'jpeg', 'lasergame-flechettes'), -- 29
    (130733, 'png', 'eclipse-lasergame-2'), -- 30
    (127583, 'png', 'eclipse-lasergame-1'), -- 31
    (151012, 'png', 'eclipse-lasergame-0'), -- 32
    (344377, 'jpeg', 'eclipse-jeu-0'), -- 33
    (496307, 'jpeg', 'eclipse-jeu-1'), -- 34
    (381415, 'jpeg', 'eclipse-bar'), -- 35
    (4887188, 'png', 'eclipse-bowling'); -- 36

commit;