set schema 'pact';
create or replace function membres_insert() returns trigger as $$
declare
  id_temp integer;
begin
  select max(id_identite) as id_tempo from pact._identite;
  insert into _identite(id_identite) values (id_tempo+1) returning id_identite into id_temp;
  insert into _compte(email,mdp_hash,nom,prenom,telephone,id_identite) values (new.email,new.mdp_hash,new.nom,new.prenom,new.telephone,id_temp);
  insert into _membre(pseudo,existe,email) values (new.pseudo,true,new.email);
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
  select max(id_identite) as id_tempo from pact._identite;
  insert into _identite(id_identite) values (id_tempo+1) returning id_identite into id_temp;
  insert into _compte(email,mdp_hash,nom,prenom,telephone,id_identite) values (new.email,new.mdp_hash,new.nom,new.prenom,new.telephone,id_temp);
  insert into _professionnel(denomination,email) values (new.denomination,new.email) returning id_professionnel into id_temp;
  insert into _prive(id_prive,siren) values (id_temp,new.siren);
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
  select max(id_identite) as id_tempo from pact._identite;
  insert into _identite(id_identite) values (id_tempo+1) returning id_identite into id_temp;
  insert into _compte(email,mdp_hash,nom,prenom,telephone,id_identite) values (new.email,new.mdp_hash,new.nom,new.prenom,new.telephone,id_temp);
  insert into _professionnel(denomination,email) values (new.denomination,new.email) returning id_professionnel into id_temp;
  insert into _public(id_public) values (id_temp);
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
  nb_change_etat= count(*) from _changement_etat where id_offre=id_offre_cherche;
  if nb_change_etat%2==0 then
    return true;
  else
    return false;
  end if;
end;
$$ language 'plpgsql';
