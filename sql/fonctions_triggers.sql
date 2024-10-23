set schema 'pact';
create or replace function offres_insert() returns trigger as $$
declare
  id_temp integer;
begin
  insert into pact._signalable default values returning id_signalable into id_temp;
  insert into offres_en_ligne(id_offre,titre,resume,description_detaille,url_site_web,date_derniere_maj,id_categorie,adresse,photoprincipale,abonnement,id_signalable,
  id_professionnel,id_signalable) 
  values (new.id_offre,new.titre,new.resume,new.description_detaille,new.url_site_web,new.date_derniere_maj,new.id_categorie,new.adresse,new.photoprincipale,
  new.abonnement,new.id_signalable,new.id_professionnel,id-temp);
  return new;
end;
$$ language 'plpgsql';
create or replace trigger offres_insert
instead of insert
on offres for each row
execute procedure offres_insert();
create or replace function membres_insert() returns trigger as $$
declare
  id_temp integer;
begin
  insert into pact._identite default values returning id_identite into id_temp;
  insert into pact._compte(email,mdp_hash,nom,prenom,telephone,id_identite) values (new.email,new.mdp_hash,new.nom,new.prenom,new.telephone,id_temp);
  insert into pact._membre(pseudo,existe,email) values (new.pseudo,true,new.email);
  return new;
end;
$$ language 'plpgsql';
create or replace trigger tg_membres_insert
instead of insert
on membres for each row
execute procedure membres_insert();
create or replace function pro_prive_insert() returns trigger as $$
declare 
  id_temp integer;
begin
  insert into pact._identite default values returning id_identite into id_temp;
  insert into pact._compte(email,mdp_hash,nom,prenom,telephone,id_identite) values (new.email,new.mdp_hash,new.nom,new.prenom,new.telephone,id_temp);
  insert into pact._professionnel(denomination,email,existe) values (new.denomination,new.email,true) returning id_professionnel into id_temp;
  insert into pact._prive(id_prive,siren) values (id_temp,new.siren);
  return new;
end;
$$ language 'plpgsql';
create or replace trigger tg_pro_prive_insert
instead of insert
on pro_prive for each row
execute procedure pro_prive_insert();
create or replace function pro_public_insert() returns trigger as $$
declare 
  id_temp integer;
begin
  insert into pact._identite default values returning id_identite into id_temp;
  insert into pact._compte(email,mdp_hash,nom,prenom,telephone,id_identite) values (new.email,new.mdp_hash,new.nom,new.prenom,new.telephone,id_temp);
  insert into pact._professionnel(denomination,email,existe) values (new.denomination,new.email,true) returning id_professionnel into id_temp;
  insert into pact._public(id_public) values (id_temp);
  return new;
end;
$$ language 'plpgsql';
create or replace trigger tg_pro_public_insert
instead of insert
on pro_public for each row
execute procedure pro_public_insert();
create or replace function est_en_ligne(id_offre_cherche integer) returns bool as $$
declare
  nb_change_etat integer;
begin
  nb_change_etat:= count(*) from _changement_etat where id_offre=id_offre_cherche;
  if nb_change_etat%2=0 then
    return true;
  else
    return false;
  end if;
end;
$$ language 'plpgsql';
create or replace function nb_offres_en_ligne(id_pro_cherche integer) returns table (id_offre integer,
  titre varchar(255),
  resume varchar(1023),
  description_detaille text,
  url_site_web varchar(2047),
  date_derniere_maj timestamp,
  id_categorie integer,
  adresse integer,
  photoprincipale integer,
  abonnement varchar(63),
  id_signalable integer,
  id_professionnel integer) as $$
declare
  id_offre_temp integer;
  boucle integer;
begin
  create view _offres as select * from _offre where id_professionnel=id_pro_cherche;
  create table offres_en_ligne(
    id_offre integer,
    titre varchar(255),
    resume varchar(1023),
    description_detaille text,
    url_site_web varchar(2047),
    date_derniere_maj timestamp,
    id_categorie integer,
    adresse integer,
    photoprincipale integer,
    abonnement varchar(63),
    id_signalable integer,
    id_professionnel integer
  );
  for i in 1..count(*) from _offres loop
    boucle:=i-1;
    id_offre_temp:= (select id_offre from _offres offset boucle rows fetch first row only); 
    if est_en_ligne(id_offre_temp) then
      insert into offres_en_ligne(id_offre,titre,resume,description_detaille,url_site_web,date_derniere_maj,id_categorie,adresse,photoprincipale,
      abonnement,id_signalable,id_professionnel) select id_offre,titre,resume,description_detaille,url_site_web,date_derniere_maj,id_categorie,adresse,photoprincipale,
      abonnement,id_signalable,id_professionnel from _offres offset boucle rows fetch first row only;
    end if;
  end loop;
  return query select * from offres_en_ligne;
  drop view _offres;
  drop table offres_en_ligne;
end;
$$ language 'plpgsql';
