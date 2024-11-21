set schema 'pact';

set
    plpgsql.extra_errors to 'all';

-- Create
create function avis_insert () returns trigger as $$
declare
    id_avis integer;
begin
    insert into _signalable default values returning id into id_avis;
    insert into pact._avis (
        id,
        id_membre_auteur,
        id_offre,
        commentaire,
        date_experience,
        contexte,
        note
    ) values (
        id_avis,
        new.id_membre_auteur,
        new.id_offre,
        new.commentaire,
        new.date_experience,
        new.contexte,
        new.note
    );
    return new;
end
$$ language plpgsql;

create trigger tg_avis_insert instead of insert on avis for each row
execute function avis_insert ();
