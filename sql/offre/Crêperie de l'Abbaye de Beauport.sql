set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, numero_voie, nom_voie)
        values
            ('22', 162, 32, 'Rue de Beauport')
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
                13,
                1,
                'gratuit',
                'Crêperie de l''Abbaye de Beauport',
                'La Crêperie de l''Abbaye de Beauport est une délicieuse crêperie située à Paimpol. Découvrez nos goutûs plats.',
                'La Crêperie de l''Abbaye de Beauport est une délicieuse crêperie située à Paimpol. Découvrez nos goutûs plats. Oui, je suis détaillé. Me demandez pas plus de détails. Je ne suis qu''un restaurant',
                'La carte? Allez voir au restaurant, on vous en donnera une',
                2,
                true,
                true
            )
        returning
            id
    ),
    s1 as ( -- Cette CTE a besoin des valeurs des précédentes, mais elle ne retourne pas de valeur. On doit quand même la nommer, on utilsera la convention de nomamge s1, s2, s3...
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
                5, -- Note sur 5
                'amis', -- Contexte : affaires, couple, solo, famille, amis
                '2005-07-11', -- Date d'experience
                'Super créperie même en été! nous y avons ai fété le 19 eme anniversaire de mon amis Benjamin et le staff nous a offert des crèpes au caramel démicieuses!' -- Commentaire
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