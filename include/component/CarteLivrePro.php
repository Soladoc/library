<?php
require_once 'component/ImageView.php';
require_once 'const.php';
require_once 'model/Livre.php';
require_once 'redirect.php';
require_once 'util.php';

final class CarteLivrePro
{
    readonly ImageView $image;

    function __construct(
        readonly Livre $livre,
    ) {
        $this->image = new ImageView($livre->image);
    }

    /**
     * Affiche le composant de carte de livre pour membre
     * @return void
     */
    function put(): void
    {
?>
<div class="book-card">
    <?php $this->image->put_img() ?>
    <h3><a href="<?= h14s(location_detail_offre_pro($this->livre->id)) ?>"><?= h14s($this->livre->titre) ?></a></h3>
    <p class="genre"><?= h14s(ucfirst($this->livre->genre)) ?></p>
</div>
<?php
    }
}
