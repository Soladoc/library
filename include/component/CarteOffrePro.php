<?php

final class CarteOffrePro
{
    readonly Offre $offre;
    readonly ImageView $image_principale;

    function __construct(Offre $offre)
    {
        $this->offre = $offre;
        $this->image_principale = new ImageView($offre->image_principale);
    }

    /**
     * Affiche le composant de carte d'offfre pour professionnel
     * @return void
     */
    function put(): void
    {
?>
<div class="offer-card">
    <?php $this->image_principale->put_img() ?>
    <h3><?= $this->offre->titre ?></h3>
    <p class="location"><?= $this->offre->adresse->format() ?></p>
    <p class="category"><?= $this->offre->categorie ?></p>
    <p class="rating">
        <?php if ($this->offre->nb_avis === 0) { ?>
            Aucun avis
        <?php } else { ?>
            Note&nbsp;: <?= $this->offre->note_moyenne ?> /5 ★ (<?= $this->offre->nb_avis ?> avis)
        <?php } ?>
    </p>
    <a href="/autres_pages/detail_offre.php?id=<?= $this->offre->id ?>&pro=true">
        <button class="btn-more-info">En savoir plus</button>
    </a>
</div>
<?php
    }
}
