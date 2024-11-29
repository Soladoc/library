<?php

require_once 'queries.php';
require_once 'redirect.php';
require_once 'util.php';

/**
 * Affiche le composant de carte d'offfre pour professionnel
 * @param array<string, mixed> $offre L'offre à afficher (ligne issue la BDD, foir `DB\query_offre`)
 * @return void
 */
function put_card_offre_pro(array $offre)
{
    $nb_avis = DB\query_avis_count($offre['id']);
?>
<div class="offer-card">
    <?php put_image(DB\query_image($offre['id_image_principale'])) ?>
    <h3><a href="<?= location_detail_offre_pro($offre['id']) ?>"><?= $offre['titre'] ?></a></h3>
    <p class="location"><?= format_adresse(notfalse(DB\query_adresse($offre['id_adresse']))) ?></p>
    <p class="category"><?= $offre['categorie'] ?></p>
    <p class="rating">
        <?php if ($nb_avis === 0) { ?>Aucun avis
        <?php } else { ?>Note&nbsp;: <?= $offre['note_moyenne'] ?>/5 ★ (<?= $nb_avis ?> avis)
        <?php } ?>
    </p>
</div>
<?php
}

/**
 * Affiche le composant de carte d'offfre pour membre ou visiteur.
 * @param array<string, mixed> $offre L'offre à afficher (ligne issue la BDD, foir `DB\query_offre`)
 * @return void
 */
function put_card_offre(array $offre)
{
?>
<div class="offer-card">
    <?php put_image(DB\query_image($offre['id_image_principale'])) ?>
    <h3><a href="<?= location_detail_offre($offre['id']) ?>"><?= $offre['titre'] ?></a></h3>
    <p class="location"><?= format_adresse(notfalse(DB\query_adresse($offre['id_adresse']))) ?></p>
    <p><?= $offre['resume'] ?></p>
    <p class="category"><?= $offre['categorie'] ?></p>
</div>
<?php
}
