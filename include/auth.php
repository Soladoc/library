<?php

function connecter_pro(int $id_pro) {
    $_SESSION['id_pro'] = $id_pro;
}

function connecter_membre(int $id_membre) {
    $_SESSION['id_membre'] = $id_membre;
}

/**
 * Exige de l'utilisateur qu'il soit connecté en tant que professionnel
 * 
 * Redirige vers la page de connexion sinon.
 * 
 * Cette fonction appelle <i>header</i>. Elle doit donc être appelé <b>avant</b> tout envoi de HTML.
 * 
 * @return int id pro
 */
function exiger_connecte_pro(): int {
    if (!($id = $_SESSION['id_pro'] ?? null)) {
        header('Location: /autres_pages/connexion.php');
        exit;
    }
    return $id;
}

/**
 * Exige de l'utilisateur qu'il soit connecté en tant que membre
 * 
 * Redirige vers la page de connexion sinon.
 * 
 * Cette fonction appelle <i>header</i>. Elle doit donc être appelé <b>avant</b> tout envoi de HTML.
 * 
 * @return int id membre
 */
function exiger_connecte_membre(): int {
    if (!($id = $_SESSION['id_membre'] ?? null)) {
        header('Location: /autres_pages/connexion.php');
        exit;
    }
    return $id;
}
