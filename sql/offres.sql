begin;

set schema 'pact';

-- Celtic Legends journée 2026
with
    id_adresse as (
        insert into
            _adresse (code_insee_commune, localite, precision_ext)
        values
            ('22360', 'Espace Brezillet', 'PARC EXPO BREZILLET')
        returning
            id
    )
insert into
    pact.spectacle (
        indication_duree,
        capacite_acceuil,
        id_adresse,
        id_image_principale,
        libelle_abonnement,
        id_professionnel,
        titre,
        resume,
        description_detaillee,
        date_derniere_maj,
        url_site_web,
        en_ligne,
        note_moyenne,
        categorie
    )
values
    (
        (table id_adresse),
        '0:20:',
        60,
        id_image_principale_value,
        libelle_abonnement_value,
        id_professionnel_value,
        titre_value,
        resume_value,
        description_detaillee_value,
        date_derniere_maj_value,
        'https://kerlabo-kart.com',
        en_ligne_value,
        note_moyenne_value,
        categorie_value
    );

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