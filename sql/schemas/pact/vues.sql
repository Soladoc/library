set schema 'bibliotheque';

create or replace view v_compte as
select
    numero_compte,
    email,
    mdp_hash
from _compte;

comment on view v_compte is
'Vue publique des comptes.';

create or replace view v_livre_complet as
select
    l.id,
    l.titre,
    string_agg(a.prenom || ' ' || a.nom, ', ') as auteurs,
    l.nom_image,
    l.numero_compte
from _livre l
join _livre_auteur la on la.id_livre = l.id
join _auteur a on a.id = la.id_auteur
group by l.id, l.titre, l.nom_image, l.numero_compte;

create or replace view v_avis as
select
    a.id,
    a.titre_avis,
    a.commentaire,
    a.note,
    a.note_ecriture,
    a.note_intrigue,
    a.note_personnages,
    a.id_livre,
    l.titre as titre_livre
from _avis a
join _livre l on l.id = a.id_livre;
