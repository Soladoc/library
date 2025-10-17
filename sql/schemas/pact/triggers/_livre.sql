set schema 'bibliotheque';

set
    plpgsql.extra_errors to 'all';

create or replace function regenerer_fichier_compte(p_numero_compte int)
returns void as $$
declare
    fichier text;
    insert_compte text;
    insert_livre text;
begin
    fichier := format('/library/sql/instances/main/pact/compte_%s.sql', p_numero_compte);

    -- Récupère la commande INSERT du compte
    select format(
        'insert into _compte (numero_compte, email, mdp_hash) values (%s, %L, %L);',
        c.numero_compte, c.email, c.mdp_hash
    )
    into insert_compte
    from _compte c
    where c.numero_compte = p_numero_compte;

    -- Écrit d’abord le compte dans le fichier
    execute format($$COPY (select %L) TO %L;$$, insert_compte, fichier);

    -- Ajoute ensuite les livres de ce compte
    for insert_livre in
        select format(
            'insert into _livre (id, numero_compte, titre, auteurs, nom_image) values (%s, %s, %L, %L, %L);',
            l.id, l.numero_compte, l.titre, l.auteurs, l.nom_image
        )
        from _livre l
        where l.numero_compte = p_numero_compte
    loop
        -- Append = true pour ajouter à la fin du fichier
        execute format($$COPY (select %L) TO %L (FORMAT text, append true);$$, insert_livre, fichier);
    end loop;
end;
$$ language plpgsql;

-- Créé un livre et modifie le fichier du compte correspondant
create or replace function v_livre_insert()
returns trigger as $$
declare
    new_image_id int;
begin
    -- Si une image est précisée, l'ajouter dans _image
    if new.nom_image is not null then
        insert into _image (mime_subtype) values (new.nom_image)
        returning id into new_image_id;
    end if;

    -- Insère le livre avec le lien vers l'image
    insert into _livre (numero_compte, titre, auteurs, nom_image, note, id_image)
    values (new.numero_compte, new.titre, new.auteurs, new.nom_image, new.note, new_image_id)
    returning id into new.id;

    -- Régénère le fichier du compte
    perform regenerer_fichier_compte(new.numero_compte);

    return new;
end;
$$ language plpgsql;

create or replace trigger tg_v_livre_insert
instead of insert on v_livre
for each row
execute function v_livre_insert();

-- Modifie un livre et modifie le fichier du compte correspondant
create or replace function v_livre_update()
returns trigger as $$
declare
    new_image_id int;
begin
    -- Interdire le changement de compte
    if old.numero_compte <> new.numero_compte then
        raise exception 'Un livre ne peut pas changer de compte.';
    end if;

    -- Si le nom de l'image change, supprimer l'ancienne et insérer la nouvelle
    if new.nom_image is distinct from old.nom_image then
        -- Supprime l’ancienne image si elle existait
        if old.id_image is not null then
            delete from _image where id = old.id_image;
        end if;

        -- Insère la nouvelle image si spécifiée
        if new.nom_image is not null then
            insert into _image (mime_subtype) values (new.nom_image)
            returning id into new_image_id;
        end if;
    else
        new_image_id := old.id_image;
    end if;

    -- Met à jour les infos du livre
    update _livre
    set titre = new.titre,
        auteurs = new.auteurs,
        nom_image = new.nom_image,
        note = new.note,
        id_image = new_image_id
    where id = old.id;

    -- Régénère le fichier du compte
    perform regenerer_fichier_compte(new.numero_compte);

    return new;
end;
$$ language plpgsql;

create or replace trigger tg_v_livre_update
instead of update on v_livre
for each row
execute function v_livre_update();

-- =============================
--  Fonction Trigger pour _livre
-- =============================

create or replace function _livre_after_delete()
returns trigger as $$
begin
    -- Supprime directement l'image associée, car chaque image
    -- ne peut être utilisée que par un seul livre (contrainte UNIQUE)
    if old.id_image is not null then
        delete from _image where id = old.id_image;
    end if;

    return old;
end;
$$ language plpgsql;

comment on function _livre_after_delete() is
'Supprime automatiquement l''image associée lorsqu''un livre est supprimé.
Comme id_image est unique, elle ne peut appartenir qu''à un seul livre.';


-- =============================
--  Trigger associé à _livre
-- =============================

create trigger tg_livre_after_delete
after delete on _livre
for each row
execute function _livre_after_delete();

comment on trigger tg_livre_after_delete on _livre is
'Déclenché après la suppression d''un livre, supprime l''image associée.';

-- Modifie le fichier du compte après la supression d'un livre
create or replace function v_livre_delete()
returns trigger as $$
begin
    -- Supprime le livre dans la table réelle
    delete from _livre
    where id = old.id;

    -- Régénère le fichier du compte concerné
    perform regenerer_fichier_compte(old.numero_compte);

    return old;
end;
$$ language plpgsql;

create or replace trigger tg_v_livre_delete
instead of delete on v_livre
for each row
execute function v_livre_delete();

comment on function v_livre_delete() is
'Supprime un livre via la vue v_livre et régénère le fichier du compte correspondant.';