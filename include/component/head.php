<?php

/**
 * Affiche l'élement `<head>` HTML avec le titre, feuilles de style CSS et les scripts JS fournis.
 * @param string $title Le titre du document.
 * @param array $stylesheets Un liste de chemins relatifs dans au dossier `/style` des feuilles de style CSS à inclure.
 * @param array $scripts Un tableau associatif mappant des chemins relatifs au dossier `/script_js` des script JS à inclure vers leurs paramètres qui correspond au reste de l'attribut.
 *
 * Note: la feuille de stile `style.css` et le script `base.js` sont inclus dans tous les documents.
 *
 * @example location description
 * ```php
 * put_head("Création d'une offre",
 *  ['creation_offre.css'],
 *  ['module/creation_offre.js' => 'defer type="module"'])
 * ```
 * Produit l'HTML suivant (simplifié)
 * ```html
 * <head>
 *     <title>Création d'une offre</title>
 *     <link rel="stylesheet" href="/style/style.css">
 *     <link rel="stylesheet" href="/style/creation_offre.css">
 *     <script defer src="/script_js/base.js">
 *     <script defer type="module" src="/script_js/module/creation_offre.js">
 * </head>
 * ```
 * @return void
 */
function put_head(string $title, array $stylesheets = [], array $scripts = [])
{
    ?>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($title) ?></title>
        <link rel="stylesheet" href="/style/style.css">
        <?php
        foreach ($stylesheets as $href) {
            // Si c'est une URL (contient un ':'), on laisse tel quel. Sinon on préfixe par le dossier des feuilles de style.
            ?><link rel="stylesheet" href="<?= str_contains($href, ':') ? $href : "/style/$href" ?>"><?php
        }
        ?>
        <script defer src="/script_js/base.js"></script>
        <?php
        foreach ($scripts as $src => $attrs) {
            // Idem.
            ?><script <?= $attrs ?> src="<?= str_contains($src, ':') ? $src : "/script_js/$src" ?>"></script><?php
        }
        ?>
    </head>
    <?php
}
