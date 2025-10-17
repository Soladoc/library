set schema 'bibliotheque';

set
    plpgsql.extra_errors to 'all';

-- Create
create or replace function v_compte_insert()
returns trigger as $$
begin
    insert into _compte (email, mdp_hash)
    values (new.email, new.mdp_hash);

    return new;
end;
$$ language plpgsql;

create trigger tg_v_compte_insert
instead of insert on v_compte
for each row
execute function v_compte_insert();

comment on function v_compte_insert() is
'Permet l''insertion dans v_compte : insère un nouveau compte dans _compte avec email et mot de passe hashé.';

-- Update

create or replace function v_compte_update()
returns trigger as $$
begin
    -- Vérifie que seules les modifications de mdp_hash sont autorisées
    if new.email <> old.email or new.numero_compte <> old.numero_compte then
        raise exception 'Modification de l''email ou du numéro de compte interdite.';
    end if;

    -- Met à jour uniquement le mot de passe
    update _compte
    set mdp_hash = new.mdp_hash
    where numero_compte = old.numero_compte;

    return new;
end;
$$ language plpgsql;

-- =============================
--  Définition du trigger
-- =============================

create trigger tg_v_compte_update
instead of update on v_compte
for each row
execute function v_compte_update();

comment on function v_compte_update() is
'Autorise uniquement la modification du mot de passe (mdp_hash) via la vue v_compte. 
Interdit toute modification de l''email ou du numéro de compte.';


-- Delete
create or replace function v_compte_delete()
returns trigger as $$
begin
    -- Supprime d'abord les livres associés au compte
    delete from _livre
    where numero_compte = old.numero_compte;

    -- Puis supprime le compte
    delete from _compte
    where numero_compte = old.numero_compte;

    return old;
end;
$$ language plpgsql;

-- =============================
--  Définition du trigger
-- =============================

create trigger tg_v_compte_delete
instead of delete on v_compte
for each row
execute function v_compte_delete();

comment on function v_compte_delete() is
'Supprime un compte via la vue v_compte. 
Supprime également tous les livres associés avant de supprimer le compte.';
