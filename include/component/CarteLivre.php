<?php
require_once 'util.php';
require_once 'redirect.php';
require_once 'model/Livre.php';
require_once 'component/ImageView.php';

final class CarteLivre
{
    readonly ImageView $image_principale;

    function __construct(
        readonly Livre $livre,
    ) {
        $this->image = new ImageView($livre->image);
    }

    /**
     * Affiche le composant de carte de livre pour membre ou visiteur.
     * @return void
     */
    function put(): void
    {
?>
<div class="book-card">
    <?php $this->image->put_img() ?>
    <h3><a class="titre" href="<?= h14s(location_detail_offre($this->livre->id)) ?>"><?= h14s($this->livre->titre) ?></a></h3>
    <p><?= h14s($this->livre->resume) ?></p>
    <p class="genre"><?= h14s(ucfirst($this->livre->genre)) ?></p>
</div>
<?php
    }

    static function put_template(): void {
?>
<div class="book-card">
    <?php ImageView::put_template('book-image') ?>
    <h3><a class="titre" href=""></a></h3>
    <p class="book-resume"></p>
    <p class="genre"></p>
</div>
<?php   
    }
}
