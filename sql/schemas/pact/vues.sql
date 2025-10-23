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
    string_agg(distinct a.prenom || ' ' || a.nom, ', ') as auteurs,
    -- Genres secondaires (hors principal)
    string_agg(distinct
        case when g.id <> l.genre_principal then g.nom end, ', '
    ) as genres_secondaires,
    -- Genre principal
    gp.nom as genre_principal,
    l.cote,
    l.nom_image,
    l.numero_compte
from _livre l
left join _livre_auteur la on la.id_livre = l.id
left join _auteur a on a.id = la.id_auteur
left join _livre_genre lg on lg.id_livre = l.id
left join _genre g on g.id = lg.id_genre
left join _genre gp on gp.id = l.genre_principal
group by l.id, l.titre, l.nom_image, l.numero_compte, gp.nom, l.cote;

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

create or replace view v_auteur as
select id, prenom, nom
from _auteur;
