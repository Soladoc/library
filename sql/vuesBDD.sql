set schema 'pact';
create or replace view membres as select * from _membre natural join _compte where existe= true;
create or replace view offres as select * from _offre;
create or replace view pro_prive as select * from _professionnel join _prive on id_professionnel=id_prive natural join _compte;
create or replace view pro_public as select * from _professionnel join _public on id_professionnel=id_public natural join _compte;
create or replace view avis as select * from _avis;
create or replace view tous_comptes_pro as select email,mdp_hash,existe,id_professionnel from _compte natural join _professionnel;
 