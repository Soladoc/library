begin;
-- NE PAS FORMATER
-- j'ai passé trop de temps à faire ça manuellement
--                                          Raphaël

-- Info:
-- Ajouter "not null" aux attributs clés étrangères ne faisant pas partie de la clé primaire. La contrainte "references" n'implique pas "not null". La contrainte "primary key" implique "not null unique"


drop schema if exists pact cascade;

create schema pact;

set schema 'pact';

create domain code_commune_insee as char(5);
create domain numero_departement as char(3);
create domain iso639_1 as char(2);
create domain nom_option char(10);
create domain ligne as varchar check (value not like E'%\n%'); -- une ligne not null de texte
create domain paragraphe as varchar; -- un paragraphe not null de texte
create domain numero_telephone as char(10) check (value ~ '^[0-9]+$');
create domain numero_siren as char(9) check (value ~ '^[0-9]+$');

-- CLASSES

create table _departement(
    numero numero_departement
        constraint departement_pk primary key,
    nom ligne not null unique
);

create table _commune(
    code_insee code_commune_insee
        constraint commune_pk primary key,
    numero_departement numero_departement not null
        constraint commune_fk_departement references _departement,
    nom ligne not null
);

create table _adresse(
    id serial
        constraint adresse_pk primary key,
    code_insee_commune code_commune_insee not null
        constraint adresse_fk_commune references _commune,
    numero_voie int not null default 0,
    complement_numero varchar(10) not null default '',
    nom_voie ligne not null default '',
    localite ligne not null default '',
    precision_int paragraphe not null default '',
    precision_ext paragraphe not null default '',

    latitude decimal,
    longitude decimal,
    check ((latitude is null) = (longitude is null))
);

create table _abonnement(
    libelle ligne
        constraint abonnement_pk primary key,
    prix decimal not null
);

create table _image(
    id serial
        constraint image_pk primary key,
    taille int not null,
    mime_subtype varchar(127) not null, -- Mime subtype (part after 'image/'). Used as a file extension.
    legende ligne not null default ''
);

create table _signalable(
    id serial
        constraint signalable_pk primary key
);

create table _identite(
    id serial
        constraint identite_pk primary key
);

create table _compte(
    id int
        constraint compte_pk primary key
        constraint compte_inherits_identite references _identite,
    id_signalable int not null unique
        constraint compte_inherits_signalable references _signalable,
    email varchar(319) not null unique,
    mdp_hash varchar(255) not null,
    nom ligne not null,
    prenom ligne not null,
    telephone numero_telephone not null,
    existe boolean not null default true
);

create table _professionnel(
    id int
        constraint professionnel_pk primary key
        constraint professionnel_inherits_compte references _compte,
    denomination ligne not null
);

create table _offre(
    id int
        constraint offre_pk primary key
        constraint offre_inherits_signalable references _signalable,
    id_adresse int not null
        constraint offre_fk_adresse references _adresse,
    id_image_principale int not null
        constraint offre_fk_image references _image,
    libelle_abonnement ligne not null
        constraint offre_fk_abonnement references _abonnement,
    id_professionnel int not null
        constraint offre_fk_professionnel references _professionnel,
    titre ligne not null,
    resume paragraphe not null,
    description_detaillee paragraphe not null,
    date_derniere_maj timestamp not null default now(),
    url_site_web varchar(2047) not null default ''
);

create table _restaurant(
    id int
        constraint restaurant_pk primary key
        constraint restaurant_inherits_offre references _offre,
    carte paragraphe not null,
    richesse int not null check (1 <= richesse and richesse <= 3),

    sert_petit_dejeuner boolean not null default false,
    sert_brunch boolean not null default false,
    sert_dejeuner boolean not null default false,
    sert_diner boolean not null default false,
    sert_boissons boolean not null default false,
    check (sert_petit_dejeuner or sert_brunch or sert_dejeuner or sert_diner or sert_boissons)
);

create table _activite(
    id int
        constraint activite_pk primary key
        constraint activite_inherits_offre references _offre,
    indication_duree interval not null,
    age_requis int not null default 0,
    prestations_incluses paragraphe not null,
    prestations_non_incluses paragraphe not null default ''
);

create table _visite(
    id int
        constraint visite_pk primary key
        constraint visite_inherits_offre references _offre,
    indication_duree interval not null
);

create table _langue(
    code iso639_1
        constraint langue_pk primary key,
    libelle ligne not null
);

create table _spectacle(
    id int
        constraint spectacle_pk primary key
        constraint spectacle_inherits_offre references _offre,
    indication_duree interval not null,
    capacite_acceuil int not null
); 

create table _parc_attractions(
    id int
        constraint parc_attractions_pk primary key
        constraint parc_attractions_inherits_offre references _offre,
    id_image_plan int not null
        constraint parc_attractions_fk_image_plan_parc references _image
);

create table _visiteur(
    id int
        constraint visiteur_pk primary key
        constraint visiteur_inherits_identite references _identite,
    ip int not null unique
);

create table _prive(
    id int
        constraint prive_pk primary key
        constraint prive_inherits_professionnel references _professionnel,
    siren numero_siren not null unique
);

create table _moyen_paiement(
    id serial
        constraint moyen_paiement_pk primary key,
    id_prive int not null
        constraint moyen_paiement_fk_prive references _prive
);

create table _public(
    id int
        constraint public_pk primary key
        constraint public_inherits_profesionnel references _professionnel
);

create table _membre(
    id int
        constraint membre_pk primary key
        constraint membre_inherits_compte references _compte,
    pseudo ligne not null unique
);

create table _facture(
    id serial
        constraint facture_pk primary key,
    date_facture timestamp not null,
    remise_ht decimal not null,
    montant_deja_verse decimal not null,
    id_offre int not null
        constraint facture_fk_offre references _offre
);

create table _prestation(
    id serial
        constraint prestation_pk primary key,
    description ligne not null,
    prix_unitaire_ht decimal not null,
    tva decimal not null,
    qte int not null,
    id_facture int not null
        constraint prestation_fk_facture references _facture
);

create table _tarif(
    nom_tarif ligne not null,
    id_offre int
        constraint tarif_fk_offre references _offre,
    constraint tarif_pk primary key (nom_tarif, id_offre),

    prix decimal not null
);

create table _option(
    nom nom_option
        constraint option_pk primary key,
    prix decimal not null
);

create table _avis(
    id int
        constraint avis_pk primary key
        constraint avis_inherits_signalable references _signalable,
    commentaire paragraphe not null,
    note int not null check (note >= 1 and note <= 5),
    moment_publication timestamp not null default now(),
    date_experience date not null,
    lu boolean not null default false,
    blackliste boolean not null default false,

    id_membre_auteur int not null
        constraint avis_fk_membre_auteur references _membre,
    id_offre int not null
        constraint avis_fk_offre references _offre,
    constraint avis_uniq_auteur_offre unique (id_membre_auteur, id_offre) -- un seul avis par couple (membre_auteur, offre)
);

create table _avis_resto(
    id int
        constraint avis_resto_pk primary key
        constraint avis_resto_inherits_avis references _avis,
    id_restaurant int not null
        constraint avis_resto_fk_restaurant references _restaurant,
    note_cuisine int not null check (1 <= note_cuisine and note_cuisine <= 5),
    note_service int not null check (1 <= note_service and note_service <= 5),
    note_ambiance int not null check (1 <= note_ambiance and note_ambiance <= 5),
    note_qualite_prix int not null check (1 <= note_qualite_prix and note_qualite_prix <= 5)
);

create table _reponse(
    id int
        constraint reponse_pk primary key
        constraint reponse_inherits_signalable references _signalable,
    id_avis int not null unique
        constraint reponse_avis references _avis,
    contenu paragraphe not null
);


-- ASSOCIATIONS

create table _horaire_ouverture(
    id_offre int
        constraint horaire_ouverture_fk_offre references _offre,
    jour_de_la_semaine int check (1 <= jour_de_la_semaine and jour_de_la_semaine <= 5),
    heure_debut time,
    heure_fin time check (heure_fin > heure_debut),
    constraint horaire_pk primary key (id_offre, jour_de_la_semaine, heure_debut, heure_fin)
);

create table _signalement(
    id_membre int
        constraint signalement_fk_membre references _membre,
    id_signalable int
        constraint signalement_fk_signalable references _signalable,
    constraint signalement_pk primary key (id_membre, id_signalable),

    raison paragraphe not null
);

create table _code_postal(
    code_insee_commune code_commune_insee
        constraint code_postal_fk_commune references _commune,
    code_postal char(5),
    constraint code_postal_pk primary key (code_insee_commune, code_postal)
);

create table _langue_visite(
    code_langue char(2)
        constraint langue_visite_fk_langue references _langue,
    id_visite int
        constraint langue_visite_fk_visite references _visite,
    constraint langue_visite_pk primary key (code_langue, id_visite)
);

create table _gallerie(
    id_offre int
        constraint gallerie_fk_offre references _offre,
    id_image int
        constraint gallerie_fk_image references _image,
    constraint gallerie_pk primary key (id_offre, id_image)
);

create table _changement_etat(
    id_offre int
        constraint changement_etat_fk_offre references _offre,
    date_changement timestamp default now(),
    constraint changement_etat_pk primary key (id_offre, date_changement)
);

create table _souscription_option(
    id_offre int
        constraint souscription_option_pk primary key
        constraint souscription_option_fk_offre references _offre,
    nom_option nom_option not null
        constraint souscription_option_fk_option references _option
);

create table _juge(
    id_identite int
        constraint approuve_fk_identite references _identite,
    id_avis int
        constraint approuve_fk_avis references _avis,
    constraint approuve_pk primary key (id_identite, id_avis),

    aime boolean not null
);

create table _tags_restaurant(
    id_restaurant int
        constraint tag_restaurant_fk_restaurant references _restaurant,
    tag ligne not null,
    constraint tag_restaurant_pk primary key (tag, id_restaurant)
);

commit;