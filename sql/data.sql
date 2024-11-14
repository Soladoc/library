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
    (
        '123456789',
        'MERTREM Solutions',
        'contact@mertrem.org',
        /*toto*/ '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike',
        'Dephric',
        'Max',
        '0288776655'
    );

insert into
    pro_public (denomination, email, mdp_hash, nom, prenom, telephone)
values
    (
        'Commune de Thiercelieux',
        'thiercelieux.commune@voila.fr',
        /*toto*/ '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike',
        'Fonct',
        'Ionnaire',
        '1122334455'
    );

insert into
    membre (pseudo, email, mdp_hash, nom, prenom, telephone)
values
    (
        '5cover',
        'the.scover@gmail.co',
        /*toto*/ '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike',
        'Scover',
        'NoLastName',
        '2134657980'
    );

commit;