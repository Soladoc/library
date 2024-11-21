<?php

require_once 'queries.php';
require_once 'util.php';

function put_image(array $image)
{
?><img src="<?= "/images_utilisateur/{$image['id']}.{$image['mime_subtype']}" ?>" alt="<?= $image['legende'] ?: "Image de l'offre" ?>" title="<?= $image['legende'] ?>"><?php
}

/**
 * Puts an offer card.
 *
 * @param array<string, mixed> $offre row in the offers view.
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

function put_card_offre(array $offre)
{ echo "5";
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

function format_adresse(array $adresse)
{
    // Concaténer les informations pour former une adresse complète

    return elvis($adresse['precision_ext'], ', ')
        . elvis($adresse['precision_int'], ', ')
        . elvis($adresse['numero_voie'], ' ')
        . elvis($adresse['complement_numero'], ' ')
        . elvis($adresse['nom_voie'], ', ')
        . elvis($adresse['localite'], ', ')
        . elvis(query_commune($adresse['code_commune'], $adresse['numero_departement'])['nom'], ', ')
        . query_codes_postaux($adresse['code_commune'], $adresse['numero_departement'])[0];
}
