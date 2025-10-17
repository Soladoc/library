set schema 'bibliotheque';

-- Vue des comptes (sans mot de passe)
create or replace view v_compte as
select
    numero_compte,
    email,
    mdp_hash
from _compte;

comment on view v_compte is
'Vue publique des comptes (sans les mots de passe).';


-- Vue des livres (avec jointures vers compte et image)
create or replace view v_livre as
select
    l.id as id_livre,
    l.titre,
    l.auteurs,
    i.nom as image_nom,
    c.email as proprietaire
from _livre l
left join _image i on l.id_image = i.id
left join _compte c on l.numero_compte = c.numero_compte;

comment on view v_livre is
'Vue publique des livres avec titre, auteurs, note, image et propriétaire (email du compte).';
