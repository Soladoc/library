begin;

set schema 'pact';

-- Bowling l'éclipse

with id_adresse as (
    insert into
        _adresse (code_insee_commune, nom_voie, localite)
    values
        ('22070', 'Rte de Tréguier', 'ZAC le Lion de Saint-Marc')
    returning id
), id_offre as (
    insert into activite (
        id_professionnel, id_image_principale, id_adresse, libelle_abonnement, url_site_web, titre, resume, description_detaillee,
        indication_duree, prestations_incluses
    ) values (
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
    ) returning id
)
insert into _gallerie
    (id_offre, id_image)
values
    ((table id_offre), 30),
    ((table id_offre), 31),
    ((table id_offre), 32),
    ((table id_offre), 33),
    ((table id_offre), 34),
    ((table id_offre), 35);

-- Chasse aux grenouilles dans le Lac du Gourgal

with id_adresse as (
    insert into
        _adresse (code_insee_commune, numero_voie, nom_voie)
    values
        ('22070', 14, 'Rue de l''Eglise')
    returning id
)
insert into activite (
    id_professionnel, id_image_principale, id_adresse, libelle_abonnement, titre, resume, description_detaillee,
    indication_duree, prestations_incluses
) values (
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

with id_adresse as (
    insert into
        _adresse (code_insee_commune, numero_voie, nom_voie)
    values
        ('22186', 1, 'Rue de Belvédère')
    returning id
)
insert into activite (
    id_professionnel, id_image_principale, id_adresse, libelle_abonnement, titre, resume, description_detaillee,
    indication_duree, age_requis, prestations_incluses
) values (
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

with id_adresse as (
    insert into
        _adresse (code_insee_commune, numero_voie, nom_voie)
    values
        ('22278', 18, 'Rue de l''Église')
    returning id
)
insert into activite (
    id_professionnel, id_image_principale, id_adresse, libelle_abonnement, url_site_web, titre, resume, description_detaillee,
    indication_duree, age_requis, prestations_incluses, prestations_non_incluses
) values (
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

with id_adresse as (
    insert into _adresse
        (code_insee_commune, nom_voie)
    values
        ('22360', 'Zone de loisirs Brezillet ouest')
    returning id
), id_offre as (
    insert into activite (
        id_professionnel, id_image_principale, id_adresse, libelle_abonnement, url_site_web, titre, resume, description_detaillee,
        indication_duree, age_requis, prestations_incluses, prestations_non_incluses
    ) values (
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
    ) returning id
)
insert into _gallerie
    (id_offre, id_image)
values
    ((table id_offre), 28),
    ((table id_offre), 19),
    ((table id_offre), 26),
    ((table id_offre), 27);

-- Lantic Parc Aventure

with id_adresse as (
    insert into
        _adresse (code_insee_commune, nom_voie)
    values
        ('22117', 'Les étangs')
    returning id
)
insert into activite (
    id_professionnel, id_image_principale, id_adresse, libelle_abonnement, url_site_web, titre, resume, description_detaillee,
    indication_duree, age_requis, prestations_incluses
) values (
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

with id_adresse as (
    insert into
        _adresse (code_insee_commune, nom_voie)
    values
        ('22154', 'Les Tronchées')
    returning id
)
insert into activite (
    id_professionnel, id_image_principale, id_adresse, libelle_abonnement, url_site_web, titre, resume, description_detaillee,
    indication_duree, age_requis, prestations_incluses
) values (
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