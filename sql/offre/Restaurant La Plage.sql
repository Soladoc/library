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
        avis (
            id_offre,
            id_membre_auteur,
            note,
            contexte,
            date_experience,
            commentaire
        )
    values
        (
            (table id_offre),
            id_membre ('SamSepi0l'),
            1,
            'affaires',
            '2024-07-25',
            'Service désagréable et désorganisé.'
        )
    ),
    s2 as (
        insert into tarif (
            nom,
            id_offre,
            montant
        )
        values(
            'Menu midi',
            (table id_offre),
            20
        ),
        (
            'Menu soir',
            (table id_offre),
            25
        ),
        (
            'Menu ouvrier',
            (table id_offre),
            10
        )
    )
insert into
    horaire_ouverture (id_offre, dow, heure_debut, heure_fin)
values
    ((table id_offre), 1, '12:', '15:30'),
    ((table id_offre), 1, '18:30', '23:59:59'),
    ((table id_offre), 2, '12:', '15:30'),
    ((table id_offre), 2, '18:30', '23:59:59'),
    ((table id_offre), 3, '12:', '15:30'),
    ((table id_offre), 3, '18:30', '23:59:59'),
    ((table id_offre), 4, '12:', '15:30'),
    ((table id_offre), 4, '18:30', '23:59:59'),
    ((table id_offre), 5, '12:', '15:30'),
    ((table id_offre), 5, '18:30', '23:59:59'),
    ((table id_offre), 6, '12:', '15:30'),
    ((table id_offre), 6, '18:30', '23:59:59');
