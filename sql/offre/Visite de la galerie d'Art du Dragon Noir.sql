begin;

set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, numero_voie, nom_voie)
        values
            ('22', 93, 21, 'rue Docteur Calmette')
        returning
            id
    ),
    id_offre as (
        insert into
            visite (
                id_adresse,
                indication_duree,
                id_image_principale,
                id_professionnel,
                libelle_abonnement,
                titre,
                resume,
                description_detaillee,
                url_site_web
            )
        values
            (
                (table id_adresse),
                '0:45:',
                36,
                1,
                'gratuit',
                'Visite de la galerie d''Art du Dragon Noir',
                'Découvrez la fascinante Galerie d''Art du Dragon Noir lors de cette visite guidée exclusive. Plongez dans un univers artistique unique où chaque œuvre raconte une histoire captivante. Cette expérience immersive vous permettra d''explorer des collections variées, allant des peintures contemporaines aux sculptures traditionnelles, tout en bénéficiant des explications passionnantes de notre guide expert. Ne manquez pas cette opportunité de vous imprégner de l''art sous toutes ses formes.',
                'La visite de la Galerie d''Art du Dragon Noir est une expérience inoubliable pour tous les amateurs d''art et de culture. Située dans un cadre enchanteur, cette galerie renommée abrite une collection éclectique d''œuvres d''art qui sauront captiver votre imagination.

Dès votre arrivée, vous serez accueilli par notre guide expert, qui vous accompagnera tout au long de la visite. Vous commencerez par une introduction à l''histoire de la galerie et à son fondateur, un passionné d''art qui a consacré sa vie à rassembler des œuvres exceptionnelles du monde entier.

La première partie de la visite vous emmènera à travers les salles dédiées à l''art contemporain. Vous découvrirez des peintures, des installations et des performances artistiques qui repoussent les limites de la créativité. Notre guide vous expliquera les techniques utilisées par les artistes, ainsi que les messages et les émotions qu''ils cherchent à transmettre à travers leurs œuvres.

Ensuite, vous pénétrerez dans les salles consacrées à l''art traditionnel. Ici, vous pourrez admirer des sculptures, des gravures et des tapisseries qui témoignent de l''héritage culturel de différentes régions du monde. Chaque pièce est soigneusement sélectionnée pour sa qualité et son importance historique, offrant un aperçu unique des traditions artistiques ancestrales.

La visite se poursuivra avec une exploration des œuvres d''artistes émergents. La galerie est fière de soutenir les talents de demain en leur offrant une plateforme pour exposer leurs créations. Vous aurez l''occasion de découvrir des œuvres innovantes et de discuter avec notre guide des tendances actuelles dans le monde de l''art.

Pour clôturer cette expérience en beauté, vous serez invité à participer à un atelier interactif où vous pourrez vous essayer à une technique artistique. Que ce soit la peinture, la sculpture ou le dessin, cet atelier vous permettra de vous immerger pleinement dans le processus créatif et de repartir avec une œuvre personnelle.

La visite de la Galerie d''Art du Dragon Noir est bien plus qu''une simple exposition; c''est une véritable aventure artistique qui vous laissera des souvenirs impérissables. Réservez dès maintenant votre place pour cette expérience unique et laissez-vous envoûter par la magie de l''art.',
                'https://www.tripadvisor.fr/Attraction_Review-g196529-d15183404-Reviews-Galerie_d_Art_du_Dragon_Noir-Lamballe_Cotes_d_Armor_Brittany.html'
            )
        returning
            id
    ),
    s1 as (
        insert into
            _langue_visite (id_visite, code_langue)
        values
            ((table id_offre), 'fr'),
            ((table id_offre), 'en')
    ),
    s2 as (
        insert into
            _tags (id_offre, tag)
        values
            ((table id_offre), 'culturel'),
            ((table id_offre), 'musée')
    ),
    s3 as (
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
            id_membre ('rstallman'),
            3,
            'famille',
            '18-09-2024',
            'Nous aurion aprécié une visite guidé'
        )
    )
insert into
    horaire_ouverture (id_offre, dow, heure_debut, heure_fin)
values
    ((table id_offre), 0, '10:', '18:'),
    ((table id_offre), 1, '10:', '18:'),
    ((table id_offre), 2, '10:', '18:'),
    ((table id_offre), 3, '10:', '18:'),
    ((table id_offre), 4, '10:', '18:'),
    ((table id_offre), 5, '10:', '18:'),
    ((table id_offre), 6, '13:', '18:');

commit;