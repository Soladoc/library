begin;

set schema 'pact';

-- Parc Aquarev
with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, nom_voie)
        values
            ('22', 136, 'Rue du Mène')
        returning
            id
    ),
    id_offre as (
        insert into
            parc_attractions (
                id_adresse,
                id_image_principale,
                id_image_plan,
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
                3,
                46,
                1,
                'gratuit',
                'Parc de loisirs Aquarev',
                'Découvrez le parc de loisirs Aquarev, véritable poumon vert de Loudéac.',
                '## Un « rêve » aménagé en pleine nature

30 hectares aménagés autour d''un étang pour le loisir et la détente : une plaine de jeux, un labyrinthe, une bambouseraie, des pontons de pêche, des aires de pique-nique, un parcours sportif, un terrain multi-sport, le tout dans une ambiance détendue, « zen »… Pour tous et en accès libre.

Autour de son « tipi » emblématique, le site présente une nouvelle image d''un parc de loisirs, de promenades, de vie au cœur d''un espace vert rustique qui permet à chacun de trouver des lieux différents, pour se rencontrer, prendre du bon temps et du plaisir.

Le site accueille régulièrement de nombreux événements municipaux et manifestations associatives. Chaque mercredi d''été, venez par exemple participer aux « Ateliers d''Aquarev » sous le tipi, des animations gratuite pour toute la famille proposées par des commerçant ou intervenants du territoire autour des plantes et de la nature.

### Une ambiance propice à la détente

Une partie du mobilier est en bois : kiosques, pontons de pêche, ponceaux, tables de pique-nique… pour s''intégrer parfaitement au cadre naturel du parc.  Le tout dans une ambiance « zen ».

Dans cet ensemble, on retrouve : des aires de pique-nique avec barbecues en accès libre, des parcours de promenade diversifiés et ombragés, du mobilier de détente…

## Des espaces ludiques

### La plaine de jeux

La plaine de jeux accueille les enfants de 3 à 12 ans : jeux de cordes, balançoire royale, parcours aventure (avec agrès), tour pyramide avec 2 toboggans, combiné multi-jeux…

### Le village des menhirs

Une aire de jeux pour les tout-petits avec balançoire et cabanons.

### La toile d''araignée

Une grande toile de cordes qui monte jusqu''à 6 m.

### Le parcours aquatique

Jeux aquatiques pour découvrir se rafraîchir et découvrir en s''amusant.

### L''espace des tout-petits à proximité du tipi

Une aire de jeux pour les plus petits avec toboggan, cabane, balançoires…
 
## Des aménagements pour tous

### Site adapté aux Personnes à Mobilité Réduite.

Tout a été fait pour que le site soit accessible aux Personnes à Mobilité Réduite (PMR). La pente des cheminements est toujours inférieure à 5 %. Trois pontons de pêche sont spécialement conçus pour les PMR, et positionnés en concertation avec la Fédération départementale de pêche et son représentant local.

Pour les bâtiments et le camping, les plans ont reçu un avis favorable de la commission d''accessibilité.

### Les jardins à thèmes

Une placette, des jeux de boules, un kiosque à thé, promenades et pontons sur pilotis

Un labyrinthe bambouseraie

Des cheminements étroits, en surface minérale, serpentant entre les différentes essences… dans un esprit «japonais».

### Des équipements sportifs

Un parcours sportif accessible à tous ponctue votre promenade autour des étangs.

Pour les plus sportifs le parc propose également des terrains de tennis et un terrain multisport en accès libre.

Pour les clubs et les licenciés : un étang de pêche et de nombreux pontons aménages, un pas de tir à l''arc, un embarquement voile…

### Espace Ado

Avec un terrain multi-sports, tennis…

## Infos pratiques

Site en accès libre jusqu''à 23h00, placé sous vidéo-surveillance.
2 parkings voitures et 1 parking bus.
Toilettes et douche.
Aire camping-car.
Camping et snack sur le site.',
                'https://www.ville-loudeac.fr/listes/parc-aquarev'
            )
        returning
            id
    ),
    s1 as (
        insert into
            _tags (id_offre, tag)
        values
            ((table id_offre), 'famille'),
            ((table id_offre), 'plein air'),
            ((table id_offre), 'sport')
    )
insert into
    _horaire_ouverture (id_offre, dow, heure_debut, heure_fin)
values
    ((table id_offre), 1, '9:', '23:'),
    ((table id_offre), 2, '9:', '23:'),
    ((table id_offre), 3, '9:', '23:'),
    ((table id_offre), 4, '9:', '23:'),
    ((table id_offre), 5, '9:', '23:'),
    ((table id_offre), 6, '9:', '23:');

-- Restaurant La Plage
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
    )
insert into
    _horaire_ouverture (id_offre, dow, heure_debut, heure_fin)
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

-- truncate table _offre restart identity cascade;
-- Crêperie Les Alizés
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
    _horaire_ouverture (id_offre, dow, heure_debut, heure_fin)
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

-- Crêperie de l'Abbaye de Beauport
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
    )
insert into
    _horaire_ouverture (id_offre, dow, heure_debut, heure_fin)
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

-- Visite de la galerie d'Art du Dragon Noir
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
    )
insert into
    _horaire_ouverture (id_offre, dow, heure_debut, heure_fin)
values
    ((table id_offre), 0, '10:', '18:'),
    ((table id_offre), 1, '10:', '18:'),
    ((table id_offre), 2, '10:', '18:'),
    ((table id_offre), 3, '10:', '18:'),
    ((table id_offre), 4, '10:', '18:'),
    ((table id_offre), 5, '10:', '18:'),
    ((table id_offre), 6, '13:', '18:');

-- Visite du phare Vauban au Cap Fréhel
with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, nom_voie)
        values
            ('22', 201, 'Rte du Cap')
        returning
            id
    ),
    id_offre as (
        insert into
            visite (
                id_adresse,
                id_image_principale,
                id_professionnel,
                libelle_abonnement,
                indication_duree,
                titre,
                resume,
                description_detaillee,
                url_site_web
            )
        values
            (
                (table id_adresse),
                8,
                1,
                'gratuit',
                '1:30:',
                'Visite du phare Vauban au Cap Fréhel',
                'Découvrez la beauté sauvage de la Bretagne avec notre visite guidée du phare du Cap Fréhel. Cette excursion vous offre une vue imprenable sur la côte bretonne, une immersion dans l''histoire maritime et une expérience inoubliable au cœur de la nature.',
                '#### Introduction
La visite du phare du Cap Fréhel est une expérience unique qui vous plonge dans l''histoire et la beauté naturelle de la Bretagne. Situé sur la côte d''Émeraude, ce phare emblématique offre des vues panoramiques à couper le souffle sur la mer et les falaises environnantes.

#### Déroulement de la visite
La visite commence par une promenade guidée à travers les sentiers côtiers, où vous pourrez admirer la flore et la faune locales. Votre guide vous racontera l''histoire fascinante du phare, construit au XIXe siècle pour guider les marins à travers les eaux tumultueuses de la Manche. Vous découvrirez également les légendes et les mythes qui entourent ce lieu chargé d''histoire.

#### Ascension du phare
L''un des moments forts de la visite est l''ascension du phare. Après avoir gravi les marches, vous atteindrez le sommet où une vue à 360 degrés sur la côte bretonne vous attend. Par temps clair, vous pourrez même apercevoir les îles Anglo-Normandes. Votre guide vous expliquera le fonctionnement du phare et son importance stratégique pour la navigation maritime.

#### Exploration des environs
Après la visite du phare, vous aurez l''occasion d''explorer les environs. Les falaises de grès rose du Cap Fréhel sont un spectacle à ne pas manquer, offrant un contraste saisissant avec le bleu de la mer. Vous pourrez également vous promener dans le parc naturel qui entoure le phare, où vous pourrez observer une variété d''oiseaux marins et de plantes rares.

#### Conclusion
La visite du phare du Cap Fréhel est une expérience enrichissante qui combine histoire, nature et aventure. Que vous soyez un passionné d''histoire, un amoureux de la nature ou simplement à la recherche d''une journée inoubliable, cette excursion est faite pour vous. Réservez dès maintenant pour vivre une aventure bretonne inoubliable.

#### Informations pratiques
- **Durée de la visite** : Environ 3 heures
- **Point de départ** : Parking du Cap Fréhel
- **Inclus** : Guide professionnel, accès au phare, promenade guidée
- **Recommandations** : Porter des chaussures confortables et des vêtements adaptés à la météo

Ne manquez pas cette opportunité unique de découvrir l''un des joyaux de la Bretagne. Réservez votre visite dès aujourd''hui et laissez-vous émerveiller par la beauté du Cap Fréhel.',
                'https://www.dinan-capfrehel.com/sit/phare-du-cap-frehel'
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
            ((table id_offre), 'patrimoine'),
            ((table id_offre), 'histoire'),
            ((table id_offre), 'nature')
    )
insert into
    _horaire_ouverture (id_offre, dow, heure_debut, heure_fin)
values
    ((table id_offre), 0, '10:', '18:'),
    ((table id_offre), 1, '10:', '18:'),
    ((table id_offre), 2, '10:', '18:'),
    ((table id_offre), 3, '10:', '18:'),
    ((table id_offre), 4, '10:', '18:'),
    ((table id_offre), 5, '10:', '18:'),
    ((table id_offre), 6, '13:', '18:');

-- Visite du musée d'Art et d'Histoire de Saint-Brieuc
with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, numero_voie, nom_voie)
        values
            ('22', 278, 2, 'Rue des Lycéens Martyrs')
        returning
            id
    ),
    id_offre as (
        insert into
            visite (
                id_adresse,
                id_image_principale,
                id_professionnel,
                libelle_abonnement,
                indication_duree,
                titre,
                resume,
                description_detaillee,
                url_site_web
            )
        values
            (
                (table id_adresse),
                45,
                2,
                'gratuit',
                '4:00:',
                'Visite du musée d''Art et d''Histoire de Saint-Brieuc',
                'Découvrez l''histoire et la culture de la Bretagne à travers une visite guidée du musée d''Art et d''Histoire de Saint-Brieuc. Plongez dans des collections riches et variées, allant de l''archéologie à l''art contemporain, en passant par des expositions temporaires fascinantes. Une expérience immersive et enrichissante pour tous les amateurs d''art et d''histoire.',
                '#### Introduction au musée
Le musée d''Art et d''Histoire de Saint-Brieuc est un lieu incontournable pour quiconque souhaite explorer le patrimoine culturel et historique de la Bretagne. Situé au cœur de la ville, ce musée offre une vue d''ensemble sur l''évolution artistique et historique de la région, depuis les temps préhistoriques jusqu''à nos jours.

#### Les collections permanentes
La visite commence par les collections permanentes, qui couvrent une large période historique. Les sections d''archéologie présentent des objets fascinants datant de la préhistoire, de l''Antiquité et du Moyen Âge, offrant un aperçu unique de la vie quotidienne et des pratiques culturelles de ces époques. Les amateurs d''art pourront admirer des œuvres de maîtres anciens et modernes, ainsi que des créations contemporaines qui témoignent de la vitalité artistique de la Bretagne.

#### Les expositions temporaires
En plus des collections permanentes, le musée propose régulièrement des expositions temporaires qui mettent en lumière des thèmes spécifiques ou des artistes locaux et internationaux. Ces expositions sont souvent accompagnées de conférences, d''ateliers et d''autres événements culturels, offrant ainsi une expérience interactive et éducative.

#### La visite guidée
Pour une expérience encore plus enrichissante, nous proposons une visite guidée par des experts passionnés. Ces guides vous feront découvrir les trésors cachés du musée, partageront des anecdotes historiques et répondront à toutes vos questions. La visite guidée est disponible en plusieurs langues et peut être adaptée aux besoins spécifiques des groupes, qu''ils soient scolaires, familiaux ou professionnels.

#### Les services supplémentaires
Le musée dispose également d''une boutique où vous pourrez acheter des souvenirs, des livres et des reproductions d''œuvres d''art. Un café-restaurant est également disponible sur place, offrant une pause agréable après la visite. Pour les visiteurs souhaitant approfondir leurs connaissances, une bibliothèque spécialisée est accessible sur demande.

#### Informations pratiques
Le musée est ouvert du mardi au dimanche, de 10h à 18h. Les tarifs d''entrée sont abordables et des réductions sont disponibles pour les étudiants, les seniors et les groupes. Pour une visite sans stress, il est recommandé de réserver à l''avance, surtout pour les visites guidées.

Ne manquez pas cette opportunité unique de découvrir l''art et l''histoire de la Bretagne dans un cadre exceptionnel. Réservez dès maintenant votre visite au musée d''Art et d''Histoire de Saint-Brieuc et laissez-vous transporter par la richesse de son patrimoine.',
                'https://reserves-naturelles.org/reserves/sept-iles'
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
            ((table id_offre), 'musée'),
            ((table id_offre), 'histoire'),
            ((table id_offre), 'art')
    )
insert into
    _horaire_ouverture (id_offre, dow, heure_debut, heure_fin)
values
    ((table id_offre), 0, '10:', '18:'),
    ((table id_offre), 1, '10:', '18:'),
    ((table id_offre), 2, '10:', '18:'),
    ((table id_offre), 3, '10:', '18:'),
    ((table id_offre), 4, '10:', '18:'),
    ((table id_offre), 5, '10:', '18:'),
    ((table id_offre), 6, '13:', '18:');

-- Trail dans la réserve naturelle des Sept Îles
with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, nom_voie, precision_ext)
        values
            ('22', 168, 'Chem. du Squevel', 'Parking du Sémaphore - Sentier des Douaniers')
        returning
            id
    ),
    id_offre as (
        insert into
            visite (
                id_adresse,
                id_image_principale,
                id_professionnel,
                libelle_abonnement,
                indication_duree,
                titre,
                resume,
                description_detaillee,
                url_site_web
            )
        values
            (
                (table id_adresse),
                43,
                2,
                'gratuit',
                '4:00:',
                'Trail dans la réserve naturelle des Sept Îles',
                'Découvrez la beauté sauvage de la réserve naturelle des Sept Îles à Perros-Guirec avec notre offre de trail unique. Parcourez des sentiers pittoresques, admirez la faune et la flore exceptionnelles, et profitez de vues panoramiques sur la côte bretonne. Une expérience inoubliable pour les amateurs de nature et de sport.',
                '#### Introduction
Plongez au cœur de la nature bretonne avec notre offre de trail dans la réserve naturelle des Sept Îles à Perros-Guirec. Cette aventure est idéale pour les amateurs de course à pied et de nature, offrant une expérience unique et enrichissante.

#### Parcours et Paysages
Le trail commence à Perros-Guirec, une charmante station balnéaire située sur la Côte de Granit Rose. Vous traverserez des sentiers côtiers offrant des vues imprenables sur les Sept Îles, un archipel protégé abritant une faune et une flore exceptionnelles. Les paysages variés incluent des falaises escarpées, des plages de sable fin, et des landes fleuries, créant un cadre idyllique pour votre course.

#### Faune et Flore
La réserve naturelle des Sept Îles est un sanctuaire pour de nombreuses espèces d''oiseaux marins, notamment les macareux moines et les fous de Bassan. Vous aurez peut-être la chance d''apercevoir ces oiseaux majestueux depuis les points de vue panoramiques le long du parcours. La flore locale est également remarquable, avec des espèces rares et protégées qui ajoutent à la beauté du paysage.

#### Niveau de Difficulté et Encadrement
Le trail est conçu pour être accessible à tous les niveaux de coureurs, avec des options de parcours de différentes longueurs et difficultés. Que vous soyez un débutant ou un coureur expérimenté, vous trouverez un itinéraire adapté à vos capacités. Notre équipe de guides expérimentés vous accompagnera tout au long du parcours, assurant votre sécurité et partageant des anecdotes sur la région.

#### Équipement et Services
Pour votre confort, nous fournissons tout l''équipement nécessaire, y compris des chaussures de trail, des sacs à dos, et des bâtons de marche. Des points de ravitaillement sont prévus le long du parcours, offrant des boissons et des collations pour vous maintenir en forme. À la fin de la course, vous pourrez vous détendre et profiter d''un repas local dans un cadre convivial.

#### Conclusion
Notre offre de trail dans la réserve naturelle des Sept Îles à Perros-Guirec est bien plus qu''une simple course. C''est une immersion totale dans la nature bretonne, une occasion de découvrir des paysages époustouflants et une faune exceptionnelle. Rejoignez-nous pour une aventure inoubliable et ressentez la magie de la Côte de Granit Rose.

Réservez dès maintenant votre place et préparez-vous à vivre une expérience unique et enrichissante !',
                'https://reserves-naturelles.org/reserves/sept-iles'
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
            ((table id_offre), 'nature'),
            ((table id_offre), 'plein air'),
            ((table id_offre), 'sport')
    )
insert into
    _horaire_ouverture (id_offre, dow, heure_debut, heure_fin)
values
    ((table id_offre), 0, '10:', '18:'),
    ((table id_offre), 1, '10:', '18:'),
    ((table id_offre), 2, '10:', '18:'),
    ((table id_offre), 3, '10:', '18:'),
    ((table id_offre), 4, '10:', '18:'),
    ((table id_offre), 5, '10:', '18:'),
    ((table id_offre), 6, '13:', '18:');

-- Découverte de la Côte de Granit Rose
with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, precision_ext, numero_voie, nom_voie)
        values
            ('22', 168, 'Parking De La Rade 83', 72, 'Rue Ernest Renan')
        returning
            id
    ),
    id_offre as (
        insert into
            visite (
                id_adresse,
                id_image_principale,
                id_professionnel,
                libelle_abonnement,
                indication_duree,
                titre,
                resume,
                description_detaillee,
                url_site_web
            )
        values
            (
                (table id_adresse),
                34,
                2,
                'gratuit',
                '2:00:',
                'Découverte de la Côte de Granit Rose en bateau',
                'Découvrez la magie de la Côte de Granite Rose avec notre excursion en bateau inoubliable. Cette visite vous emmène à travers des paysages époustouflants, des formations rocheuses uniques et des plages de sable fin. Profitez de vues panoramiques, de commentaires enrichissants et de moments de détente pour une expérience complète et mémorable.',
                '**Introduction :**
Embarquez pour une aventure inoubliable avec notre excursion en bateau le long de la Côte de Granite Rose. Cette région emblématique de la Bretagne est célèbre pour ses formations rocheuses spectaculaires, ses plages de sable fin et ses eaux cristallines. Notre visite est conçue pour vous offrir une expérience immersive et enrichissante, combinant découverte, détente et émerveillement.

**Départ et Itinéraire :**
Notre excursion commence au port de Perros-Guirec, où vous serez accueilli par notre équipage chaleureux et expérimenté. Après un briefing de sécurité, nous mettrons le cap vers les sites les plus emblématiques de la Côte de Granite Rose. Vous naviguerez le long de la côte, admirant des formations rocheuses uniques telles que le Phare de Ploumanac''h, la Plage de Trestraou et les Sept-Îles.

**Points Forts de la Visite :**
- **Phare de Ploumanac''h :** Un des phares les plus pittoresques de la région, offrant une vue imprenable sur la côte.
- **Plage de Trestraou :** Une plage de sable fin idéale pour se détendre et profiter du paysage.
- **Les Sept-Îles :** Un archipel protégé abritant une riche biodiversité, notamment des colonies d''oiseaux marins.

**Commentaires et Histoire :**
Tout au long de la visite, notre guide vous fournira des commentaires enrichissants sur l''histoire, la géologie et la faune de la région. Vous apprendrez comment les formations rocheuses ont été sculptées par le temps et les éléments, et découvrirez les légendes locales qui entourent ces lieux mystiques.

**Moments de Détente :**
En plus des vues spectaculaires, notre excursion inclut des moments de détente. Nous ferons une pause sur une plage isolée où vous pourrez vous baigner, vous détendre sur le sable ou simplement profiter de la tranquillité de la nature. Un goûter léger sera également servi à bord pour vous rafraîchir et vous restaurer.

**Conclusion :**
Notre visite en bateau de la Côte de Granite Rose est une expérience unique qui combine découverte, détente et émerveillement. Que vous soyez un amoureux de la nature, un passionné d''histoire ou simplement à la recherche d''une journée inoubliable, cette excursion est faite pour vous. Réservez dès maintenant pour vivre une aventure mémorable sur l''une des plus belles côtes de France.

---

**Informations Pratiques :**
- **Durée :** 3 heures
- **Départ :** Port de Perros-Guirec
- **Inclus :** Guide expérimenté, commentaires enrichissants, goûter léger, moments de détente sur la plage
- **Réservation :** Contactez-nous pour réserver votre place et obtenir plus d''informations.

Nous avons hâte de vous accueillir à bord pour une journée inoubliable sur la Côte de Granite Rose !',
                'https://www.bretagne-cotedegranitrose.com'
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
            ((table id_offre), 'nature'),
            ((table id_offre), 'nautique'),
            ((table id_offre), 'culturel')
    )
insert into
    _horaire_ouverture (id_offre, dow, heure_debut, heure_fin)
values
    ((table id_offre), 0, '10:', '18:'),
    ((table id_offre), 1, '10:', '18:'),
    ((table id_offre), 2, '10:', '18:'),
    ((table id_offre), 3, '10:', '18:'),
    ((table id_offre), 4, '10:', '18:'),
    ((table id_offre), 5, '10:', '18:'),
    ((table id_offre), 6, '13:', '18:');

-- Balade poétique à Camet
with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, localite)
        values
            ('22', 203, 'Vallée des Saints')
        returning
            id
    ),
    id_offre as (
        insert into
            visite (
                id_adresse,
                id_image_principale,
                id_professionnel,
                libelle_abonnement,
                indication_duree,
                titre,
                resume,
                description_detaillee,
                url_site_web
            )
        values
            (
                (table id_adresse),
                42,
                2,
                'gratuit',
                '2:00:',
                'Balade poétique à Camet',
                'Après, Les oiseaux, les fleurs sauvages, les 4 saisons, les poètes et la nature, les arbres ; cette année met à l''honneur Le silence de l''eau. En nous baladant tout autour de l''étang, nous allons rencontrer poèmes et chansons, d''Eugène Guillevic à Anne Sylvestre, de Guy Béart à Paul Eluard, situé à un endroit qui résonne avec le poème.',
                'Cette édition sur le thème de l''eau a inspiré de nombreuses animations :

- Un atelier d''aquarelle à la médiathèque, avec l''artiste Gérard Hubert
- Une séance de fabrication de bateaux, poissons, grenouilles, baleines, cygnes en origamis
- Des aquarelles réalisées par les résident·es exposées à la médiathèque, des lectures et chansons à l''EHPAD Louis Morel de Ploeuc
- Des lectures, la réalisation de fresques et l''écriture de poèmes à l''école Le Petit Prince, fresques exposées dans la médiathèque et sous les préaux extérieurs',
                'https://mediathequesdelabaie.fr/au-programme/rendez-vous2/2563-balade-poetique-a-camet'
            )
        returning
            id
    ),
    s1 as (
        insert into
            _langue_visite (id_visite, code_langue)
        values
            ((table id_offre), 'fr')
    ),
    s2 as (
        insert into
            _tags (id_offre, tag)
        values
            ((table id_offre), 'culturel'),
            ((table id_offre), 'nature')
    )
insert into
    _periode_ouverture (id_offre, debut, fin)
values
    ((table id_offre), '2024-06-14T12:00:00.000Z', '2024-09-27T18:00:00.000Z'),
    ((table id_offre), '2025-06-14T12:00:00.000Z', '2025-09-27T18:00:00.000Z'),
    ((table id_offre), '2026-06-14T12:00:00.000Z', '2026-09-27T18:00:00.000Z');

-- Randonnée dans la vallée des Saints
with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, localite)
        values
            ('63', 046, 'Vallée des Saints')
        returning
            id
    ),
    id_offre as (
        insert into
            visite (
                id_adresse,
                id_image_principale,
                id_professionnel,
                libelle_abonnement,
                indication_duree,
                titre,
                resume,
                description_detaillee,
                url_site_web
            )
        values
            (
                (table id_adresse),
                44,
                1,
                'gratuit',
                '4:00:',
                'Randonnée dans la vallée des Saints',
                'Découvrez la Vallée des Saints à Boudes, un site naturel exceptionnel et chargé d''histoire. Cette offre touristique vous propose une visite guidée de la vallée, incluant des explications sur les sculptures de saints, une randonnée pédestre à travers les paysages pittoresques, et une dégustation de produits locaux. Une expérience unique pour les amateurs de nature, d''histoire et de gastronomie.',
                '**Introduction à la Vallée des Saints :**
La Vallée des Saints à Boudes est un lieu emblématique qui allie beauté naturelle et richesse culturelle. Située dans le département du Puy-de-Dôme, cette vallée abrite une collection unique de sculptures de saints, réalisées par des artistes locaux et internationaux. Chaque statue raconte une histoire et contribue à l''atmosphère spirituelle et sereine du site.

**Visite Guidée des Sculptures :**
La visite commence par une introduction détaillée sur l''histoire et la signification des sculptures de la Vallée des Saints. Un guide expérimenté vous accompagnera à travers le parcours, vous offrant des explications sur chaque statue, leur symbolisme et les légendes qui les entourent. Vous aurez l''occasion de poser des questions et d''approfondir vos connaissances sur les saints et leur rôle dans la culture locale.

**Randonnée Pédestre :**
Après la visite des sculptures, une randonnée pédestre vous attend. Le parcours, adapté à tous les niveaux, vous mènera à travers les paysages magnifiques de la vallée. Vous traverserez des forêts luxuriantes, des champs fleuris et des rivières cristallines. Le guide vous fournira des informations sur la flore et la faune locales, ainsi que sur les particularités géologiques de la région.

**Dégustation de Produits Locaux :**
Pour clôturer cette expérience en beauté, une dégustation de produits locaux vous sera proposée. Vous aurez l''occasion de goûter aux spécialités culinaires de la région, telles que les fromages, les charcuteries et les vins locaux. Cette dégustation se déroulera dans un cadre convivial, où vous pourrez échanger avec les producteurs et en apprendre davantage sur les traditions gastronomiques du Puy-de-Dôme.

**Informations Pratiques :**
- **Durée de la visite :** Environ 4 heures.
- **Point de rendez-vous :** Entrée principale de la Vallée des Saints à Boudes.
- **Équipement recommandé :** Chaussures de marche, vêtements adaptés à la météo, bouteille d''eau.
- **Tarif :** 35€ par personne (incluant la visite guidée, la randonnée et la dégustation).

**Réservation :**
Pour réserver votre place, veuillez contacter notre service client au [numéro de téléphone] ou par email à [adresse email]. Les places sont limitées, alors n''attendez pas pour réserver votre expérience inoubliable à la Vallée des Saints à Boudes.

---

Rejoignez-nous pour une journée mémorable où nature, histoire et gastronomie se rencontrent dans un cadre exceptionnel.',
                'https://www.lavalleedessaints.com'
            )
        returning
            id
    ),
    s1 as (
        insert into
            _langue_visite (id_visite, code_langue)
        values
            ((table id_offre), 'fr')
    ),
    s2 as (
        insert into
            _tags (id_offre, tag)
        values
            ((table id_offre), 'nature'),
            ((table id_offre), 'plein air')
    )
insert into
    _horaire_ouverture (id_offre, dow, heure_debut, heure_fin)
values
    ((table id_offre), 0, '10:', '18:'),
    ((table id_offre), 1, '10:', '18:'),
    ((table id_offre), 2, '10:', '18:'),
    ((table id_offre), 3, '10:', '18:'),
    ((table id_offre), 4, '10:', '18:'),
    ((table id_offre), 5, '10:', '18:'),
    ((table id_offre), 6, '13:', '18:');

-- Visite du Fort La Latte - La Roche Goyon
with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, localite)
        values
            ('22', 201, 'Cap Fréhel')
        returning
            id
    ),
    id_offre as (
        insert into
            visite (
                id_adresse,
                id_image_principale,
                id_professionnel,
                libelle_abonnement,
                indication_duree,
                titre,
                resume,
                description_detaillee,
                url_site_web
            )
        values
            (
                (table id_adresse),
                26,
                1,
                'gratuit',
                '3:00:',
                'Visite du Château de La Roche Goyon, Fort La Latte à Plévenon',
                'Découvrez l''histoire fascinante et les paysages époustouflants du Château de La Roche Goyon, également connu sous le nom de Fort La Latte, lors de cette visite guidée inoubliable. Situé à Plévenon, en Bretagne, ce château médiéval offre une vue imprenable sur la mer et une plongée dans le passé riche de la région. Cette offre touristique inclut une visite guidée, des démonstrations de tir à l''arbalète, et une dégustation de produits locaux pour une expérience complète et immersive.',
                '**Visite Guidée du Château :**
Commencez votre aventure par une visite guidée du Château de La Roche Goyon. Votre guide, expert en histoire locale, vous fera découvrir les différentes parties du château, y compris les tours, les remparts, et les salles historiques. Vous apprendrez comment ce fort a joué un rôle crucial dans la défense de la côte bretonne au fil des siècles. La visite inclut des anecdotes fascinantes sur les seigneurs qui ont habité le château et les batailles qui s''y sont déroulées.

**Démonstrations de Tir à l''Arbalète :**
Pour une expérience encore plus immersive, participez à une démonstration de tir à l''arbalète. Nos experts en armes médiévales vous montreront comment ces armes étaient utilisées pour défendre le château. Vous aurez même l''occasion de tirer quelques flèches vous-même, sous la supervision de nos instructeurs qualifiés. Cette activité est idéale pour les amateurs d''histoire et les passionnés de culture médiévale.

**Dégustation de Produits Locaux :**
Après votre visite et vos activités, profitez d''une dégustation de produits locaux. Savourez des spécialités bretonnes telles que des crêpes, du cidre, et des fromages régionaux. Cette dégustation se déroule dans un cadre pittoresque, offrant une vue magnifique sur la mer et les falaises environnantes. C''est l''occasion parfaite pour découvrir la richesse culinaire de la Bretagne tout en profitant de la beauté naturelle de la région.

**Informations Pratiques :**
- **Durée de la visite :** Environ 3 heures
- **Horaires :** Départs à 10h et 14h, tous les jours sauf le lundi
- **Tarif :** 25€ par adulte, 15€ par enfant (de 6 à 12 ans), gratuit pour les enfants de moins de 6 ans
- **Inclus :** Visite guidée, démonstration de tir à l''arbalète, dégustation de produits locaux
- **Réservation :** Obligatoire, places limitées

Ne manquez pas cette opportunité unique de découvrir l''un des châteaux les plus emblématiques de Bretagne. Réservez dès maintenant pour une expérience inoubliable au Château de La Roche Goyon, Fort La Latte à Plévenon.',
                'https://www.lefortlalatte.com'
            )
        returning
            id
    ),
    s1 as (
        insert into
            _langue_visite (id_visite, code_langue)
        values
            ((table id_offre), 'fr')
    ),
    s2 as (
        insert into
            _tags (id_offre, tag)
        values
            ((table id_offre), 'patrimoine'),
            ((table id_offre), 'histoire'),
            ((table id_offre), 'culturel')
    )
insert into
    _horaire_ouverture (id_offre, dow, heure_debut, heure_fin)
values
    ((table id_offre), 0, '10:', '18:'),
    ((table id_offre), 1, '10:', '18:'),
    ((table id_offre), 2, '10:', '18:'),
    ((table id_offre), 3, '10:', '18:'),
    ((table id_offre), 4, '10:', '18:'),
    ((table id_offre), 5, '10:', '18:'),
    ((table id_offre), 6, '13:', '18:');

-- Découverte interactive de la cité des Télécoms
with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, localite)
        values
            ('22', 198, 'Cité des Télécoms')
        returning
            id
    ),
    id_offre as (
        insert into
            visite (
                id_adresse,
                id_image_principale,
                id_professionnel,
                libelle_abonnement,
                indication_duree,
                titre,
                resume,
                description_detaillee,
                url_site_web
            )
        values
            (
                (table id_adresse),
                4,
                1,
                'gratuit',
                '3:00:',
                'Visite Interactive de la Cité des Télécoms à Pleumeur-Bodou',
                'Plongez dans l''univers fascinant des télécommunications avec notre visite interactive de la Cité des Télécoms à Pleumeur-Bodou. Cette expérience immersive vous permettra de découvrir l''histoire et les innovations technologiques qui ont révolutionné notre monde. Grâce à des dispositifs interactifs, des démonstrations en direct et des ateliers pratiques, vous vivrez une aventure unique et enrichissante, idéale pour les passionnés de technologie et les curieux de tous âges.',
                'Introduction à la Cité des Télécoms :
La Cité des Télécoms, située à Pleumeur-Bodou en Bretagne, est un lieu incontournable pour tous ceux qui s''intéressent à l''histoire et aux avancées technologiques des télécommunications. Cette visite interactive vous offre une opportunité unique de comprendre comment les télécommunications ont transformé notre quotidien et continuent de le faire.

Parcours Interactif :
Dès votre arrivée, vous serez accueilli par un guide expert qui vous accompagnera tout au long de votre visite. Le parcours commence par une introduction historique, où vous découvrirez les premières inventions et les pionniers des télécommunications. Vous pourrez voir de vos propres yeux des objets d''époque, tels que les premiers téléphones et les anciens équipements de transmission.

Démonstrations en Direct :
L''un des points forts de cette visite est les démonstrations en direct. Vous assisterez à des présentations animées par des spécialistes qui vous expliqueront le fonctionnement des technologies actuelles et futures. Que ce soit la fibre optique, les satellites ou les réseaux 5G, chaque démonstration est conçue pour être à la fois éducative et divertissante.

Ateliers Pratiques :
Pour une expérience encore plus immersive, des ateliers pratiques sont proposés. Vous aurez l''occasion de participer à des activités interactives, comme la création de votre propre réseau de communication ou l''assemblage d''un dispositif de télécommunication. Ces ateliers sont adaptés à tous les niveaux, des débutants aux plus expérimentés, et sont un excellent moyen de mettre en pratique ce que vous avez appris.

Expositions Thématiques :
La Cité des Télécoms abrite également plusieurs expositions thématiques qui changent régulièrement. Ces expositions couvrent des sujets variés, allant de l''impact des télécommunications sur la société à l''évolution des technologies de communication. Chaque exposition est conçue pour être interactive et engageante, avec des installations multimédias et des jeux éducatifs.

Espace de Réalité Virtuelle :
Pour une immersion totale, un espace de réalité virtuelle est disponible. Grâce à des casques VR, vous pourrez explorer des environnements virtuels qui simulent les infrastructures de télécommunications, les centres de données et même les satellites en orbite. Cette expérience vous permettra de comprendre de manière visuelle et immersive les complexités des réseaux de communication.

Conclusion :
La visite interactive de la Cité des Télécoms à Pleumeur-Bodou est une expérience enrichissante et captivante qui vous laissera des souvenirs inoubliables. Que vous soyez un passionné de technologie, un curieux ou simplement à la recherche d''une activité éducative et divertissante, cette visite est faite pour vous. Réservez dès maintenant et préparez-vous à découvrir le monde fascinant des télécommunications !

Informations Pratiques :

- Durée de la visite : Environ 3 heures
- Tarifs : Adultes : 15 €, Enfants (6-12 ans) : 10 €, Gratuit pour les moins de 6 ans
- Horaires : Ouvert tous les jours de 10h à 18h
- Réservations : Recommandées en ligne ou par téléphone
- Accès : Facilement accessible en voiture ou en transport en commun

N''attendez plus et venez vivre une aventure technologique unique à la Cité des Télécoms !',
                'https://www.cite-telecoms.com'
            )
        returning
            id
    ),
    s1 as (
        insert into
            _gallerie (id_offre, id_image)
        values
            ((table id_offre), 9),
            ((table id_offre), 11),
            ((table id_offre), 14)
    ),
    s2 as (
        insert into
            _tags (id_offre, tag)
        values
            ((table id_offre), 'culturel'),
            ((table id_offre), 'musée'),
            ((table id_offre), 'technologie')
    )
insert into
    _horaire_ouverture (id_offre, dow, heure_debut, heure_fin)
values
    ((table id_offre), 0, '10:', '18:'),
    ((table id_offre), 1, '10:', '18:'),
    ((table id_offre), 2, '10:', '18:'),
    ((table id_offre), 3, '10:', '18:'),
    ((table id_offre), 4, '10:', '18:'),
    ((table id_offre), 5, '10:', '18:'),
    ((table id_offre), 6, '13:', '18:');

-- Celtic Legends journée 2026
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
                'gratuit',
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
    )
insert into
    _periode_ouverture (id_offre, debut, fin)
values
    ((table id_offre), '2026-04-10T20:00:00.000Z', '2026-04-11T01:00:00.000Z');

-- Karting Kerlabo
with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, nom_voie)
        values
            ('22', 045, 'Axe Chatelaudren-Quintin')
        returning
            id
    ),
    id_offre as (
        insert into
            activite (
                id_adresse,
                id_image_principale,
                id_professionnel,
                libelle_abonnement,
                age_requis,
                titre,
                resume,
                description_detaillee,
                url_site_web,
                indication_duree,
                prestations_incluses
            )
        values
            (
                (table id_adresse),
                27,
                2,
                'gratuit',
                7,
                'Karting Kerlabo',
                'Au cœur des Côtes-d''Armor et à mi-chemin entre St-Brieuc et Guingamp, le site de Kerlabo est un incontournable du karting en Bretagne. Sur un site dédié au loisir et au sport automobile, le circuit de Kerlabo vous offre un tracé unique et vallonné sur plus de 800 mètres!',
                'Ouvert samedi et dimanche (selon météo : horaires à vérifier sur google)+ les apres midi des vacances scolaires du 21 au 31 décembre. 

    Autres jours et horaires possibles sur demande pour groupes de 8 pers. mini.

    Nos horaires sont mis à jour régulièrement sur google en fonction des réservations de groupes. Bien les vérifier avant de se déplacer!

    Vous pouvez téléphoner avant de vous déplacer pour connaitre l''affluence mais nous ne faisons PAS DE RESERVATION (sauf groupes + de 8 personnes pour des animations type challenge ou endurance)

    Nous vous demandons de privilégier vos équipements personnels (casque intégral ou cagoule) si vous en avez + prévoir blouson et GANTS en hiver

    Une CAGOULE en coton réutilisable et personnelle sera OBLIGATOIRE sous nos casques prêtés. Les cagoules sont en vente sur place au tarif de 3€',
                'https://kerlabo-kart.com',
                '0:20:',
                'Nous vous proposons un circuit de karting'
            )
        returning
            id
    ),
    s1 as (
        insert into
            _tags (id_offre, tag)
        values
            ((table id_offre), 'sport'),
            ((table id_offre), 'plein air')
    )
insert into
    _gallerie (id_offre, id_image)
values
    ((table id_offre), 24),
    ((table id_offre), 25),
    ((table id_offre), 28);

-- Randonnée au Menez Bré
with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, localite)
        values
            ('22', 164, 'Menez Bré')
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
                2,
                38,
                'gratuit',
                'Randonnée au Menez Bré',
                'Découvrez la beauté sauvage et préservée du Menez Bré avec notre offre touristique de randonnée. Situé en Bretagne, le Menez Bré offre des paysages à couper le souffle, une riche biodiversité et une histoire fascinante. Cette randonnée guidée vous permettra de vous immerger dans la nature tout en apprenant sur l''histoire et la culture locale. Idéale pour les amateurs de nature et les passionnés de randonnée, cette expérience promet des moments inoubliables.',
                'Itinéraire et Durée :
Notre randonnée au Menez Bré commence à partir du village pittoresque de Plougonven. La randonnée dure environ 4 heures et couvre une distance de 10 kilomètres. Le parcours est modérément difficile, avec des montées et des descentes qui offrent des vues panoramiques sur les montagnes environnantes et la vallée.

Points d''Intérêt :
Au cours de la randonnée, vous découvrirez plusieurs points d''intérêt historiques et naturels. Le Menez Bré est connu pour ses mégalithes, témoins de l''histoire ancienne de la région. Vous aurez l''occasion de voir des menhirs et des dolmens, ainsi que des vestiges de l''époque celtique. De plus, la randonnée traverse des forêts luxuriantes et des landes, offrant une diversité de paysages qui raviront les amoureux de la nature.

Faune et Flore :
Le Menez Bré est un véritable sanctuaire pour la faune et la flore. Vous pourrez observer une variété d''oiseaux, dont des rapaces, ainsi que des mammifères comme les renards et les chevreuils. La flore est également riche, avec des espèces rares et protégées. Notre guide vous aidera à identifier les différentes plantes et animaux que vous rencontrerez.

Guide et Équipement :
La randonnée est guidée par un expert local qui connaît parfaitement la région. Il vous fournira des informations sur l''histoire, la géologie et la biodiversité du Menez Bré. Tout l''équipement nécessaire, y compris les cartes et les bâtons de randonnée, sera fourni. Nous vous recommandons de porter des chaussures de randonnée confortables et des vêtements adaptés aux conditions météorologiques.

Repas et Rafraîchissements :
Un pique-nique composé de produits locaux sera inclus dans l''offre. Vous pourrez déguster des spécialités bretonnes tout en profitant de la vue imprenable sur les montagnes. Des pauses régulières seront prévues pour vous permettre de vous reposer et de vous hydrater.

Réservation et Informations Pratiques :
Pour réserver votre place, veuillez nous contacter par téléphone ou par e-mail. Les groupes sont limités à 15 personnes pour garantir une expérience personnalisée et sécurisée. Le point de rendez-vous sera communiqué lors de la réservation. Nous vous recommandons de réserver à l''avance pour garantir votre place.

Rejoignez-nous pour une aventure inoubliable au cœur de la Bretagne et découvrez les trésors cachés du Menez Bré.',
                '2:30:',
                'Une randonnée au Ménez Bré'
            )
        returning
            id
    ),
    s1 as (
        insert into
            _tags (id_offre, tag)
        values
            ((table id_offre), 'nature'),
            ((table id_offre), 'plein air')
    )
insert into
    _gallerie (id_offre, id_image)
values
    ((table id_offre), 39),
    ((table id_offre), 40);

-- Bowling l'éclipse
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

-- Chasse aux grenouilles dans le Lac du Gourgal
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

-- Initiation au char à voile sur la plage
with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, numero_voie, nom_voie)
        values
            ('22', 186, 1, 'Rue de Belvédère')
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
                age_requis,
                prestations_incluses
            )
        values
            (
                (table id_adresse),
                1,
                10,
                'gratuit',
                'Initiation au char à voile sur la plage de Trestraou',
                'De 7 à 77 ans, le char à voile est à la portée de tous et vous procure une immense sensation de liberté.',
                'Direction les longues plages de sable fin du Marquenterre, au nord de la côte picarde, entre Quend-Plage et Fort-Mahon : l''un des meilleurs spots pour s''essayer au char à voile ! 

Une séance dure 3 heures environ.
Vous commencez par 45 minutes d''initiation avec votre moniteur au cours desquelles vous apprenez les manœuvres, à créer votre trajectoire en sentant le vent.
Et c''est parti pour plus de 2 heures de randonnée sur les longues plages et vous profitez rapidement des sensations grisantes de la vitesse.

Le saviez-vous ? Un char à voile peut atteindre 90km/h ! Rendez-vous dans nos bases nautiques !
L''initiation au char à voile peut être incluse dans le cadre d''une activité scolaire et extrascolaire (Journée de bord de mer plein air, classe de découverte, journée d''intégration), week-end multi activités, séminaires (char à voile, randonnée nautique, équitation, hébergement, repas).',
                '3:00:',
                7,
                'Nous vous proposons une initiation et randonnée en char à voile'
            )
        returning
            id
    )
insert into
    _tags (id_offre, tag)
values
    ((table id_offre), 'nautique'),
    ((table id_offre), 'sport');

-- Escape game À l'Arche du Temps
with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, numero_voie, nom_voie)
        values
            ('22', 278, 18, 'Rue de l''Église')
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
                age_requis,
                prestations_incluses,
                prestations_non_incluses
            )
        values
            (
                (table id_adresse),
                1,
                7,
                'gratuit',
                'https://larchedutemps.com',
                'Escape game À l''Arche du Temps',
                'L''Escape Game Arche Du Temps vous permet de disposer d''un cadre atypique, avec une possibilité de privatisation de l''espace d''accueil (80m²) avec salon modulable, et connexion wifi pour un séminaire, une réunion ou autre.',
                '# Escape Game

## Le Gardien des Reliques

1148 – A la veille de la cérémonie d''inauguration de la Cathédrale, Frère Thomas est retrouvé assassiné dans sa chapelle !

Vous et votre équipe aurez une heure pour retrouver le coupable, son mobile et surtout le trésor qu''il protégeait : les prestigieuses reliques de Saint Lazare.

Dans le décor authentique de la Chapelle des VII Dormants, cet Escape Game vous plonge dans le passé millénaire d''Autun. Cherchez les indices, combinez les pistes et résolvez le mystère du Gardien des Reliques !

De 3 à 6 joueurs, en famille, entre collègues ou entre amis, venez relever nos défis ! Au total, le briefing et l''enquête sont prévus pour une durée d''environ 1h30.',
                '1:30:',
                16,
                'Nous vous proposons un escape game avec énigmes',
                'Nous ne vous proposons pas de repas'
            )
        returning
            id
    )
insert into
    _tags (id_offre, tag)
values
    ((table id_offre), 'famille'),
    ((table id_offre), 'jeu'),
    ((table id_offre), 'aventure');

-- Laser Game Evolution Saint-Brieuc
with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, nom_voie)
        values
            ('22', 360, 'Zone de loisirs Brezillet ouest')
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
                age_requis,
                prestations_incluses,
                prestations_non_incluses
            )
        values
            (
                (table id_adresse),
                1,
                29,
                'gratuit',
                'https://saint-brieuc.lasergame-evolution.fr/',
                'Laser Game Evolution Saint-Brieuc',
                'Bienvenue au Laser Game Evolution de Saint-Brieuc ! Réservation conseillée - Vous pouvez jouer à partir de 4 joueurs et à partir de 7 ans minimum.',
                'Equipé de votre PISTOLET 100% LASER, votre précision et votre rapidité sont vos atouts pour surpasser vos adversaires dans nos labyrinthes ! Utilisez les planches pour vous cacher, mais méfiez-vous des meurtrières et des miroirs ! Pour un anniversaire, avec vos amis ou encore vos collaborateurs, venez jouer sur réservation, car nos labyrinthes vous seront entièrement privatisés ! Une équipe dynamique sera à votre disposition pour vous faire passer un moment inoubliable ! Laser Game Evolution, LE JEU LASER 100 % !',
                '0:20:',
                7,
                'Nous vous proposons un laser game, un quiz game, un blindtest, des fléchettes',
                'Nous ne vous proposons pas de repas'
            )
        returning
            id
    ),
    s1 as (
        insert into
            _tags (id_offre, tag)
        values
            ((table id_offre), 'sport'),
            ((table id_offre), 'famille'),
            ((table id_offre), 'jeu')
    )
insert into
    _gallerie (id_offre, id_image)
values
    ((table id_offre), 30),
    ((table id_offre), 31),
    ((table id_offre), 32),
    ((table id_offre), 33);

-- Lantic Parc Aventure
with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, nom_voie)
        values
            ('22', 117, 'Les étangs')
        returning
            id
    ),
    id_offre as (
        insert into
            activite (
                id_adresse,
                id_image_principale,
                id_professionnel,
                libelle_abonnement,
                url_site_web,
                titre,
                resume,
                description_detaillee,
                indication_duree,
                age_requis,
                prestations_incluses
            )
        values
            (
                (table id_adresse),
                6,
                2,
                'gratuit',
                'https://www.lanticparcaventure.bzh/',
                'Lantic Parc Aventure',
                'LANTIC PARC AVENTURE propose plusieurs parcours acrobatiques en hauteur, du paint ball, du laser tag, pour toute la famille. Venez passer une journée dans les arbres, en famille, entre amis ou collègues, dans un cadre exceptionnel. Restauration sur place.',
                'Lantic Parc Aventure est situé en pleine nature, au bord des étangs de Lantic, dans un espace de 3,6 hectares. L''accrobranche est une activité de plein-air qui consiste à grimper à la cime des arbres et se balader d''arbre en arbre au travers de différents obstacles, toujours plus funs les uns que les autres (tyroliennes, ponts de singe, rondins tournants, filets, passerelles, …).',
                '1:00:',
                12,
                'Nous vous proposons un parcours d''accrobranche'
            )
        returning
            id
    )
insert into
    _tags (id_offre, tag)
values
    ((table id_offre), 'famille'),
    ((table id_offre), 'plein air'),
    ((table id_offre), 'aventure');

-- Accrobranche au parc aventure Indian Forest
with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, nom_voie)
        values
            ('22', 154, 'Les Tronchées')
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
                age_requis,
                prestations_incluses
            )
        values
            (
                (table id_adresse),
                2,
                41,
                'gratuit',
                'https://www.aventure-nature.com/accrobranche',
                'Accrobranche au parc aventure Indian Forest',
                'le parc aventure Indian Forest - Parcours d''accrobranche dans les Côtes-d''Armor (22)',
                'Envie de pratiquer un sport ludique en pleine nature ? Nous vous proposons de découvrir l''accrobranche, un sport original adapté aux petits et grands. L''équipe du parc dispose d''un diplôme d''encadrement OPAH. Ceci pour vous assurer d''agréables moments en toute sécurité. Ils sont à votre service afin de vous faire découvrir cette discipline forte en sensations et en fous rires.

Notre activité est contrôlée par un organisme spécialisé dans la vérification des parcours acrobatiques en hauteur. De plus, des experts forestiers interviennent à chaque saison pour la préservation forestière.

Bienvenue dans notre parc aventure si vous êtes dans les Côtes-d''Armor notamment à Saint-Brieuc, Dinan, Guingamp, Lanvollon, Lannion, Pléneuf-Val-André, Morieux, Lamballe ou Paimpol.',
                '1:10:',
                11,
                'Nous vous proposons un parcours d''accrobranche'
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