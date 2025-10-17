-- NE PAS FORMATER
-- j'ai passé trop de temps à faire ça manuellement
--                                          Raphaël

-- Info:
-- Ajouter "not null" aux attributs clés étrangères ne faisant pas partie de la clé primaire. La contrainte "references" n'implique pas "not null". La contrainte "primary key" implique "not null unique"

-- Supprime le schéma s'il existe déjà
drop schema if exists bibliotheque cascade;

-- Création du schéma
create schema bibliotheque;
set schema 'bibliotheque';

-- Table pour les images (optionnelle)
create table _image (
    id serial
        constraint image_pk primary key,
    mime_subtype varchar(127)
);
comment on column _image.mime_subtype is 'Partie après "image/", utilisée comme extension.';

-- Table des comptes utilisateurs
create table _compte (
    numero_compte serial
        constraint compte_pk primary key,  -- clé primaire auto-générée
    email adresse_email not null unique,    -- adresse mail (obligatoire et unique)
    mdp_hash varchar(255) not null         -- mot de passe hashé
);

comment on table _compte is 'Comptes utilisateurs avec adresse mail unique, numéro auto-généré et mot de passe hashé.';

-- Table des livres
create table _livre (
    id serial
        constraint livre_pk primary key,        -- identifiant auto-généré
    titre varchar(255) not null,                -- titre du livre (obligatoire)
    auteurs varchar(255) not null,              -- un ou plusieurs auteurs (obligatoire)
    id_image int unique
        constraint livre_fk_image references _image,  -- image optionnelle
    note numeric(2,1) check (note >= 0 and note <= 10), -- note sur 10 (optionnelle)
    numero_compte int
        constraint livre_fk_compte references _compte(numero_compte)  -- lien vers un compte
);

comment on table _livre is 'Livres avec titre, auteurs, image optionnelle, note et lien vers un compte.';
comment on table _image is 'Images associées aux livres.';

create table if not exists _log_export (
    id serial primary key,
    contenu text not null,
    cree_le timestamp default now()
);
