begin;

set schema 'pact';

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
                'standard',
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