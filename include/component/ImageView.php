<?php

require_once 'const.php';
require_once 'util.php';
require_once 'model/Image.php';

final class ImageView
{
    readonly Image $image;

    function __construct(Image $image)
    {
        $this->image = $image;
    }

    /**
     * Affiche le composant d'image utilisateur dans une figure.
     * @return void
     */
    function put_figure(): void
    {
?>
<figure>
    <?php $this->put_img() ?>
    <?php if ($this->image->legende) { ?>
        <figcaption><?= $this->image->legende ?></figcaption>
    <?php } ?>
</figure>
<?php
    }

    /**
     * Affiche le composant d'image utilisateur dans une simple img.
     * @return void
     */
    function put_img(): void
    {
?>
<img src="<?= $this->image->display_location() ?>"
    alt="<?= $this->image->legende ?: 'image' ?>"
    title="<?= $this->image->legende ?>">
<?php
    }
}
