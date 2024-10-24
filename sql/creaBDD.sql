-- NE PAS FORMATER
-- j'ai passé trop de temps à faire ça manuellement
--                                          Raphaël

drop schema if exists pact cascade;

create schema pact;

set schema 'pact';

create table _departement(
    numero char(3)
        constraint departement_pk primary key,
    nom varchar(24) not null unique
);

create table _commune(
    code_insee char(5),
    code_postal char(5),
    constraint commune_pk primary key (code_insee, code_postal), -- une commune peut avoir plusieurs codes postaux (ex. Rangiroa)

    nom varchar(47) not null,
    numero_dep char(3)
        constraint commune_fk_departement references _departement(numero)
);

create table _adresse(
    id_adresse serial
        constraint adresse_pk primary key,
    numero_voie int default null,
    complement_numero varchar(10) not null default '',
    nom_voie varchar(255) not null default '',
    localite varchar(255) not null default '',
    
    latitude decimal,
    longitude decimal,
    check ((latitude is null) = (longitude is null)),
    
    commune_code_insee char(5) not null,
    commune_code_postal char(5) not null,
    constraint adresse_fk_commune foreign key (commune_code_insee, commune_code_postal) references _commune(code_insee, code_postal),

    precision_int varchar(255) not null default '',
    precision_ext varchar(255) not null default ''
);

create table _abonnement(
    libelle varchar(63)
        constraint abonnement_pk primary key,
    prix decimal not null
);

create table _image(
    id_image serial
        constraint image_pk primary key,
    taille int not null,
    legende varchar(255)
);

create table _signalable(
    id_signalable serial
        constraint signalable_pk primary key
);

create table _identite(
    id_identite serial
        constraint identite_pk primary key
);

create table _compte(
    email varchar(319)
        constraint compte_pk primary key,
    mdp_hash varchar(255) not null,
    nom varchar(255) not null,
    prenom varchar(255) not null,
    telephone char(10) not null,
    existe bool not null default true,
    id_identite integer unique
        constraint compte_inherits_identite references _identite(id_identite)
);

create table _professionnel(
    id_professionnel serial
        constraint professionnel_pk primary key,
    denomination varchar(255) not null,
    email varchar(319) not null
        constraint professionnel_inherits_compte references _compte(email)
);

create table _offre(
    id_offre serial constraint offre_pk primary key,
    titre varchar(255) not null,
    resume varchar(1023) not null,
    description_detaille text not null,
    date_derniere_maj timestamp not null default now(),
    adresse integer
        constraint offre_fk_adresse references _adresse(id_adresse),
    photoprincipale integer
        constraint offre_fk_image references _image(id_image),
    abonnement varchar(63)
        constraint offre_fk_abonnement references _abonnement(libelle),
    id_professionnel integer
        constraint offre_fk_professionnel references _professionnel(id_professionnel),
    id_signalable integer unique
        constraint offre_inherits_signalable references _signalable(id_signalable),
    url_site_web varchar(2047)
);

create table _changement_etat(
    id_offre integer
        constraint changement_etat_fk_offre references _offre(id_offre),
    date_changement timestamp default now(),
    constraint changement_etat_pk primary key (id_offre, date_changement)
);

create table _restaurant(
    id_restaurant integer
        constraint restaurant_pk primary key
        constraint restaurant_inherits_offre references _offre(id_offre),
    carte text not null,
    richesse int check (richesse <= 3 and richesse >= 1),

    sert_petit_dejeuner boolean default false,
    sert_brunch boolean default false,
    sert_dejeuner boolean default false,
    sert_diner boolean default false,
    sert_boisson boolean default false,
    check (sert_petit_dejeuner or sert_brunch or sert_dejeuner or sert_diner or sert_boisson)
);

create table _tag_restauration(
    tag varchar(63)
        constraint tag_restauration_pk primary key
);

create table _tag_restaurant(
    tag varchar(63)
        constraint tag_restaurant_fk_tag_restauration references _tag_restauration(tag),
    id_restaurant integer
        constraint tag_restaurant_fk_restaurant references _restaurant(id_restaurant),
    constraint tag_restaurant_pk primary key (tag, id_restaurant)
);

create table _activite(
    id_activite integer
        constraint activite_pk primary key
        constraint activite_inherits_offre references _offre(id_offre),
    indication_duree interval not null,
    age_requis int not null default 0,
    prestations_incluses text not null,
    prestations_non_incluses text
);

create table _visite(
    id_visite integer
        constraint visite_pk primary key
        constraint visite_inherits_offre references _offre(id_offre),
    indication_duree interval not null
);

create table _langue(
    iso639_1 char(2)
        constraint langue_pk primary key,
    libelle varchar(63)
);

create table _langue_visite(
    langue char(2)
        constraint langue_visite_fk_langue references _langue(iso639_1),
    id_visite integer
        constraint langue_visite_fk_visite references _visite(id_visite),
    constraint langue_visite_pk primary key (langue, id_visite)
);

create table _spectacle(
    id_spectacle integer
        constraint spectacle_pk primary key
        constraint spectacle_inherits_offre references _offre(id_offre),
    indication_duree interval not null,
    capacite_acceuil int not null
);

create table _parcattraction(
    id_parcattraction integer
        constraint parcattraction_pk primary key
        constraint parcattraction_inherits_offre references _offre(id_offre),
    plan_parc integer
        constraint parcattraction_fk_image__plan_parc references _image(id_image)
);

create table _gallerie(
    id_offre integer
        constraint gallerie_fk_offre references _offre(id_offre),
    id_image integer
        constraint gallerie_fk_image references _image(id_image),
    constraint gallerie_pk primary key (id_offre, id_image)
);

create table _visiteur(
    ip int
        constraint visiteur_pk primary key,
    id_identite integer
        constraint visiteur_inherits_identite references _identite(id_identite)
);

create table _prive(
    id_prive integer
        constraint prive_pk primary key
        constraint prive_inherits_professionnel references _professionnel(id_professionnel),
    siren char(12) unique
);

create table _moyenpaiement(
    id_moyenpaiement serial
        constraint moyenpaiement_pk primary key,
    siren char(12)
        constraint moyenpaiement_fk_prive references _prive(siren)
);

create table _public(
    id_public integer
        constraint public_pk primary key
        constraint public_inherits_profesionnel references _professionnel(id_professionnel)
);

create table _membre(
    id_membre serial
        constraint membre_pk primary key,
    pseudo varchar(63) not null unique,
    email varchar(319) not null
        constraint membre_inherits_compte references _compte(email)
);

create table _signalement(
    id_membre integer
        constraint signalement_fk_membre references _membre(id_membre),
    id_signalable integer
        constraint signalement_fk_signalable references _signalable(id_signalable),
    constraint signalement_pk primary key (id_membre, id_signalable),

    raison varchar(2047) not null
);

create table _facture(
    id_facture serial
        constraint facture_pk primary key,
    date_facture timestamp not null,
    remise_ht decimal not null,
    montant_deja_verse decimal not null,
    id_offre integer
        constraint facture_fk_offre references _offre(id_offre)
);

create table _prestation(
    id_prestation serial
        constraint prestation_pk primary key,
    description varchar(255) not null,
    prix_unitaire_ht decimal not null,
    tva decimal not null,
    qte int not null,
    id_facture integer
        constraint prestation_fk_facture references _facture(id_facture)
);

create table _tarif(
    denomination varchar(255)
        constraint tarif_pk primary key,
    prix decimal not null
);

create table _option(
    nom char(10)
        constraint option_pk primary key,
    prix decimal not null
);

create table _souscriptionoption(
    id_offre integer
        constraint souscriptionoption_pk primary key
        constraint souscriptionoption_fk_offre references _offre(id_offre),
    nom_souscription char(10)
        constraint souscriptionoption_fk_option references _option(nom)
);

create table _avis(
    auteur varchar(63)
        constraint avis_fk_membre__auteur references _membre(pseudo),
    offre integer
        constraint avis_fk_offre references _offre(id_offre),
    constraint avis_pk primary key (auteur, offre),

    commentaire varchar(2047) not null,
    note int not null check (note >= 1 and note <= 5),
    moment_publication timestamp not null default now(),
    date_experience date not null,
    lu bool not null default false,
    blackliste bool not null default false,
    id_signalable integer
        constraint offre_inherits_signalable references _signalable(id_signalable)
);

create table _approuve(
    id_identite integer
        constraint approuve_fk_identite references _identite(id_identite),

    auteur varchar(63),
    offre integer,
    constraint approuve_fk_avis foreign key (auteur, offre) references _avis(auteur, offre)
);

create table _desapprouve(
    id_identite integer
        constraint desapprouve_fk_identite references _identite(id_identite),

    auteur varchar(63),
    offre integer,
    constraint desapprouve_fk_avis foreign key (auteur, offre) references _avis(auteur, offre)
);

create table _avis_resto(
    auteur varchar(63),
    offre integer
        constraint avis_resto_fk_restaurant references _restaurant(id_restaurant),
    constraint avis_resto_pk primary key (auteur, offre),
    -- garantit l'invariant: offre = avis_resto_inherits_avis.offre
    constraint avis_resto_inherits_avis foreign key (auteur, offre) references _avis(auteur, offre),

    note_cuisine int check (note_cuisine >= 1 and note_cuisine <= 5),
    note_service int check (note_service >= 1 and note_service <= 5),
    note_ambiance int check (note_ambiance >= 1 and note_ambiance <= 5),
    note_qualite_prix int check (note_qualite_prix >= 1 and note_qualite_prix <= 5)
);

create table _reponse(
    id_reponse serial
        constraint reponse_pk primary key,
    contenu varchar(2047) not null,
    id_signalable integer unique
        constraint reponse_inherits_signalable references _signalable(id_signalable),

    auteur varchar(63),
    offre integer,
    constraint reponse_fk_offre foreign key (auteur, offre) references _avis(auteur, offre)
);

