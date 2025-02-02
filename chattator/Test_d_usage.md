<style>
/* Styles généraux */
body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    max-width: 800px;
    margin: 40px auto;
    padding: 20px;
    background-color: #f4f4f4;
    color: #333;
}

/* Titres */
h1, h2, h3 {
    color: #2c3e50;
    border-bottom: 2px solid #ddd;
    padding-bottom: 5px;
}

/* Tableau */
table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

table, th, td {
    border: 1px solid #ddd;
}

th, td {
    padding: 10px;
    text-align: left;
}

th {
    background-color: #3498db;
    color: white;
}

/* Liens */
a {
    color: #e74c3c;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

/* Code */
code {
    background-color: #eef;
    padding: 2px 5px;
    border-radius: 4px;
}

pre {
    background-color: #222;
    color: #f8f8f2;
    padding: 10px;
    border-radius: 5px;
    overflow-x: auto;
}

/* Citations */
blockquote {
    margin: 10px 0;
    padding: 10px;
    background: #e3f2fd;
    border-left: 5px solid #2196f3;
}

/* Listes */
ul {
    list-style: square;
    margin-left: 20px;
}


</style>

# Tests d'usage du chat Tchattator

## Connexion au serveur

| Action | Résultat attendu | Résultat réel |
|--------|-----------------|--------------|
| Lancer le client et tenter de se connecter au serveur | Le client affiche "Connexion établie avec le serveur.(Code 200)" |  |
| Saisir un nom d'utilisateur valide et un mot de passe correct | Le serveur répond "Nom d'utilisateur reçu" et "Mot de passe reçu", puis envoie un token |  |
| Saisir un nom d'utilisateur invalide | Le serveur refuse la connexion |  |

## Envoi et réception de messages

| Action | Résultat attendu | Résultat réel |
|--------|-----------------|--------------|
| Envoyer un message à un utilisateur | Le serveur demande le message et le confirme avec "Message envoyé" |  |
| Lire tous les messages reçus | Le serveur affiche la liste des messages |  |
| Lire uniquement les messages non lus | Seuls les messages non lus sont affichés |  |

## Gestion des messages

| Action | Résultat attendu | Résultat réel |
|--------|-----------------|--------------|
| Modifier un message existant | Le serveur demande l'ID du message et enregistre la modification |  |
| Supprimer un message | Le serveur demande l'ID du message et le supprime |  |

## Gestion des utilisateurs

| Action | Résultat attendu | Résultat réel |
|--------|-----------------|--------------|
| Rechercher un utilisateur | Le serveur affiche les informations de l'utilisateur |  |
| Bloquer un utilisateur | Le serveur confirme "Blocage de l'utilisateur effectué" |  |
| Débloquer un utilisateur | Le serveur confirme "Blocage de l'utilisateur annulé" |  |
| Bannir un utilisateur | Le serveur confirme "Bannissement de l'utilisateur effectué" |  |
| Débannir un utilisateur | Le serveur confirme "Bannissement de l'utilisateur annulé" |  |

## Déconnexion

| Action | Résultat attendu | Résultat réel |
|--------|-----------------|--------------|
| Se déconnecter en tapant 14 | Le serveur ferme la session et affiche "Au revoir" |  |
| Quitter l'application en tapant 15 | Le serveur met fin à la connexion proprement |  |

