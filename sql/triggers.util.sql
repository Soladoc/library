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
        modifiee_le,
        periodes_ouverture
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
        coalesce(new.modifiee_le, localtimestamp),
        coalesce(new.periodes_ouverture, '{}')
    );
    insert into pact._changement_etat (id_offre) values (id_signalable);
    return id_signalable;
end
$$ language plpgsql strict;
comment on function insert_offre (record) is
'Insère une offre.
`new` contient les valeurs de l''offre.
@returns L''ID de l''offre insérée.';

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
        telephone,
        id_adresse
    ) values (
        id_identite,
        id_signalable,
        new.email,
        new.mdp_hash,
        new.nom,
        new.prenom,
        new.telephone,
        new.id_adresse
    );
    return id_identite;
end
$$ language plpgsql strict;
comment on function insert_compte (record) is
'Insère un compte.
`new` contient les valeurs du compte.
@returns L''ID du compte inséré.';

create function _offre_after_update () returns trigger as $$
begin
    new.modifiee_le = localtimestamp;
end
$$ language plpgsql;
comment on function _offre_after_update () is
'Fonction trigger pour les sous classes de offre qui met à jour l''attribut modifiee_le';
