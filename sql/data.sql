begin;

set schema 'pact';

insert into
    _abonnement (libelle, prix)
values
    ('gratuit', 0),
    ('standard', 5),
    ('premium', 10);

insert into
    _option (nom, prix)
values
    ('À la Une', 20),
    ('En Relief', 30);

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, nom_voie)
        values
            ('22', 136, 'Rue du Mène')
        returning
            id
    )
insert into
    pro_prive (id_adresse, siren, denomination, email, mdp_hash, nom, prenom, telephone)
values
    (
        (table id_adresse),
        '123456789',
        'MERTREM Solutions',
        'contact@mertrem.org',
        '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike', -- toto
        'Dephric',
        'Max',
        '0288776655'
    );

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, nom_voie)
        values
            ('22', 154, 'Les Tronchées')
        returning
            id
    )
insert into
    pro_public (id_adresse, denomination, email, mdp_hash, nom, prenom, telephone)
values
    (
        (table id_adresse),
        'Commune de Thiercelieux',
        'thiercelieux.commune@voila.fr',
        '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike', -- toto
        'Fonct',
        'Ionnaire',
        '1122334455'
    );

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, localite)
        values
            ('22', 201, 'Cap Fréhel')
        returning
            id
    )
insert into
    membre (id_adresse, pseudo, email, mdp_hash, nom, prenom, telephone)
values
    (
        (table id_adresse),
        '5cover',
        'the.scover@gmail.co',
        /*toto*/
        '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike', -- toto
        'Scover',
        'NoLastName',
        '2134657980'
    );

commit;