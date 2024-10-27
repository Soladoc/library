begin;

set schema 'pact';

insert into
    _abonnement(libelle_abonnement, prix)
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
    ('Thiercelieux', 'thiercelieux.commune@voila.fr', /*toto*/ '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike', 'Fonct', 'Ionnaire', '1122334455');

insert into
    _image(taille, mime_type, legende)
values
    (125156, 'jpeg', '1200x680_grenouille'),
    (776892, 'webp', '637cf1668f4302361f300639'),
    (45504, 'jpeg', 'ABW2BFCVCMKBAVPE4BRRBYWZQ4'),
    (784734, 'jpeg', 'antenne_pb1_c_cite_des_telecoms'),
    (28511, 'jpeg', 'cap-frehel-lighthouse'),
    (80529, 'jpeg', 'cdt_vueext_tdrone-141_md1-800x600'),
    (1454608, 'jpeg', 'chimair_cite-des-telecoms-82-scaled'),
    (884208, 'jpeg', 'Cite_des_telecoms_Parc_du_Radome_Pleumeur_Bodou_9'),
    (134354, 'jpeg', 'facade-cite-telecoms'),
    (278752, 'jpeg', 'fort-la-latte'),
    (165607, 'jpeg', 'le-de-de-tregastel'),
    (273188, 'jpeg', 'les-oeuvres'),
    (301557, 'jpeg', 'loisirs_parc_du_radome_cite_des_telecoms_vue_aerienne_alex_kozel'),
    (292245, 'jpeg', 'maison-d-ernest-renan'),
    (76107, 'jpeg', 'parc-indian-forest-morieux'),
    (252164, 'jpeg', 'reserve-naturelle-des'),
    (258810, 'jpeg', 'st-brieuc-museum-front');

insert into _offre(titre, resume, description_detaillee, url_site_web, adresse, id_signalable, id_professionnel, id_image_principale)
        values ('barraque à frites', 'Des frites. C''est bon les frites', 'cest une barraque à frite', 'blabla.fr',(table adresse),
(table signalable),
(table professionnel), 1)
    returning
        id_offre)
    insert into _gallerie(id_offre, id_image)
        values ((table offre), 2),
((table offre), 3);

commit;