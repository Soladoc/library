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
        '$2y$10$cNmRElBKaejyb6ziQ3Xu/ewVP.D9/mDgYx2rqHmH8gp6RP68Qve/O', -- pro1_mdp
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
        '$2y$10$pYIdXCakAXF0OwC8cZY2tOyxHqNrtFO0hTM9tXzJfehfQCMMNlKja', -- pro3_mdp
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
        '$2y$10$kRLzFBXkDjrC7d6HfnxLIuN9XimXNiSQwlEUgoY1i1CUui4LBEqya', -- pro2_mdp
        'pro2_nom',
        'pro2_prenom',
        '2220002220',
        '52d43379-8f75-4fbd-8b06-d80a87b2c2b4'
    ),
    (
        (table id_adresse),
        'pro4',
        'pro@4.413',
        '$2y$10$qFqBoE52tjVj1RNI80J1Pu.fvrfVBfMIxqysblolGNk79Q2R22ZNy', -- pro4_mdp
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
        'member1',
        'member@1.413',
        '$2y$10$xuJ9YQIsn1CnN5ja76YDbeY0rX8bmtMraSxeoeMRgBr20lGDpUm/2', -- member1_mdp
        'member1_nom',
        'member1_prenom',
        '0001110001',
        '123e4567-e89b-12d3-a456-426614174000'
    ),
    (
        (table id_adresse),
        'member2',
        'member@2.413',
        '$2y$10$WmZ.J1qFMf.m0mB3N7m6e.rYbkSjxH5yl/22ksIqjTth8Ar7jEgVO', -- member2_mdp
        'member2_nom',
        'member2_prenom',
        '0002220002',
        '9ea59c5b-bb75-4cc9-8f80-77b4ce851a0b'
    ),
    (
        (table id_adresse),
        'member3',
        'member@3.413',
        '$2y$10$TvDgV/Lk1OF8dIMmlIub/ellK3FoWCqhvDGF6Ob5bY7KDvAvwNeTO', -- member3_mdp
        'member3_nom',
        'member3_prenom',
        '0003330003',
        null
    );