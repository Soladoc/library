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
comment on table _image is 'Images associées aux livres.';

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
    nom_image int unique
        constraint livre_fk_image references _image(id),  -- image optionnelle
    numero_compte int
        constraint livre_fk_compte references _compte(numero_compte)  -- lien vers un compte
);

comment on table _livre is 'Livres avec titre, auteurs, image optionnelle, note et lien vers un compte.';


create table if not exists _log_export (
    id serial primary key,
    contenu text not null,
    cree_le timestamp default now()
);

create table _avis (
    id serial
        constraint avis_pk primary key,             -- identifiant unique de l'avis
    titre_avis varchar(255),                        -- titre ou résumé de l'avis
    commentaire text,                               -- texte libre de l'avis
    note int not null
        check (note between 0 and 5),               -- note globale
    note_ecriture int check (note_ecriture between 0 and 5),   -- sous-note écriture
    note_intrigue int check (note_intrigue between 0 and 5),   -- sous-note intrigue
    note_personnages int check (note_personnages between 0 and 5), -- sous-note personnages
    id_livre int not null
        constraint avis_fk_livre references _livre(id)
        on delete cascade,                          -- si un livre est supprimé → supprime l'avis
    constraint avis_unique_livre unique (id_livre)  -- un seul avis par livre
);

comment on table _avis is 'Avis unique pour chaque livre, avec une note globale, des sous-notes et un commentaire.';
comment on column _avis.titre_avis is 'Titre ou résumé court de l''avis.';
comment on column _avis.commentaire is 'Commentaire détaillé de l''avis.';
comment on column _avis.note is 'Note globale (0 à 5).';
comment on column _avis.note_ecriture is 'Sous-note sur la qualité de l''écriture (0 à 5).';
comment on column _avis.note_intrigue is 'Sous-note sur la qualité de l''intrigue (0 à 5).';
comment on column _avis.note_personnages is 'Sous-note sur la profondeur des personnages (0 à 5).';
comment on column _avis.id_livre is 'Référence unique vers le livre concerné.';
