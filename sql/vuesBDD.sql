set schema 'pact';
create or replace view comptes as select * from _compte;
create or replace view membres as select * from _membre;
create or replace view offres as select * from _offre;
create or replace view pro_prive as select * from _professionnel join _prive on id_professionnel=id_prive;
create or replace view pro_public as select * from _professionnel join _public on id_professionnel=id_public;
