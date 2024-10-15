# Changements BDD

## InfoActivité, InfoSpectacle, InfoVisite

- indication_dureee: changed type to timespan

## Langue

- ajouter libelle varchar(63)

## Membre

- pseudo: ajouter un domaine

## Public

- ajouter secteur ("public" ou "associatif")

## Tag

- tag: ajouter domaine (dictionnaire de données)

## TagRestauration

- ajouter au dictionnaire de données
- tag: ajouter domaine (dictionnaire de données)

## Offre

- ajouté attribut calculé : en_ligne (définit par le nombre de ChangementEtat, comme expliqué dans le VPP)
- Description détaillée: ne peut plus être null

## InfoCategorie

- class supprimée

## InfosRestauration, InfosActivité, InfosVisite, InfosSpectacle, InfosParcAttractions

- renommé en Restaurant, Activité, Visite, Spectacle, ParcAttractions (renommé aussi l'attribut id_* de clé primaire)
- hérite désormais de Offre

## Attraction

- classe supprimée

## OffreGratuite, OffrePayante, OffrePremium

- classe supprimée

## Abonnement

- nouvelle classe (3 instances fixes, voir le commentaire associé)

## blacklist, SouscriptionOption, grille tarifaire

- changé l'assocation pour lier à Offre avec une contrainte sur le libellé de l'Abonnement
