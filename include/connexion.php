<?php

function connecter_pro(string $email, int $id_pro) {
    $_SESSION['email'] = $email;
    $_SESSION['id_pro'] = $id_pro;
}

function connecter_membre(string $email, int $id_membre) {
    $_SESSION['email'] = $email;
    $_SESSION['id_membre'] = $id_membre;
}

/**
 * Exige de l'utilisateur qu'il soit connecté en tant que professionnel
 * 
 * Redirige vers la page de connexion sinon.
 * 
 * Cette fonction appelle <i>header</i>. Elle doit donc être appelé <b>avant</b> tout envoi de HTML.
 * 
 * @return array{'email': string, 'id': int}
 */
function exiger_connecte_pro(): array {
    if (($email = $_SESSION['email'] ?? null) || ($id = $_SESSION['id_pro'] ?? null)) {
        header('Location: /autres_pages/connexion.php');
        exit;
    }
    return ['email' => $email, 'id' => $id];
}

/**
 * Exige de l'utilisateur qu'il soit connecté en tant que membre
 * 
 * Redirige vers la page de connexion sinon.
 * 
 * Cette fonction appelle <i>header</i>. Elle doit donc être appelé <b>avant</b> tout envoi de HTML.
 * 
 * @return array{'email': string, 'id': int}
 */
function exiger_connecte_membre(): array {
    if (($email = $_SESSION['email'] ?? null) || ($id = $_SESSION['id_membre'] ?? null)) {
        header('Location: /autres_pages/connexion.php');
        exit;
    }
    return ['email' => $email, 'id' => $id];
}
