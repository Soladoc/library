set schema 'tchattator';

create view "user" as
    select id, email, nom, prenom, api_key, mdp_hash, pseudo display_name, 0 kind from pact.membre
union all
    select id, email, nom, prenom, api_key, mdp_hash, denomination, 1 from pact.pro_prive
union all
    select id, email, nom, prenom, api_key, mdp_hash, denomination, 2 from pact.pro_public;