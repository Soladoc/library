-- Fonctions utilitaires pour les triggers
-- Non destinées à êtres appelées en dehors de triggers.sql

set schema 'pact';

set plpgsql.extra_errors to 'all';

create function insert_avis (new record, p_id_offre int) returns int as $$
declare
    id_avis int;
    -- On ne prend pas en compte les heures pour cette vérification.
    -- Cela signifie qu'on autorise la publication d'un avis le même jour que la création de l'offre mais à une heure antérieure.
    -- C'est soit ça soit interdire la publication à heure postérieure car date_experience n'a pas d'heure.
    -- Mieut vaut autoriser un cas potentiellement invalide plutôt qu'interdire un cas potentiellement valide.
    date_creation_offre constant date not null = offre_creee_le(p_id_offre)::date;
begin
    if new.date_experience < date_creation_offre then
        raise 'La date d''expérience de l''avis (%) ne peut pas être antérieure à la date création de de l''offre (%)', new.date_experience, date_creation_offre;
    end if;
    insert into _signalable default values returning id into id_avis;
    insert into pact._avis (
        id,
        id_offre,
        id_membre_auteur,
        commentaire,
        date_experience,
        contexte,
        note
    ) values (
        id_avis,
        p_id_offre,
        new.id_membre_auteur,
        new.commentaire,
        new.date_experience,
        new.contexte,
        new.note
    );
    return id_avis;
end
$$ language plpgsql strict;

comment on function insert_avis (record, int) is
'Insère un avis.
`new` contient les valeurs de l''avis.
@param p_id_offre l''ID de l''offre commentée (new n''est pas utilisé pour remplir cette colonne)
@returns L''ID de l''avis inséré.';

create function insert_offre (new record) returns int as $$
declare
    id_signalable int;
begin
    if (new.libelle_abonnement = 'gratuit') = new.id_professionnel in (select id from _prive) then
        raise 'Cette offre (%) d''abonnement % ne peut pas être administrée par un professionnel du secteur %',
            new.titre, new.libelle_abonnement, professionnel_secteur(new.id_professionnel);
    end if;

    new.modifiee_le = coalesce(new.modifiee_le, localtimestamp);

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
        new.url_site_web,
        new.modifiee_le,
        coalesce(new.periodes_ouverture, '{}')
    );
    insert into pact._changement_etat (id_offre, fait_le) values (id_signalable, new.modifiee_le);
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
