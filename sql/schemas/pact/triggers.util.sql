-- Fonctions utilitaires pour les triggers
-- Non destinées à êtres appelées en dehors de triggers.sql

set schema 'bibliotheque';

set plpgsql.extra_errors to 'all';

-- Déclencheur avant insertion sur _livre pour générer automatiquement la cote
create trigger trigger_generate_cote_before_insert
    before insert on _livre
    for each row
    execute procedure generate_cote_for_livre();