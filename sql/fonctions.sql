begin;

set schema 'pact';

set plpgsql.extra_errors to 'all';

-- Retourne une ligne nommant la catégorie d'une offre.
create function offre_categorie(id_offre int)
    returns ligne
    as $$
begin
    if id_offre in (select id from _restaurant) then return 'restaurant'; end if;
    if id_offre in (select id from _activite) then return 'activité'; end if;
    if id_offre in (select id from _visite) then return 'visite'; end if;
    if id_offre in (select id from _spectacle) then return 'spectacle'; end if;
    if id_offre in (select id from _parc_attractions) then return 'parc d''attractions'; end if;
    raise 'incohérence: offre non catégorisée';
end;
$$
language 'plpgsql';

-- Insère une offre et retourne son id.
create function insert_offre(new record)
    returns int
    as $$
declare
    id_signalable int;
begin
    insert into pact._signalable default values returning id into id_signalable;
    insert into pact._offre
        (id, id_adresse, id_image_principale, libelle_abonnement, id_professionnel, titre, resume, description_detaillee, url_site_web)
    values
        (id_signalable, new.id_adresse, new.id_image_principale, new.libelle_abonnement, new.id_professionnel, new.titre, new.resume, new.description_detaillee, new.url_site_web);
    return id_signalable;
end
$$
language 'plpgsql';

-- Insère un compte et retourne son id.
create function insert_compte(new record)
    returns int
    as $$
declare
    id_identite int;
    id_signalable int;
begin
    insert into pact._identite default values returning id into id_identite;
    insert into pact._signalable default values returning id into id_signalable;
    insert into pact._compte(id, id_signalable, email, mdp_hash, nom, prenom, telephone)
        values (id_identite, id_signalable, new.email, new.mdp_hash, new.nom, new.prenom, new.telephone);
    return id_identite;
end
$$
language 'plpgsql';

commit;