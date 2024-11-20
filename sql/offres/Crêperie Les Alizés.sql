begin;

set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, numero_voie, nom_voie)
        values
            ('22', 162, 14, 'Rue des Huit Patriotes')
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
                15,
                1,
                'gratuit',
                'Crêperie Les Alizés',
                'La Crêperie Les Alizés est une délicieuse crêperie située à Paimpol. Découvrez nos goutûs plats.',
                'La Crêperie Les Alizés est une délicieuse crêperie située à Paimpol. Découvrez nos goutûs plats. Oui, je suis détaillé. Me demandez pas plus de détails. Je ne suis qu''un restaurant',
                'La carte? Allez voir au restaurant, on vous en donnera une',
                2,
                true,
                true
            )
        returning
            id
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