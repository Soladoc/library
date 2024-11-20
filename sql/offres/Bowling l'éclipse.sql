begin;

set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, nom_voie, localite)
        values
            ('22', 070, 'Rte de Tréguier', 'ZAC le Lion de Saint-Marc')
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
                url_site_web,
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
                17,
                'gratuit',
                'https://www.tregor-bowling.com',
                'Bowling L''éclipse',
                'Un bowling, laser game et bar avec jeux',
                'Toutes sortes de choses bla bla bla bla bla blabla bla blabla bla blabla bla blabla bla blabla bla blabla bla blabla bla blabla bla blabla bla blabla bla blabla bla blabla bla blabla bla blabla bla bla',
                '0:20:',
                'De nombreuses choses'
            )
        returning
            id
    ),
    s1 as (
        insert into
            _tags (id_offre, tag)
        values
            ((table id_offre), 'sport'),
            ((table id_offre), 'famille')
    )
insert into
    _gallerie (id_offre, id_image)
values
    ((table id_offre), 16),
    ((table id_offre), 18),
    ((table id_offre), 19),
    ((table id_offre), 20),
    ((table id_offre), 21),
    ((table id_offre), 22);