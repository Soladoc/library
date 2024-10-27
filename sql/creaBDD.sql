begin;
-- NE PAS FORMATER
-- j'ai passé trop de temps à faire ça manuellement
--                                          Raphaël

drop schema if exists pact cascade;

create schema pact;

set schema 'pact';

create domain code_commune_insee as char(5);
create domain numero_departement as char(3);
create domain iso639_1 as char(2);
create domain nom_option char(10);
create domain ligne as varchar not null check (value not like E'%\n%'); -- une ligne de texte
create domain paragraphe as varchar not null; -- un paragraphe de texte
create domain numero_telephone as char(10) check (value ~ '^[0-9]+$');
create domain numero_siren as char(9) check (value ~ '^[0-9]+$');

-- CLASSES

create table _departement(
    id_departement numero_departement
        constraint departement_pk primary key,
    nom ligne unique
);

create table _commune(
    id_commune code_commune_insee
        constraint commune_pk primary key,
    id_departement numero_departement
        constraint commune_fk_departement references _departement(id_departement),
    nom ligne
);

create table _adresse(
    id_adresse serial
        constraint adresse_pk primary key,
    id_commune code_commune_insee
        constraint adresse_fk_commune references _commune(id_commune),
    numero_voie int not null default 0,
    complement_numero varchar(10) not null default '',
    nom_voie ligne default '',
    localite ligne default '',
    precision_int paragraphe default '',
    precision_ext paragraphe default '',

    latitude decimal,
    longitude decimal,
    check ((latitude is null) = (longitude is null))
);

create table _abonnement(
    libelle_abonnement ligne
        constraint abonnement_pk primary key,
    prix decimal not null
);

create table _image(
    id_image serial
        constraint image_pk primary key,
    taille int not null,
    mime_subtype varchar(127) not null, -- Mime subtype (part after 'image/'). Used as a file extension.
    legende ligne default ''
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
    id_compte int primary key
        constraint compte_inherits_identite references _identite(id_identite),
    id_signalable int not null unique
        constraint compte_inherits_signalable references _signalable(id_signalable),
    email varchar(319) not null unique,
    mdp_hash varchar(255) not null,
    nom ligne,
    prenom ligne,
    telephone numero_telephone not null,
    existe boolean not null default true
);

create table _professionnel(
    id_professionnel int
        constraint professionnel_pk primary key
        constraint professionnel_inherits_compte references _compte(id_compte),
    denomination ligne
);

create table _offre(
    id_offre int
        constraint offre_pk primary key
        constraint offre_inherits_signalable references _signalable(id_signalable),
    id_adresse int
        constraint offre_fk_adresse references _adresse(id_adresse),
    id_image_principale int
        constraint offre_fk_image references _image(id_image),
    libelle_abonnement ligne
        constraint offre_fk_abonnement references _abonnement(libelle_abonnement),
    id_professionnel int
        constraint offre_fk_professionnel references _professionnel(id_professionnel),
    titre ligne,
    resume paragraphe,
    description_detaillee paragraphe,
    date_derniere_maj timestamp not null default now(),
    url_site_web varchar(2047) not null default ''
);

create table _restaurant(
    id_restaurant int
        constraint restaurant_pk primary key
        constraint restaurant_inherits_offre references _offre(id_offre),
    carte paragraphe,
    richesse int check (richesse <= 3 and richesse >= 1),

    sert_petit_dejeuner boolean default false,
    sert_brunch boolean default false,
    sert_dejeuner boolean default false,
    sert_diner boolean default false,
    sert_boisson boolean default false,
    check (sert_petit_dejeuner or sert_brunch or sert_dejeuner or sert_diner or sert_boisson)
);

create table _activite(
    id_activite int
        constraint activite_pk primary key
        constraint activite_inherits_offre references _offre(id_offre),
    indication_duree interval not null,
    age_requis int not null default 0,
    prestations_incluses paragraphe,
    prestations_non_incluses paragraphe default ''
);

create table _visite(
    id_visite int
        constraint visite_pk primary key
        constraint visite_inherits_offre references _offre(id_offre),
    indication_duree interval not null
);

create table _langue(
    id_langue iso639_1
        constraint langue_pk primary key,
    libelle ligne
);

create table _spectacle(
    id_spectacle int
        constraint spectacle_pk primary key
        constraint spectacle_inherits_offre references _offre(id_offre),
    indication_duree interval not null,
    capacite_acceuil int not null
); 

create table _parc_attractions(
    id_parc_attractions int
        constraint parc_attractions_pk primary key
        constraint parc_attractions_inherits_offre references _offre(id_offre),
    id_image_plan int
        constraint parc_attractions_fk_image_plan_parc references _image(id_image)
);

create table _visiteur(
    id_visiteur int
        constraint visiteur_pk primary key
        constraint visiteur_inherits_identite references _identite(id_identite),
    ip int not null unique
);

create table _prive(
    id_prive int
        constraint prive_pk primary key
        constraint prive_inherits_professionnel references _professionnel(id_professionnel),
    siren numero_siren not null unique
);

create table _moyen_paiement(
    id_moyen_paiement serial
        constraint moyen_paiement_pk primary key,
    id_prive int
        constraint moyen_paiement_fk_prive references _prive(id_prive)
);

create table _public(
    id_public int
        constraint public_pk primary key
        constraint public_inherits_profesionnel references _professionnel(id_professionnel)
);

create table _membre(
    id_membre int
        constraint membre_pk primary key
        constraint membre_inherits_compte references _compte(id_compte),
    pseudo ligne unique
);

create table _facture(
    id_facture serial
        constraint facture_pk primary key,
    date_facture timestamp not null,
    remise_ht decimal not null,
    montant_deja_verse decimal not null,
    id_offre int
        constraint facture_fk_offre references _offre(id_offre)
);

create table _prestation(
    id_prestation serial
        constraint prestation_pk primary key,
    description ligne,
    prix_unitaire_ht decimal not null,
    tva decimal not null,
    qte int not null,
    id_facture int
        constraint prestation_fk_facture references _facture(id_facture)
);

create table _tarif(
    nom_tarif ligne,
    id_offre int
        constraint tarif_fk_offre references _offre(id_offre),
    constraint tarif_pk primary key (nom_tarif, id_offre),

    prix decimal not null
);

create table _option(
    nom_option nom_option
        constraint option_pk primary key,
    prix decimal not null
);

create table _avis(
    id_avis int
        constraint avis_pk primary key
        constraint avis_inherits_signalable references _signalable(id_signalable),
    commentaire paragraphe,
    note int not null check (note >= 1 and note <= 5),
    moment_publication timestamp not null default now(),
    date_experience date not null,
    lu boolean not null default false,
    blackliste boolean not null default false,

    id_membre_auteur int
        constraint avis_fk_membre_auteur references _membre(id_membre),
    id_offre int
        constraint avis_fk_offre references _offre(id_offre),
    constraint avis_uniq_auteur_offre unique (id_membre_auteur, id_offre) -- un seul avis par couple (membre_auteur, offre)
);

create table _avis_resto(
    id_avis_resto int
        constraint avis_resto_pk primary key
        constraint avis_resto_inherits_avis references _avis(id_avis),
    id_restaurant int
        constraint avis_resto_fk_restaurant references _restaurant(id_restaurant),
    note_cuisine int check (note_cuisine >= 1 and note_cuisine <= 5),
    note_service int check (note_service >= 1 and note_service <= 5),
    note_ambiance int check (note_ambiance >= 1 and note_ambiance <= 5),
    note_qualite_prix int check (note_qualite_prix >= 1 and note_qualite_prix <= 5)
);

create table _reponse(
    id_reponse int
        constraint reponse_pk primary key
        constraint reponse_inherits_signalable references _signalable(id_signalable),
    id_avis int unique
        constraint reponse_avis references _avis(id_avis),
    contenu paragraphe
);

-- ASSOCIATIONS

create table _signalement(
    id_membre int
        constraint signalement_fk_membre references _membre(id_membre),
    id_signalable int
        constraint signalement_fk_signalable references _signalable(id_signalable),
    constraint signalement_pk primary key (id_membre, id_signalable),

    raison paragraphe
);

create table _code_postal(
    id_commune code_commune_insee
        constraint code_postal_fk_commune references _commune(id_commune),
    code_postal char(5),
    constraint code_postal_pk primary key (id_commune, code_postal)
);

create table _langue_visite(
    id_langue char(2)
        constraint langue_visite_fk_langue references _langue(id_langue),
    id_visite int
        constraint langue_visite_fk_visite references _visite(id_visite),
    constraint langue_visite_pk primary key (id_langue, id_visite)
);

create table _gallerie(
    id_offre int
        constraint gallerie_fk_offre references _offre(id_offre),
    id_image int
        constraint gallerie_fk_image references _image(id_image),
    constraint gallerie_pk primary key (id_offre, id_image)
);

create table _changement_etat(
    id_offre int
        constraint changement_etat_fk_offre references _offre(id_offre),
    date_changement timestamp default now(),
    constraint changement_etat_pk primary key (id_offre, date_changement)
);

create table _souscription_option(
    id_offre int
        constraint souscription_option_pk primary key
        constraint souscription_option_fk_offre references _offre(id_offre),
    nom_option nom_option
        constraint souscription_option_fk_option references _option(nom_option)
);

create table _juge(
    id_identite int
        constraint approuve_fk_identite references _identite(id_identite),
    id_avis int
        constraint approuve_fk_avis references _avis(id_avis),
    constraint approuve_pk primary key (id_identite, id_avis),

    aime boolean not null
);

create table _tags_restaurant(
    id_restaurant int
        constraint tag_restaurant_fk_restaurant references _restaurant(id_restaurant),
    tag ligne,
    constraint tag_restaurant_pk primary key (tag, id_restaurant)
);

commit;