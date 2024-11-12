begin;

set schema 'pact';

-- Visite du Fort La Latte - La Roche Goyon
with
    id_adresse as (
        insert into
            _adresse (code_insee_commune, localite)
        values
            ('22201', 'Cap Fréhel')
        returning
            id
    ),
    id_offre as (
        insert into
            visite (
                id_adresse,
                indication_duree,
                id_image_principale,
                libelle_abonnement,
                id_professionnel,
                titre,
                resume,
                description_detaillee,
                url_site_web
            )
        values
            (
                (table id_adresse),
                '3:00:',
                20,
                'gratuit',
                1,
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
    )
insert into
    _horaire_ouverture (id_offre, jour_de_la_semaine, heure_debut, heure_fin)
values
    ((table id_offre), 1, '10:', '18:'),
    ((table id_offre), 2, '10:', '18:'),
    ((table id_offre), 3, '10:', '18:'),
    ((table id_offre), 4, '10:', '18:'),
    ((table id_offre), 5, '10:', '18:'),
    ((table id_offre), 6, '10:', '18:'),
    ((table id_offre), 7, '13:', '18:');

-- Découverte interactive de la cité des Télécoms
with
    id_adresse as (
        insert into
            _adresse (code_insee_commune, localite)
        values
            ('22198', 'Cité des Télécoms')
        returning
            id
    ),
    id_offre as (
        insert into
            visite (
                id_adresse,
                indication_duree,
                id_image_principale,
                libelle_abonnement,
                id_professionnel,
                titre,
                resume,
                description_detaillee,
                url_site_web
            )
        values
            (
                (table id_adresse),
                '3:00:',
                2,
                'gratuit',
                1,
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
    )
insert into
    _horaire_ouverture (id_offre, jour_de_la_semaine, heure_debut, heure_fin)
values
    ((table id_offre), 1, '10:', '18:'),
    ((table id_offre), 2, '10:', '18:'),
    ((table id_offre), 3, '10:', '18:'),
    ((table id_offre), 4, '10:', '18:'),
    ((table id_offre), 5, '10:', '18:'),
    ((table id_offre), 6, '10:', '18:'),
    ((table id_offre), 7, '13:', '18:');

-- Celtic Legends journée 2026
with
    id_adresse as (
        insert into
            _adresse (code_insee_commune, localite, precision_ext)
        values
            ('22360', 'Espace Brezillet', 'PARC EXPO BREZILLET')
        returning
            id
    ),
    id_offre as (
        insert into
            spectacle (
                id_adresse,
                indication_duree,
                capacite_accueil,
                id_image_principale,
                libelle_abonnement,
                id_professionnel,
                titre,
                resume,
                description_detaillee,
                url_site_web
            )
        values
            (
                (table id_adresse),
                '2:00:',
                1000,
                12,
                'gratuit',
                2,
                'Celtic Legends - Tournée 2026',
                'Celtic Legends est un spectacle de musiques et de danses irlandaises qui s’est produit sur de nombreuses scènes à travers le monde depuis sa création, attirant près de 3 millions de spectateurs.',
                'Celtic Legends revient en 2026 avec une nouvelle version du spectacle. Créé à Galway, au Coeur du Connemara, Celtic Legends est un condensé de la culture traditionnelle Irlandaise recréant sur scène l’ambiance électrique d’une soirée dans un pub traditionnel. Venez partager durant 2 heures ce voyage au coeur de l’Irlande soutenu par 5 talentueux musiciens sous la baguette de Sean McCarthy et de 12 extraordinaires danseurs sous la houlette de la créative Jacintha Sharpe.',
                'https://www.celtic-legends.net'
            )
        returning
            id
    )
insert into
    _periode_ouverture (id_offre, debut, fin)
values
    ((table id_offre), '2026-04-10T20:00:00.000Z', '2026-04-11T01:00:00.000Z');

-- Karting Kerlabo
with
    id_adresse as (
        insert into
            _adresse (code_insee_commune, nom_voie)
        values
            ('22045', 'Axe Chatelaudren-Quintin')
        returning
            id
    ),
    id_offre as (
        insert into
            activite (
                id_professionnel,
                id_image_principale,
                age_requis,
                id_adresse,
                libelle_abonnement,
                titre,
                resume,
                description_detaillee,
                url_site_web,
                indication_duree,
                prestations_incluses
            )
        values
            (
                2,
                40,
                7,
                (table id_adresse),
                'gratuit',
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
    )
insert into
    _gallerie (id_offre, id_image)
values
    ((table id_offre), 41),
    ((table id_offre), 42),
    ((table id_offre), 43);

-- Randonnée au Menez Bré
with
    id_adresse as (
        insert into
            _adresse (code_insee_commune, localite)
        values
            ('22164', 'Menez Bré')
        returning
            id
    ),
    id_offre as (
        insert into
            activite (
                id_professionnel,
                id_image_principale,
                id_adresse,
                libelle_abonnement,
                titre,
                resume,
                description_detaillee,
                indication_duree,
                prestations_incluses
            )
        values
            (
                2,
                37,
                (table id_adresse),
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
    )
insert into
    _gallerie (id_offre, id_image)
values
    ((table id_offre), 38),
    ((table id_offre), 39);

-- Bowling l'éclipse
with
    id_adresse as (
        insert into
            _adresse (code_insee_commune, nom_voie, localite)
        values
            ('22070', 'Rte de Tréguier', 'ZAC le Lion de Saint-Marc')
        returning
            id
    ),
    id_offre as (
        insert into
            activite (
                id_professionnel,
                id_image_principale,
                id_adresse,
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
                1,
                36,
                (table id_adresse),
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
    )
insert into
    _gallerie (id_offre, id_image)
values
    ((table id_offre), 30),
    ((table id_offre), 31),
    ((table id_offre), 32),
    ((table id_offre), 33),
    ((table id_offre), 34),
    ((table id_offre), 35);

-- Chasse aux grenouilles dans le Lac du Gourgal
with
    id_adresse as (
        insert into
            _adresse (code_insee_commune, numero_voie, nom_voie)
        values
            ('22070', 14, 'Rue de l''Eglise')
        returning
            id
    )
insert into
    activite (
        id_professionnel,
        id_image_principale,
        id_adresse,
        libelle_abonnement,
        titre,
        resume,
        description_detaillee,
        indication_duree,
        prestations_incluses
    )
values
    (
        1,
        3,
        (table id_adresse),
        'gratuit',
        'Chasse aux grenouilles dans le Lac du Gourgal',
        'Chasse aux grenouilles dans le Lac du Gourgal résumé',
        'Chasse aux grenouilles dans le Lac du Gourgal description',
        '3:00:',
        'Chasse aux grenouilles dans le Lac du Gourgal prestations incluses'
    );

-- Initiation au char à voile sur la plage
with
    id_adresse as (
        insert into
            _adresse (code_insee_commune, numero_voie, nom_voie)
        values
            ('22186', 1, 'Rue de Belvédère')
        returning
            id
    )
insert into
    activite (
        id_professionnel,
        id_image_principale,
        id_adresse,
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
        1,
        7,
        (table id_adresse),
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
    );

with
    id_adresse as (
        insert into
            _adresse (code_insee_commune, numero_voie, nom_voie)
        values
            ('22278', 18, 'Rue de l''Église')
        returning
            id
    )
insert into
    activite (
        id_professionnel,
        id_image_principale,
        id_adresse,
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
        1,
        4,
        (table id_adresse),
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
    );

with
    id_adresse as (
        insert into
            _adresse (code_insee_commune, nom_voie)
        values
            ('22360', 'Zone de loisirs Brezillet ouest')
        returning
            id
    ),
    id_offre as (
        insert into
            activite (
                id_professionnel,
                id_image_principale,
                id_adresse,
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
                1,
                19,
                (table id_adresse),
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
    )
insert into
    _gallerie (id_offre, id_image)
values
    ((table id_offre), 28),
    ((table id_offre), 19),
    ((table id_offre), 26),
    ((table id_offre), 27);

-- Lantic Parc Aventure
with
    id_adresse as (
        insert into
            _adresse (code_insee_commune, nom_voie)
        values
            ('22117', 'Les étangs')
        returning
            id
    )
insert into
    activite (
        id_professionnel,
        id_image_principale,
        id_adresse,
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
        2,
        9,
        (table id_adresse),
        'gratuit',
        'https://www.lanticparcaventure.bzh/',
        'Lantic Parc Aventure',
        'LANTIC PARC AVENTURE propose plusieurs parcours acrobatiques en hauteur, du paint ball, du laser tag, pour toute la famille. Venez passer une journée dans les arbres, en famille, entre amis ou collègues, dans un cadre exceptionnel. Restauration sur place.',
        'Lantic Parc Aventure est situé en pleine nature, au bord des étangs de Lantic, dans un espace de 3,6 hectares. L''accrobranche est une activité de plein-air qui consiste à grimper à la cime des arbres et se balader d''arbre en arbre au travers de différents obstacles, toujours plus funs les uns que les autres (tyroliennes, ponts de singe, rondins tournants, filets, passerelles, …).',
        '1:00:',
        12,
        'Nous vous proposons un parcours d''accrobranche'
    );

-- Accrobranche au parc aventure Indiant Forest
with
    id_adresse as (
        insert into
            _adresse (code_insee_commune, nom_voie)
        values
            ('22154', 'Les Tronchées')
        returning
            id
    )
insert into
    activite (
        id_professionnel,
        id_image_principale,
        id_adresse,
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
        2,
        17,
        (table id_adresse),
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
    );

commit;