set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, localite, precision_ext)
        values
            ('22', 360, 'Espace Brezillet', 'PARC EXPO BREZILLET')
        returning
            id
    ),
    id_offre as (
        insert into
            spectacle (
                id_adresse,
                id_image_principale,
                id_professionnel,
                libelle_abonnement,
                indication_duree,
                capacite_accueil,
                titre,
                resume,
                description_detaillee,
                url_site_web
            )
        values
            (
                (table id_adresse),
                12,
                2,
                'premium',
                '2:00:',
                1000,
                'Celtic Legends - Tournée 2026',
                'Celtic Legends est un spectacle de musiques et de danses irlandaises qui s''est produit sur de nombreuses scènes à travers le monde depuis sa création, attirant près de 3 millions de spectateurs.',
                'Celtic Legends revient en 2026 avec une nouvelle version du spectacle. Créé à Galway, au Coeur du Connemara, Celtic Legends est un condensé de la culture traditionnelle Irlandaise recréant sur scène l''ambiance électrique d''une soirée dans un pub traditionnel. Venez partager durant 2 heures ce voyage au coeur de l''Irlande soutenu par 5 talentueux musiciens sous la baguette de Sean McCarthy et de 12 extraordinaires danseurs sous la houlette de la créative Jacintha Sharpe.',
                'https://www.celtic-legends.net'
            )
        returning
            id
    ),
    s1 as (
        insert into
            _tags (id_offre, tag)
        values
            ((table id_offre), 'musique'),
            ((table id_offre), 'spectacle')
    ),
    s2 as ( -- Cette CTE a besoin des valeurs des précédentes, mais elle ne retourne pas de valeur. On doit quand même la nommer, on utilsera la convention de nomamge s1, s2, s3...
        insert into
            avis ( --
                id_offre,
                id_membre_auteur,
                note,
                contexte,
                date_experience,
                commentaire
            )
        values
            ( --
                (table id_offre),
                id_membre ('5cover'), -- Récupère l'ID de membre à partir du pseudo
                4, -- Note sur 5
                'affaires', -- Contexte : affaires, couple, solo, famille, amis
                '2024-07-13', -- Date d'experience
                'Incroyable le jeu son et lumières est parfaitement maitrisé!' -- Commentaire
            )
    )
insert into
    periode_ouverture (id_offre, debut_le, fin_le)
values
    ((table id_offre), '2026-04-10T20:00:00.000Z', '2026-04-11T01:00:00.000Z');
