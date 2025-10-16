-- NE PAS FORMATER
-- j'ai passé trop de temps à faire ça manuellement
--                                          Raphaël

-- Info:
-- Ajouter "not null" aux attributs clés étrangères ne faisant pas partie de la clé primaire. La contrainte "references" n'implique pas "not null". La contrainte "primary key" implique "not null unique"

-- CLASSES
drop schema if exists pact cascade;
create schema pact;
set schema 'pact';

create table _image (
    id serial
        constraint image_pk primary key,
    taille int not null,
    mime_subtype varchar(127) not null,
    legende ligne check (legende <> '')
);
comment on column _image.taille is 'Mime subtype (part after "image/"). Used as a file extension.';

create table _compte (
    id int
        constraint compte_pk primary key
        constraint compte_inherits_signalable references _signalable on delete cascade,
    email adresse_email not null unique,
    mdp_hash varchar(255) not null,
    nom ligne not null,
    prenom ligne not null,
    telephone numero_telephone not null,
    id_adresse int not null
        constraint compte_fk_adresse references _adresse,
    api_key uuid unique
);

create table _offre (
    id int
        constraint offre_pk primary key
        constraint offre_inherits_signalable references _signalable on delete cascade,
    id_adresse int not null
        constraint offre_fk_adresse references _adresse,
    id_image_principale int not null
        constraint offre_fk_image references _image,
    id_professionnel int not null
        constraint offre_fk_professionnel references _professionnel,
    libelle_abonnement mot_minuscule not null
        constraint offre_fk_abonnement references _abonnement,
    titre ligne not null,
    resume ligne not null,
    description_detaillee paragraphe not null,
    modifiee_le timestamp not null,
    url_site_web varchar(2047),
    periodes_ouverture tsmultirange not null
);