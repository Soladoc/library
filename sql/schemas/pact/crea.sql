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
    id serial constraint image_pk primary key,
    mime_subtype varchar(127)
);
comment on table _image is 'Images associées aux livres. L''image sera enregistrée en tant que id.mime_subtype dans le système de fichiers.';

-- Table des comptes utilisateurs
create table _compte (
    numero_compte serial constraint compte_pk primary key,  -- clé primaire auto-générée
    email adresse_email not null unique,    -- adresse mail (obligatoire et unique)
    mdp_hash varchar(255) not null         -- mot de passe hashé (irreversible)
);
comment on table _compte is 'Comptes utilisateurs avec adresse mail unique, numéro auto-généré et mot de passe hashé.';

-- Table des genres (à déclarer avant _livre pour les FK)
create table _genre (
    id serial constraint genre_pk primary key,
    nom varchar(255) not null unique
);
comment on table _genre is 'Genres de livres, chaque nom est unique.';
comment on column _genre.nom is 'Nom du genre, unique.';

-- Table des livres
create table _livre (
    id serial constraint livre_pk primary key,        -- identifiant auto-généré
    titre varchar(255) not null,                      -- titre du livre (obligatoire)
    nom_image int unique constraint livre_fk_image references _image(id),  -- image optionnelle
    numero_compte int constraint livre_fk_compte references _compte(numero_compte),  -- lien vers un compte
    genre_principal int constraint livre_fk_genre_principal references _genre(id),  -- genre principal (obligatoire pour la cote)
    cote varchar(20) not null default ''              -- cote générée automatiquement
);
comment on table _livre is 'Livres avec titre, auteurs, genre principal, cote générée automatiquement, image optionnelle, note et lien vers un compte.';

-- Unicité de la cote pour un compte
create unique index unique_cote_per_compte on _livre(cote, numero_compte);

-- Table du log export
create table if not exists _log_export (
    id serial primary key,
    contenu text not null,
    cree_le timestamp default now()
);

-- Table des avis
create table _avis (
    id serial constraint avis_pk primary key,             -- identifiant unique de l'avis
    titre_avis varchar(255),                              -- titre ou résumé de l'avis
    commentaire text,                                     -- texte libre de l'avis
    note int not null check (note between 0 and 5),       -- note globale
    note_ecriture int check (note_ecriture between 0 and 5),   -- sous-note écriture
    note_intrigue int check (note_intrigue between 0 and 5),   -- sous-note intrigue
    note_personnages int check (note_personnages between 0 and 5), -- sous-note personnages
    id_livre int not null constraint avis_fk_livre references _livre(id) on delete cascade,      -- si un livre est supprimé → supprime l'avis
    constraint avis_unique_livre unique (id_livre)        -- un seul avis par livre
);
comment on table _avis is 'Avis unique pour chaque livre, avec une note globale, des sous-notes et un commentaire.';
comment on column _avis.titre_avis is 'Titre ou résumé court de l''avis.';
comment on column _avis.commentaire is 'Commentaire détaillé de l''avis.';
comment on column _avis.note is 'Note globale (0 à 5).';
comment on column _avis.note_ecriture is 'Sous-note sur la qualité de l''écriture (0 à 5).';
comment on column _avis.note_intrigue is 'Sous-note sur la qualité de l''intrigue (0 à 5).';
comment on column _avis.note_personnages is 'Sous-note sur la profondeur des personnages (0 à 5).';
comment on column _avis.id_livre is 'Référence unique vers le livre concerné.';

-- Table des auteurs
create table _auteur (
    id serial primary key,
    prenom varchar(127) not null,
    nom varchar(127) not null,
    constraint auteur_unique unique (prenom, nom)
);
comment on table _auteur is 'Auteurs avec prénom et nom. La combinaison prénom+nom est unique.';
comment on column _auteur.prenom is 'Prénom de l''auteur.';
comment on column _auteur.nom is 'Nom de l''auteur.';

-- Table de liaison livres / auteurs
create table _livre_auteur (
    id_livre int not null constraint livre_auteur_fk_livre references _livre(id) on delete cascade,
    id_auteur int not null constraint livre_auteur_fk_auteur references _auteur(id) on delete cascade,
    primary key (id_livre, id_auteur)
);
comment on table _livre_auteur is 'Liaison entre livres et auteurs : un livre peut avoir plusieurs auteurs et un auteur peut avoir écrit plusieurs livres.';

-- Table de liaison livres / genres supplémentaires
create table _livre_genre (
    id_livre int not null constraint livre_genre_fk_livre references _livre(id) on delete cascade,
    id_genre int not null constraint livre_genre_fk_genre references _genre(id) on delete cascade,
    primary key (id_livre, id_genre)
);
comment on table _livre_genre is 'Liaison entre livres et genres : un livre peut avoir plusieurs genres, et un genre plusieurs livres.';
