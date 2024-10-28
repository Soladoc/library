<?php

require_once 'query.php';

/**
 * Puts an offer card.
 *
 * @param array<string, mixed> $offer row in the offers view.
 */
function offer_put_card_pro(array $offer)
{
?>
<div class="offer-card">
    <img src="<?= $offer['image_url'] ?>" alt="Image de l'offre">
    <h3><?= $offer['titre'] ?></h3>
    <p class="location"><?= $offer['localisation'] ?></p>
    <p class="category"><?= $offer['categorie'] ?></p>
    <p class="rating">Note : <?= $offer['note_moyenne'] ?>/5 ★ (<?= query_offres_nb_avis($offer['id']) ?> avis)</p>
    <a href="/autres_pages/detail_offre_pro.php?id=<?= $offer['id'] ?>">
        <button class="btn-more-info">En savoir plus</button>
    </a>
</div>
<?php
}