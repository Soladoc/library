<?php
/**
 * auth.php
 * Fonctions relatives à l'authentification
 */

notfalse(session_start());

/**
 * Déconnecte la session actuelle.
 * @return void
 */
function se_deconnecter(): void {
    assert(est_connecte());
    $_SESSION = [];
    notfalse(session_destroy());
}

/**
 * Sommes-nous connectés?
 * @return bool `true` si nous (la session courante) sommes connectés en tant que membre ou professionnel, `false` sinon.
 */
function est_connecte(): bool {
    return est_connecte_pro() || est_connecte_membre();
}

// Professionnel

/**
 * Marque la session comme connectée à une compte professionnel.
 * @param int $id_pro L'ID du professionnel qui sera désormais connecté sur la session actuelLe.
 * @return void
 */
function se_connecter_pro(int $id_pro): void
{
    $_SESSION['id_pro'] = $id_pro;
}

/**
 * Retourne l'ID du membre actuellement connecté, redirigeant vers la page de connexion sinon.
 * Cette fonction appelle <i>header</i>. Elle doit donc être appelé <b>avant</b> tout envoi de HTML.
 * @return int L'ID du professionnel actuellement connecté.
 */
function exiger_connecte_pro(): int
{
    if (($id = id_pro_connecte()) !== null) {
        return $id;
    }
    header('Location: /autres_pages/connexion.php');
    exit;
}

/**
 * Retourne l'ID du professionnel actuellement connecté.
 * @return ?int L'ID du professionnel actuellement connecté, ou `null` si la session actuelle n'est pas connectée en tant que professionnel.
 */
function id_pro_connecte(): ?int
{
    return $_SESSION['id_pro'] ?? null;
}

/**
 * Sommes-nous connectés en tant que professionnel?
 * @return bool `true` si nous (la session courante) sommes connectés en tant que professionnel, `false` sinon.
 */
function est_connecte_pro(): bool
{
    return isset($_SESSION['id_pro']);
}

// Membre

/**
 * Marque la session comme connectée à une compte membre.
 * @param int $id_pro L'ID du membre qui sera désormais connecté sur la session actuelLe.
 * @return void
 */
function se_connecter_membre(int $id_membre): void
{
    $_SESSION['id_membre'] = $id_membre;
}


/**
 * Retourne l'ID du professionnel actuellement connecté, redirigeant vers la page de connexion sinon.
 * Cette fonction appelle <i>header</i>. Elle doit donc être appelé <b>avant</b> tout envoi de HTML.
 * @return int L'ID du membre actuellement connecté.
 */
function exiger_connecte_membre(): int
{
    if (($id = id_membre_connecte()) !== null) {
        return $id;
    }
    header('Location: /autres_pages/connexion.php');
    exit;
}

/**
 * Retourne l'ID du membre actuellement connecté.
 * @return ?int L'ID du membre actuellement connecté, ou `null` si la session actuelle n'est pas connectée en tant que professionnel.
 */
function id_membre_connecte(): ?int
{
    return $_SESSION['id_membre'] ?? null;
}

/**
 * Sommes-nous connectés en tant que membre?
 * @return bool `true` si nous (la session courante) sommes connectés en tant que membre, `false` sinon.
 */
function est_connecte_membre(): bool
{
    return isset($_SESSION['id_membre']);
}
