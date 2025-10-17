set schema 'pact';

set
    plpgsql.extra_errors to 'all';


-- Delete
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

create trigger tg__livre_after_delete
after delete on _livre
for each row
execute function _livre_after_delete();

comment on trigger tg__livre_after_delete on _livre is
'Déclenché après la suppression d''un livre, supprime l''image associée.';