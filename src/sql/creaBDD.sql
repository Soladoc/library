DROP SCHEMA IF EXISTS pact CASCADE;

CREATE SCHEMA pact;

CREATE TABLE pact._departement(
	numero 	CHAR(3)	CONSTRAINT _departement_pk PRIMARY KEY,
	nom 		VARCHAR(24)	NOT NULL unique
);

CREATE TABLE pact._commune(
	code_insee 	CHAR(5)	CONSTRAINT _commune_pk PRIMARY KEY,
	nom 		VARCHAR(47)	NOT NULL,
	numero CHAR(3) not null, 
	constraint _commune_fk_numero foreign key (numero) references pact._departement(numero)
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

create table pact._infocategorie(
  id_infocategorie serial constraint _infocategorie_pk primary key
);

create table pact._inforestauration(
  id_inforestauration integer constraint _inforestauration_pk primary key,
  richesse int check (richesse<=3 and richesse>=1),
  sert_petit_dejeuner boolean default false,
  sert_brunch boolean default false,
  sert_dejeuner boolean default false,
  sert_diner boolean default false,
  sert_boisson boolean default false,
  check (sert_petit_dejeuner or sert_brunch or sert_dejeuner or sert_diner or sert_boisson),
  carte text,
  constraint _inforestauration_fk_id_inforestauration foreign key (id_inforestauration) references pact._infocategorie(id_infocategorie)
);

create table pact._tag_restauration(
  tag varchar(63) constraint _tag_restauration_pk primary key
);

create table pact._info_tag_restauration(
  tag varchar(63),
  id_inforestauration integer,
  constraint _info_tag_restauration_pk primary key (tag,id_inforestauration),
  constraint _info_tag_restauration_fk_tag foreign key (tag) references pact._tag_restauration(tag),
  constraint _info_tag_restauration_fk_id_inforestauration foreign key (id_inforestauration) references pact._inforestauration(id_inforestauration)
);

create table pact._infoactivite(
  id_infoactivite integer constraint _infoactivite_pk primary key,
  indication_duree varchar(255),
  age_requis int,
  prestations_incluses text,
  prestations_non_incluses text,
  constraint _infoactivite_fk_id_infoactivite foreign key (id_infoactivite) references pact._infocategorie(id_infocategorie)
);

create table pact._infovisite(
  id_infovisite integer constraint _infovisite_pk primary key,
  indication_duree varchar(255),
  constraint _infovisite_fk_id_infovisite foreign key (id_infovisite) references pact._infocategorie(id_infocategorie)
);

create table pact._langue(
  iso639_1 char(2) constraint _langue_pk primary key
);

create table pact._langue_visite(
  langue char(2),
  id_visite integer,
  constraint _langue_visite_pk primary key (langue,id_visite),
  constraint _langue_visite_fk_langue foreign key (langue) references pact._langue(iso639_1),
  constraint _langue_visite_fk_id_visite foreign key (id_visite) references pact._infovisite(id_infovisite)
);

create table pact._infospectacle(
  id_infospectacle integer constraint _infospectacle_pk primary key,
  indication_duree varchar(255),
  capacite_acceuil int,
  constraint _infospectacle_fk_id_infospectacle foreign key (id_infospectacle) references pact._infocategorie(id_infocategorie)
);

create table pact._image(
  id_image serial constraint _image_pk primary key,
  legende varchar(255),
  taille int
);

create table pact._infoparcattraction(
  id_infoparcattraction integer constraint _infoparcattraction_pk primary key,
  plan_parc integer,
  constraint _infoparcattraction_fk_id_infoparcattraction foreign key (id_infoparcattraction) references pact._infocategorie(id_infocategorie),
  constraint _infoparcattraction_fk_plan_parc foreign key (plan_parc) references pact._image(id_image)
);

create table pact._offre(
  id_offre serial constraint _offre_pk primary key,
  titre varchar(255),
  resume varchar(1023),
  description_detaille text not null,
  url_site_web varchar(2047),
  date_derniere_maj timestamp,
  id_categorie integer,
  adresse integer,
  photoprincipale integer,
  constraint _offre_fk_id_categorie foreign key (id_categorie) references pact._infocategorie(id_infocategorie),
  constraint _offre_fk_adresse foreign key (adresse) references pact._adresse(id_adresse),
  constraint _offre_fk_image foreign key (photoprincipale) references pact._image(id_image)
);

create table pact._professionnel(
  id_professionnel serial constraint _professionnel_pk primary key,
  denomination varchar(255)
);

create table pact._prive(
  siren char(9) constraint _prive_pk primary key,
  id_pro integer,
  constraint _prive_fk_id_pro foreign key (id_pro) references pact._professionnel(id_professionnel)
);

create table pact._public(
  id_public integer constraint _public_pk primary key,
  constraint _public_fk_id_public foreign key (id_public) references pact._professionnel(id_professionnel)
);

create table pact._offregratuite(
  id_offregratuite integer constraint _offregratuite_pk primary key,
  id_public integer,
  constraint _offregratuite_fk_id_offregratuite foreign key (id_offregratuite) references pact._offre(id_offre),
  constraint _offregratuite_fk_id_public foreign key (id_public) references pact._public(id_public)
);

create table pact._offrepayante(
  id_offrepayante integer constraint _offrepayante_pk primary key,
  constraint _offrepayante_fk_id_offrepayante foreign key (id_offrepayante) references pact._offre(id_offre)
);

create table pact._offrepremium(
  id_offrepremium integer constraint _offrepremium_pk primary key,
  constraint _offrepremium_fk_id_offrepremium foreign key (id_offrepremium) references pact._offrepayante(id_offrepremium)
);
