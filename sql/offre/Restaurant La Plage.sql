set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, numero_voie, nom_voie)
        values
            ('22', 168, 35, 'Rte de Perros')
        returning
            id
    ),
    id_offre as (
        insert into
            restaurant (
                id_adresse,
                id_image_principale,
                id_professionnel,
                libelle_abonnement,
                titre,
                resume,
                description_detaillee,
                carte,
                richesse,
                sert_dejeuner,
                sert_diner
            )
        values
            (
                (table id_adresse),
                1,
                1,
                'gratuit',
                'La Plage',
                'La Plage est un délicieux restaurant situé à Paimpol. Découvrez nos goutûs plats.',
                'La Plage est un délicieux restaurant situé à Paimpol. Découvrez nos goutûs plats. Oui, je suis détaillé. Me demandez pas plus de détails. Je ne suis qu''un restaurant. Marie y travaillait et puis j''y suis allé une fois c''est vraiment incroyable trop bon.',
                'La carte? Allez voir au restaurant, on vous en donnera une',
                2,
                true,
                true
            )
        returning
            id
    ),
    s1 as (
        insert into
            avis (id_offre, id_membre_auteur, note, contexte, date_experience, commentaire)
        values
            ((table id_offre), id_membre ('SamSepi0l'), 1, 'affaires', '2024-07-25', 'Service désagréable et désorganisé.')
    )
insert into
    _ouverture_hebdomadaire (id_offre, dow, horaires)
values
    (
        (table id_offre),
        1,
        (
            select
                timemultirange (timerange ('12:', '15:30'), timerange ('18:30', '23:59:59'))
        )
    ),
    (
        (table id_offre),
        2,
        (
            select
                timemultirange (timerange ('12:', '15:30'), timerange ('18:30', '23:59:59'))
        )
    ),
    (
        (table id_offre),
        3,
        (
            select
                timemultirange (timerange ('12:', '15:30'), timerange ('18:30', '23:59:59'))
        )
    ),
    (
        (table id_offre),
        4,
        (
            select
                timemultirange (timerange ('12:', '15:30'), timerange ('18:30', '23:59:59'))
        )
    ),
    (
        (table id_offre),
        5,
        (
            select
                timemultirange (timerange ('12:', '15:30'), timerange ('18:30', '23:59:59'))
        )
    ),
    (
        (table id_offre),
        6,
        (
            select
                timemultirange (timerange ('12:', '15:30'), timerange ('18:30', '23:59:59'))
        )
    );