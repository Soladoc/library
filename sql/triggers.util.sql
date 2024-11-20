begin;

-- Fonctions utilitaires pour les triggers
-- Non destinées à êtres appelées en dehors de triggers.sql

set schema 'pact';

set plpgsql.extra_errors to 'all';

create function insert_offre (new record) returns int as $$
declare
    id_signalable int;
begin
    insert into pact._signalable default values returning id into id_signalable;
    insert into pact._offre (
        id,
        id_adresse,
        id_image_principale,
        id_professionnel,
        libelle_abonnement,
        titre,
        resume,
        description_detaillee,
        url_site_web,
        date_derniere_maj
    ) values (
        id_signalable,
        new.id_adresse,
        new.id_image_principale,
        new.id_professionnel,
        new.libelle_abonnement,
        new.titre,
        new.resume,
        new.description_detaillee,
        coalesce(new.url_site_web, ''),
        now()
    );
    insert into pact._changement_etat (id_offre) values (id_signalable);
    return id_signalable;
end
$$ language plpgsql;
comment on function insert_offre (record) is 'Insère une offre et retourne son id.';

create function insert_compte (new record) returns int as $$
declare
    id_identite int;
    id_signalable int;
begin
    insert into pact._identite default values returning id into id_identite;
    insert into pact._signalable default values returning id into id_signalable;
    insert into pact._compte (
        id,
        id_signalable,
        email,
        mdp_hash,
        nom,
        prenom,
        telephone
    ) values (
        id_identite,
        id_signalable,
        new.email,
        new.mdp_hash,
        new.nom,
        new.prenom,
        new.telephone
    );
    return id_identite;
end
$$ language plpgsql;
comment on function insert_compte (record) is 'Insère un compte et retourne son id.';

commit;