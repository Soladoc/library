<?php

/**
 * Summary of put_head
 * @param string $title
 * @param string[] $stylesheets
 * @param array<string, string> $scripts
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
