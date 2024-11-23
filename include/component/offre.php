<?php

require_once 'queries.php';
require_once 'util.php';

/**
 * Affiche le composant d'image utilisateur.
 * @param array $image L'image à afficher (ligne issue la BDD, voir `query_image`)
 * @return void
 */
function put_image(array $image)
{
?><img src="<?= "/images_utilisateur/{$image['id']}.{$image['mime_subtype']}" ?>" alt="<?= $image['legende'] ?: "Image de l'offre" ?>" title="<?= $image['legende'] ?>"><?php
}

/**
 * Affiche le composant de carte d'offfre pour professionnel
 * @param array<string, mixed> $offre L'offre à afficher (ligne issue la BDD, foir `query_offre`)
 * @return void
 */
function put_card_offre_pro(array $offre)
{
    $nb_avis = query_avis_count($offre['id']);
?>
<div class="offer-card">
    <?php put_image(query_image($offre['id_image_principale'])) ?>
    <h3><?= $offre['titre'] ?></h3>
    <p class="location"><?= format_adresse(notfalse(query_adresse($offre['id_adresse']))) ?></p>
    <p class="category"><?= $offre['categorie'] ?></p>
    <p class="rating">
        <?php if ($nb_avis === 0) { ?>Aucun avis
        <?php } else { ?>Note&nbsp;: <?= $offre['note_moyenne'] ?>/5 ★ (<?= $nb_avis ?> avis)
        <?php } ?>
    </p>
    <a href="/autres_pages/detail_offre.php?id=<?= $offre['id'] ?>&pro=true">
        <button class="btn-more-info">En savoir plus</button>
    </a>
</div>
<?php
}

/**
 * Affiche le composant de carte d'offfre pour membre ou visiteur.
 * @param array<string, mixed> $offre L'offre à afficher (ligne issue la BDD, foir `query_offre`)
 * @return void
 */
function put_card_offre(array $offre)
{
?>
<div class="offer-card">
    <?php put_image(query_image($offre['id_image_principale'])) ?>
    <h3><?= $offre['titre'] ?> </h3>
    <p class="location"><?= format_adresse(notfalse(query_adresse($offre['id_adresse']))) ?></p>
    <p><?= $offre['resume'] ?></p>
    <p class="category"><?= $offre['categorie'] ?></p>
    <a href="/autres_pages/detail_offre.php?id=<?= $offre['id'] ?>">
        <button class="btn-more-info">En savoir plus</button>
    </a>
</div>
<?php
}
