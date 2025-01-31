set schema 'pact';

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
    pro_prive (
        id_adresse,
        siren,
        denomination,
        email,
        mdp_hash,
        nom,
        prenom,
        telephone,
        api_key
    )
values
    (
        (table id_adresse),
        '111000111',
        'pro1',
        'pro@1.413',
        '$2y$10$.ZvKuA57PFTphxqg/qEXu.0ksxy8lTBTcTDyRyPt4wmY19PejNPG6', -- pro1_mdp
        'pro1_nom',
        'pro1_prenom',
        '1110001110',
        'bb1b5a1f-a482-4858-8c6b-f4746481cffa'
    ),
    (
        (table id_adresse),
        '333000333',
        'pro3',
        'pro@3.413',
        '$2y$10$nK37RHTYwCc/URixHdUq.eO1PVWtf5miCvS7DUbq4FxNWsTmZ4pbm', -- pro3_mdp
        'pro3_nom',
        'pro3_prenom',
        '3330003330',
        null
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
    pro_public (id_adresse, denomination, email, mdp_hash, nom, prenom, telephone, api_key)
values
    (
        (table id_adresse),
        'pro2',
        'pro@2.413',
        '$2y$10$ulUDhEqbxAqYOakJqDTeTO033nfoHKmiaAEwa0C2WUEgH.CxjyRgW', -- pro2_mdp
        'pro2_nom',
        'pro2_prenom',
        '2220002220',
        '52d43379-8f75-4fbd-8b06-d80a87b2c2b4'
    ),
    (
        (table id_adresse),
        'pro4',
        'pro@4.413',
        '$2y$10$CtrXMYZ.JAO5BN22CKH1ru147YT8sGboZYwyiHo3/4Wa6gOwn6Qha', -- pro4_mdp
        'pro4_nom',
        'pro4_prenom',
        '4440002220',
        null
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
    membre (id_adresse, pseudo, email, mdp_hash, nom, prenom, telephone, api_key)
values
    (
        (table id_adresse),
        'membre1',
        'membre@1.413',
        '$2y$10$mBh2EAxVYnkdAdh/MAVMsuvI2UdT8IocJY2Zu.nstcAJojU6ZJR2W', -- membre1_mdp
        'membre1_nom',
        'membre1_prenom',
        '0001110001',
        '123e4567-e89b-12d3-a456-426614174000'
    ),
    (
        (table id_adresse),
        'membre2',
        'membre@2.413',
        '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike', -- membre2_mdp
        'membre2_nom',
        'membre2_prenom',
        '0002220002',
        '9ea59c5b-bb75-4cc9-8f80-77b4ce851a0b'
    ),
    (
        (table id_adresse),
        'membre3',
        'membre@3.413',
        '$2y$10$zM8bQW3VXaY/wGhgsXMVfumzqgf4T2hYZLJF8m9IDnHSFEXeGqFh2', -- membre3_mdp
        'membre3_nom',
        'membre3_prenom',
        '0003330003',
        null
    );