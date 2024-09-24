# Changelog

## 23/09

### Jira

- 2FA universellle: enlevé mention de l'authentification en double facteurs de l'US "Réinitialiser son mot de passe (Professionnel)" et ajouté une US pour la 2FA explicitement (car elle doit aussi être disponible pour les membres)
- Changé l'estimation de l'US filtrage par mot-clé de 13 à 8 h : en effet il n'y a pas besoin de rechercher dans plusieurs champs puisque je viens de découvrir qu'on a un champ dédié pour les tags. Algorithme de recherche : simple page rank : nombre de mot-clés correspondants divisé par nombre total de mot clés

### UML

- Clés primaires
- Suivi des recommendations dans les documents de conception
- Tag et tags d'Offre
- catégorie d'Offre : attribut transformé en stratégie (il prend un ensemble fermé de valeurs qui sont accompagnées d'attributs supplémentaires)
