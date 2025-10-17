set schema 'bibliotheque';

create or replace function regenerer_fichier_images()
returns void as $$
declare
    ligne text;
    fichier text := '/var/lib/postgresql/images.sql';  -- 🔧 adapte le chemin selon ton installation
    contenu text := '';
begin
    -- Construire le contenu complet du fichier
    for ligne in
        select format(
            'insert into _image (id, mime_subtype) values (%s, %L);',
            id, mime_subtype
        )
        from _image
        order by id
    loop
        contenu := contenu || ligne || E'\n';
    end loop;

    -- Écrit le contenu dans le fichier (nécessite superuser)
    perform pg_catalog.pg_file_unlink(fichier);  -- Supprime l’ancien fichier s’il existe
    perform pg_catalog.pg_file_write(fichier, contenu, false);

exception
    when others then
        raise warning 'Impossible d''écrire le fichier % : %', fichier, sqlerrm;
end;
$$ language plpgsql;

create or replace function _image_after_insert()
returns trigger as $$
begin
    perform regenerer_fichier_images();
    return new;
end;
$$ language plpgsql;

create or replace trigger tg__image_after_insert
after insert on _image
for each row
execute function _image_after_insert();

create or replace function _image_after_delete()
returns trigger as $$
begin
    perform regenerer_fichier_images();
    return old;
end;
$$ language plpgsql;

create or replace trigger tg__image_after_delete
after delete on _image
for each row
execute function _image_after_delete();
