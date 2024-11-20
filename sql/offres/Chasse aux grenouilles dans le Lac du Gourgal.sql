begin;

set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, numero_voie, nom_voie)
        values
            ('22', 070, 14, 'Rue de l''Eglise')
        returning
            id
    ),
    id_offre as (
        insert into
            activite (
                id_adresse,
                id_professionnel,
                id_image_principale,
                libelle_abonnement,
                titre,
                resume,
                description_detaillee,
                indication_duree,
                prestations_incluses
            )
        values
            (
                (table id_adresse),
                1,
                5,
                'gratuit',
                'Chasse aux grenouilles dans le Lac du Gourgal',
                'Chasse aux grenouilles dans le Lac du Gourgal résumé',
                'Chasse aux grenouilles dans le Lac du Gourgal description',
                '3:00:',
                'Chasse aux grenouilles dans le Lac du Gourgal prestations incluses'
            )
        returning
            id
    )
insert into
    _tags (id_offre, tag)
values
    ((table id_offre), 'nature'),
    ((table id_offre), 'plein air'),
    ((table id_offre), 'aventure');

commit;