set schema 'tchattator';

create view "user" as
    select id, email, nom, prenom, pseudo display_name, 0 kind from pact.membre
union all
    select id, email, nom, prenom, denomination, 1 from pact.pro_prive
union all
    select id, email, nom, prenom, denomination, 2 from pact.pro_public;