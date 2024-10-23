DROP SCHEMA IF EXISTS pact CASCADE;

CREATE SCHEMA pact;

CREATE TABLE pact._departement(
	numero 	CHAR(3)	CONSTRAINT _departement_pk PRIMARY KEY,
	nom 		VARCHAR(24)	NOT NULL unique
);

CREATE TABLE pact._commune(
	code_insee 	CHAR(5)	CONSTRAINT _commune_pk PRIMARY KEY,
	nom 		VARCHAR(47)	NOT NULL,
	numero_dep CHAR(3) not null, 
	constraint _commune_fk_numero_dep foreign key (numero_dep) references pact._departement(numero)
);

create table pact._adresse(
  id_adresse serial constraint _adresse_pk primary key,
  numero_voie int,
  complement_numero varchar(10),
  nom_voie varchar(255),
  localite varchar(255),
  code_postal char(5),
  precision_int varchar(255),
  precision_ext varchar(255),
  latitude decimal,
  longitude decimal,
  code_insee char(5),
  constraint _adresse_fk_code_insee foreign key (code_insee) references pact._commune(code_insee)
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
CREATE TABLE pact._identite
(
  id_identite   SERIAL CONSTRAINT _identite_pk PRIMARY KEY
);
CREATE TABLE pact._compte
(
  email         VARCHAR(319) CONSTRAINT _compte_pk PRIMARY KEY,
  mdp_hash      varchar(255),
  nom           VARCHAR(255),
  prenom        VARCHAR(255),
  telephone     CHAR(10),
  id_identite   INTEGER,
  CONSTRAINT _compte_fk_id_identite FOREIGN KEY (id_identite) REFERENCES pact._identite (id_identite)
);
CREATE TABLE pact._professionnel
(
  id_professionnel   SERIAL CONSTRAINT _professionnel_pk PRIMARY KEY,
  denomination       VARCHAR(255),
  email              VARCHAR(319),
  existe      BOOL,
  CONSTRAINT _professionnel_fk_email FOREIGN KEY (email) REFERENCES pact._compte (email)
);
create table pact._offre(
  id_offre serial constraint _offre_pk primary key,
  titre varchar(255),
  resume varchar(1023),
  description_detaille text not null,
  url_site_web varchar(2047),
  date_derniere_maj timestamp default now(),
  id_categorie integer,
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
  constraint _changement_etat_pk primary key (id_offre,date_changement),
  constraint _changement_etat_fk_id_offre foreign key (id_offre) references pact._offre(id_offre)
);

create table pact._restaurant(
  id_restaurant integer constraint _restaurant_pk primary key,
  richesse int check (richesse<=3 and richesse>=1),
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
  constraint _tag_restaurant_pk primary key (tag,id_restaurant),
  constraint _tag_restaurant_fk_tag foreign key (tag) references pact._tag_restauration(tag),
  constraint _tag_restaurant_fk_id_restaurant foreign key (id_restaurant) references pact._restaurant(id_restaurant)
);

CREATE TABLE pact._activite
(
  id_activite                INTEGER CONSTRAINT _activite_pk PRIMARY KEY,
  indication_duree           INTERVAL,
  age_requis                 INT,
  prestations_incluses       TEXT NOT NULL,
  prestations_non_incluses   TEXT,
  CONSTRAINT _activite_fk_id_activite FOREIGN KEY (id_activite) REFERENCES pact._offre (id_offre)
);
CREATE TABLE pact._visite
(
  id_visite          INTEGER CONSTRAINT _visite_pk PRIMARY KEY,
  indication_duree   INTERVAL,
  CONSTRAINT _restaurant_fk_id_visite FOREIGN KEY (id_visite) REFERENCES pact._offre (id_offre)
);
CREATE TABLE pact._langue
(
  iso639_1   CHAR(2) CONSTRAINT _langue_pk PRIMARY KEY,
  libelle    VARCHAR(63)
);
CREATE TABLE pact._langue_visite
(
  langue      CHAR(2),
  id_visite   INTEGER,
  CONSTRAINT _langue_visite_pk PRIMARY KEY (langue,id_visite),
  CONSTRAINT _langue_visite_fk_langue FOREIGN KEY (langue) REFERENCES pact._langue (iso639_1),
  CONSTRAINT _langue_visite_fk_id_visite FOREIGN KEY (id_visite) REFERENCES pact._visite (id_visite)
);
CREATE TABLE pact._spectacle
(
  id_spectacle       INTEGER CONSTRAINT _spectacle_pk PRIMARY KEY,
  indication_duree   INTERVAL,
  capacite_acceuil   INT,
  CONSTRAINT _spectacle_fk_id_spectacle FOREIGN KEY (id_spectacle) REFERENCES pact._offre (id_offre)
);
CREATE TABLE pact._parcattraction
(
  id_parcattraction   INTEGER CONSTRAINT _parcattraction_pk PRIMARY KEY,
  plan_parc           INTEGER,
  CONSTRAINT _parcattraction_fk_plan_parc FOREIGN KEY (plan_parc) REFERENCES pact._image (id_image),
  CONSTRAINT _parcattraction_fk_id_parcattraction FOREIGN KEY (id_parcattraction) REFERENCES pact._offre (id_offre)
);
CREATE TABLE pact._gallerie
(
  id_offre   INTEGER,
  id_image   INTEGER,
  CONSTRAINT _gallerie_pk PRIMARY KEY (id_offre,id_image),
  CONSTRAINT _gallerie_fk_id_offre FOREIGN KEY (id_offre) REFERENCES pact._offre (id_offre),
  CONSTRAINT _gallerie_fk_id_image FOREIGN KEY (id_image) REFERENCES pact._image (id_image)
);
CREATE TABLE pact._visiteur
(
  ip            INT CONSTRAINT visiteur_pk PRIMARY KEY,
  id_identite   INTEGER,
  CONSTRAINT _visiteur_fk_id_identite FOREIGN KEY (id_identite) REFERENCES pact._identite (id_identite)
);
CREATE TABLE pact._prive
(
  id_prive   INTEGER CONSTRAINT _prive_pk PRIMARY KEY,
  siren      CHAR(9) UNIQUE,
  CONSTRAINT _prive_fk_id_prive FOREIGN KEY (id_prive) REFERENCES pact._professionnel (id_professionnel)
);
CREATE TABLE pact._moyenpaiement
(
  id_moyenpaiement   SERIAL CONSTRAINT _moyenpaiement_pk PRIMARY KEY,
  siren              CHAR(9),
  CONSTRAINT _moyenpaiement_fk_siren FOREIGN KEY (siren) REFERENCES pact._prive (siren)
);
CREATE TABLE pact._public
(
  id_public   INTEGER CONSTRAINT _public_pk PRIMARY KEY,
  CONSTRAINT _public_fk_id_public FOREIGN KEY (id_public) REFERENCES pact._professionnel (id_professionnel)
);
CREATE TABLE pact._membre
(
  id_membre   SERIAL CONSTRAINT _membre_pk PRIMARY KEY,
  pseudo      VARCHAR(63) UNIQUE,
  existe      BOOL,
  email       VARCHAR(319),
  CONSTRAINT _membre_fk_email FOREIGN KEY (email) REFERENCES pact._compte (email)
);
CREATE TABLE pact._signalement
(
  id_membre       INTEGER,
  id_signalable   INTEGER,
  raison          VARCHAR(2047),
  CONSTRAINT _signalement_pk PRIMARY KEY (id_membre,id_signalable),
  CONSTRAINT _signalement_fk_id_membre FOREIGN KEY (id_membre) REFERENCES pact._membre (id_membre),
  CONSTRAINT _signalement_fk_id_signalable FOREIGN KEY (id_signalable) REFERENCES pact._signalable (id_signalable)
);
CREATE TABLE pact._facture
(
  id_facture           SERIAL CONSTRAINT _facture_pk PRIMARY KEY,
  date_facture         TIMESTAMP,
  remise_ht            DECIMAL,
  montant_deja_verse   DECIMAL,
  id_offre             INTEGER,
  CONSTRAINT _facture_fk_id_offre FOREIGN KEY (id_offre) REFERENCES pact._offre (id_offre)
);
CREATE TABLE pact._prestation
(
  id_prestation      SERIAL CONSTRAINT _prestation_pk PRIMARY KEY,
  description        VARCHAR(255),
  prix_unitaire_ht   DECIMAL,
  tva                DECIMAL,
  qte                INT,
  id_facture         INTEGER,
  CONSTRAINT _prestation_fk_id_facture FOREIGN KEY (id_facture) REFERENCES pact._facture (id_facture)
);
CREATE TABLE pact._tarif
(
  denomination   VARCHAR(255) CONSTRAINT _tarif_pk PRIMARY KEY,
  prix           DECIMAL
);
CREATE TABLE pact._option
(
  nom    CHAR(10) CONSTRAINT _option_pk PRIMARY KEY,
  prix   DECIMAL
);
CREATE TABLE pact._souscriptionoption
(
  id_offre           INTEGER CONSTRAINT _souscriptionoption_pk PRIMARY KEY,
  nom_souscription   CHAR(10),
  CONSTRAINT _souscriptionoption_fk_id_offre FOREIGN KEY (id_offre) REFERENCES pact._offre (id_offre),
  CONSTRAINT _souscriptionoption_fk_nom_souscription FOREIGN KEY (nom_souscription) REFERENCES pact._option (nom)
);
CREATE TABLE pact._avis
(
  auteur               VARCHAR(63),
  offre                INTEGER,
  commentaire          VARCHAR(2047),
  note                 INT CHECK (note >= 1 AND note <= 5),
  moment_publication   TIMESTAMP DEFAULT NOW(),
  date_experience      DATE,
  lu                   BOOL DEFAULT FALSE,
  id_signalable        INTEGER,
  blackliste           BOOL DEFAULT FALSE,
  CONSTRAINT _avis_pk PRIMARY KEY (auteur,offre),
  CONSTRAINT _avis_fk_auteur FOREIGN KEY (auteur) REFERENCES pact._membre (pseudo),
  CONSTRAINT _avis_fk_offre FOREIGN KEY (offre) REFERENCES pact._offre (id_offre),
  CONSTRAINT _offre_fk_id_signalable FOREIGN KEY (id_signalable) REFERENCES pact._signalable (id_signalable)
);
CREATE TABLE pact._approuve
(
  id_identite   INTEGER,
  auteur        VARCHAR(63),
  offre         INTEGER,
  CONSTRAINT _approuve_fk_id_identite FOREIGN KEY (id_identite) REFERENCES pact._identite (id_identite),
  CONSTRAINT _approuve_fk_auteur_offre FOREIGN KEY (auteur,offre) REFERENCES pact._avis (auteur,offre)
);
CREATE TABLE pact._desapprouve
(
  id_identite   INTEGER,
  auteur        VARCHAR(63),
  offre         INTEGER,
  CONSTRAINT _desapprouve_fk_id_identite FOREIGN KEY (id_identite) REFERENCES pact._identite (id_identite),
  CONSTRAINT _desapprouve_fk_auteur_offre FOREIGN KEY (auteur,offre) REFERENCES pact._avis (auteur,offre)
);
CREATE TABLE pact._avis_resto
(
  auteur              VARCHAR(63),
  offre               INTEGER,
  note_cuisine        INT CHECK (note_cuisine >= 1 AND note_cuisine <= 5),
  note_service        INT CHECK (note_service >= 1 AND note_service <= 5),
  note_ambiance       INT CHECK (note_ambiance >= 1 AND note_ambiance <= 5),
  note_qualite_prix   INT CHECK (note_qualite_prix >= 1 AND note_qualite_prix <= 5),
  CONSTRAINT _avis_resto_pk PRIMARY KEY (auteur,offre),
  CONSTRAINT _avis_resto_fk_auteur_offre FOREIGN KEY (auteur,offre) REFERENCES pact._avis (auteur,offre),
  CONSTRAINT _avis_resto_fk_offre_2 FOREIGN KEY (offre) REFERENCES pact._restaurant (id_restaurant)
);
CREATE TABLE pact._reponse
(
  id_reponse      SERIAL CONSTRAINT _reponse_pk PRIMARY KEY,
  contenu         VARCHAR(2047),
  auteur          VARCHAR(63),
  offre           INTEGER,
  id_signalable   INTEGER,
  CONSTRAINT _reponse_fk_auteur_offre FOREIGN KEY (auteur,offre) REFERENCES pact._avis (auteur,offre),
  CONSTRAINT _reponse_fk_id_signalable FOREIGN KEY (id_signalable) REFERENCES pact._signalable (id_signalable)
);

