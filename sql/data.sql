set schema 'pact';

insert into _abonnement(libelle, prix)
    values ('gratuit', 0),
('standard', 5), -- ébauche
('premium', 10);

-- ébauche
insert into _signalable default values;

insert into _identite(id_identite)
    values (1);

insert into _adresse(id_adresse, nom_voie, commune_code_insee, commune_code_postal)
    values (1, 'aaaaaaaaaaaaaaaaaaa', '1001', '1400');

insert into _compte(id_identite, email, mdp_hash, nom, prenom, telephone)
    values (1, 'a.gmail', 'hashça', 'Abraham', 'Lincoln', '0123456789');

insert into _professionnel(id_professionnel, denomination, email)
    values (1, 'MERTREM Solutions', 'a.gmail');

insert into _image(id_image, legende, taille)
    values (123, 'legende', 100);

insert into _offre(titre, resume, description_detaille, url_site_web, adresse, id_signalable, id_professionnel, photoprincipale)
    values ('barraque à frites', 'aaaaaaaaaaa', 'cest une barraque à frite', 'blabla.fr', 1, 1, 1, 123);

