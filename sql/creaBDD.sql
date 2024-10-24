drop schema if exists pact cascade;

create schema pact;

create table pact._departement(
    numero char(3) constraint _departement_pk primary key,
    nom varchar(24) not null unique
);

create table pact._commune(
    code_insee char(5) not null,
    nom varchar(47) not null,
    numero_dep char(3) not null,
    code_postal char(5) not null,
    constraint _commune_pk primary key (code_insee, code_postal), -- une commune peut avoir plusieurs codes postaux (ex. Rangiroa)
    constraint _commune_fk_numero_dep foreign key (numero_dep) references pact._departement(numero)
);

create table pact._adresse(
    id_adresse serial constraint _adresse_pk primary key,
    numero_voie int,
    complement_numero varchar(10),
    nom_voie varchar(255),
    localite varchar(255),
    precision_int varchar(255),
    precision_ext varchar(255),
    latitude decimal,
    longitude decimal,
    commune_code_insee char(5) not null,
    commune_code_postal char(5) not null,
    check ((latitude is null) =(longitude is null)),
    constraint _adresse_fk_code_insee foreign key (commune_code_insee, commune_code_postal) references pact._commune(code_insee, code_postal)
);

create table pact._abonnement(
    libelle varchar(63) constraint _abonnement_pk primary key,
    prix decimal
);

create table pact._image(
    id_image serial constraint _image_pk primary key,
    legende varchar(255),
    taille int
);

create table pact._signalable(
    id_signalable serial constraint _signalable_pk primary key
);

create table pact._identite(
    id_identite serial constraint _identite_pk primary key
);

create table pact._compte(
    email varchar(319) constraint _compte_pk primary key,
    mdp_hash varchar(255),
    nom varchar(255),
    prenom varchar(255),
    telephone char(10),
    id_identite integer,
    existe bool,
    constraint _compte_fk_id_identite foreign key (id_identite) references pact._identite(id_identite)
);

create table pact._professionnel(
    id_professionnel serial constraint _professionnel_pk primary key,
    denomination varchar(255),
    email varchar(319),
    constraint _professionnel_fk_email foreign key (email) references pact._compte(email)
);

create table pact._offre(
    id_offre serial constraint _offre_pk primary key,
    titre varchar(255),
    resume varchar(1023),
    description_detaille text not null,
    url_site_web varchar(2047),
    date_derniere_maj timestamp default now(),
    adresse integer,
    photoprincipale integer,
    abonnement varchar(63),
    id_signalable integer,
    id_professionnel integer,
    constraint _offre_fk_adresse foreign key (adresse) references pact._adresse(id_adresse),
    constraint _offre_fk_image foreign key (photoprincipale) references pact._image(id_image),
    constraint _offre_fk_abonnement foreign key (abonnement) references pact._abonnement(libelle),
    constraint _offre_fk_id_signalable foreign key (id_signalable) references pact._signalable(id_signalable),
    constraint _offre_fk_id_professionnel foreign key (id_professionnel) references pact._professionnel(id_professionnel)
);

create table pact._changement_etat(
    id_offre integer,
    date_changement timestamp default now(),
    constraint _changement_etat_pk primary key (id_offre, date_changement),
    constraint _changement_etat_fk_id_offre foreign key (id_offre) references pact._offre(id_offre)
);

create table pact._restaurant(
    id_restaurant integer constraint _restaurant_pk primary key,
    richesse int check (richesse <= 3 and richesse >= 1),
    sert_petit_dejeuner boolean default false,
    sert_brunch boolean default false,
    sert_dejeuner boolean default false,
    sert_diner boolean default false,
    sert_boisson boolean default false,
    check (sert_petit_dejeuner or sert_brunch or sert_dejeuner or sert_diner or sert_boisson),
    carte text,
    constraint _restaurant_fk_id_restaurant foreign key (id_restaurant) references pact._offre(id_offre)
);

create table pact._tag_restauration(
    tag varchar(63) constraint _tag_restauration_pk primary key
);

create table pact._tag_restaurant(
    tag varchar(63),
    id_restaurant integer,
    constraint _tag_restaurant_pk primary key (tag, id_restaurant),
    constraint _tag_restaurant_fk_tag foreign key (tag) references pact._tag_restauration(tag),
    constraint _tag_restaurant_fk_id_restaurant foreign key (id_restaurant) references pact._restaurant(id_restaurant)
);

create table pact._activite(
    id_activite integer constraint _activite_pk primary key,
    indication_duree interval,
    age_requis int,
    prestations_incluses text not null,
    prestations_non_incluses text,
    constraint _activite_fk_id_activite foreign key (id_activite) references pact._offre(id_offre)
);

create table pact._visite(
    id_visite integer constraint _visite_pk primary key,
    indication_duree interval,
    constraint _restaurant_fk_id_visite foreign key (id_visite) references pact._offre(id_offre)
);

create table pact._langue(
    iso639_1 char(2) constraint _langue_pk primary key,
    libelle varchar(63)
);

create table pact._langue_visite(
    langue char(2),
    id_visite integer,
    constraint _langue_visite_pk primary key (langue, id_visite),
    constraint _langue_visite_fk_langue foreign key (langue) references pact._langue(iso639_1),
    constraint _langue_visite_fk_id_visite foreign key (id_visite) references pact._visite(id_visite)
);

create table pact._spectacle(
    id_spectacle integer constraint _spectacle_pk primary key,
    indication_duree interval,
    capacite_acceuil int,
    constraint _spectacle_fk_id_spectacle foreign key (id_spectacle) references pact._offre(id_offre)
);

create table pact._parcattraction(
    id_parcattraction integer constraint _parcattraction_pk primary key,
    plan_parc integer,
    constraint _parcattraction_fk_plan_parc foreign key (plan_parc) references pact._image(id_image),
    constraint _parcattraction_fk_id_parcattraction foreign key (id_parcattraction) references pact._offre(id_offre)
);

create table pact._gallerie(
    id_offre integer,
    id_image integer,
    constraint _gallerie_pk primary key (id_offre, id_image),
    constraint _gallerie_fk_id_offre foreign key (id_offre) references pact._offre(id_offre),
    constraint _gallerie_fk_id_image foreign key (id_image) references pact._image(id_image)
);

create table pact._visiteur(
    ip int constraint visiteur_pk primary key,
    id_identite integer,
    constraint _visiteur_fk_id_identite foreign key (id_identite) references pact._identite(id_identite)
);

create table pact._prive(
    id_prive integer constraint _prive_pk primary key,
    siren char(12) unique,
    constraint _prive_fk_id_prive foreign key (id_prive) references pact._professionnel(id_professionnel)
);

create table pact._moyenpaiement(
    id_moyenpaiement serial constraint _moyenpaiement_pk primary key,
    siren char(12),
    constraint _moyenpaiement_fk_siren foreign key (siren) references pact._prive(siren)
);

create table pact._public(
    id_public integer constraint _public_pk primary key,
    constraint _public_fk_id_public foreign key (id_public) references pact._professionnel(id_professionnel)
);

create table pact._membre(
    id_membre serial constraint _membre_pk primary key,
    pseudo varchar(63) unique,
    email varchar(319),
    constraint _membre_fk_email foreign key (email) references pact._compte(email)
);

create table pact._signalement(
    id_membre integer,
    id_signalable integer,
    raison varchar(2047),
    constraint _signalement_pk primary key (id_membre, id_signalable),
    constraint _signalement_fk_id_membre foreign key (id_membre) references pact._membre(id_membre),
    constraint _signalement_fk_id_signalable foreign key (id_signalable) references pact._signalable(id_signalable)
);

create table pact._facture(
    id_facture serial constraint _facture_pk primary key,
    date_facture timestamp,
    remise_ht DECIMAL,
    montant_deja_verse DECIMAL,
    id_offre integer,
    constraint _facture_fk_id_offre foreign key (id_offre) references pact._offre(id_offre)
);

create table pact._prestation(
    id_prestation serial constraint _prestation_pk primary key,
    description varchar(255),
    prix_unitaire_ht DECIMAL,
    tva DECIMAL,
    qte int,
    id_facture integer,
    constraint _prestation_fk_id_facture foreign key (id_facture) references pact._facture(id_facture)
);

create table pact._tarif(
    denomination varchar(255) constraint _tarif_pk primary key,
    prix DECIMAL
);

create table pact._option(
    nom char(10) constraint _option_pk primary key,
    prix DECIMAL
);

create table pact._souscriptionoption(
    id_offre integer constraint _souscriptionoption_pk primary key,
    nom_souscription char(10),
    constraint _souscriptionoption_fk_id_offre foreign key (id_offre) references pact._offre(id_offre),
    constraint _souscriptionoption_fk_nom_souscription foreign key (nom_souscription) references pact._option(nom)
);

create table pact._avis(
    auteur varchar(63),
    offre integer,
    commentaire varchar(2047),
    note int check (note >= 1 and note <= 5),
    moment_publication timestamp default now(),
    date_experience date,
    lu bool default false,
    id_signalable integer,
    blackliste bool default false,
    constraint _avis_pk primary key (auteur, offre),
    constraint _avis_fk_auteur foreign key (auteur) references pact._membre(pseudo),
    constraint _avis_fk_offre foreign key (offre) references pact._offre(id_offre),
    constraint _offre_fk_id_signalable foreign key (id_signalable) references pact._signalable(id_signalable)
);

create table pact._approuve(
    id_identite integer,
    auteur varchar(63),
    offre integer,
    constraint _approuve_fk_id_identite foreign key (id_identite) references pact._identite(id_identite),
    constraint _approuve_fk_auteur_offre foreign key (auteur, offre) references pact._avis(auteur, offre)
);

create table pact._desapprouve(
    id_identite integer,
    auteur varchar(63),
    offre integer,
    constraint _desapprouve_fk_id_identite foreign key (id_identite) references pact._identite(id_identite),
    constraint _desapprouve_fk_auteur_offre foreign key (auteur, offre) references pact._avis(auteur, offre)
);

create table pact._avis_resto(
    auteur varchar(63),
    offre integer,
    note_cuisine int check (note_cuisine >= 1 and note_cuisine <= 5),
    note_service int check (note_service >= 1 and note_service <= 5),
    note_ambiance int check (note_ambiance >= 1 and note_ambiance <= 5),
    note_qualite_prix int check (note_qualite_prix >= 1 and note_qualite_prix <= 5),
    constraint _avis_resto_pk primary key (auteur, offre),
    constraint _avis_resto_fk_auteur_offre foreign key (auteur, offre) references pact._avis(auteur, offre),
    constraint _avis_resto_fk_offre_2 foreign key (offre) references pact._restaurant(id_restaurant)
);

create table pact._reponse(
    id_reponse serial constraint _reponse_pk primary key,
    contenu varchar(2047),
    auteur varchar(63),
    offre integer,
    id_signalable integer,
    constraint _reponse_fk_auteur_offre foreign key (auteur, offre) references pact._avis(auteur, offre),
    constraint _reponse_fk_id_signalable foreign key (id_signalable) references pact._signalable(id_signalable)
);

