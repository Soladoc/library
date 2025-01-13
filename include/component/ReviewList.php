<?php

require_once 'model/Offre.php';

final class ReviewList
{
    function __construct(
        readonly Offre $offre,
        private ?int $id_membre_co,
    ) {
    }

    function put(): void
    {
        ?>
        <div class="review-list">
            <h4>Avis de la communauté</h4>
            <div class="review-summary">
                <h4>Résumé des notes</h4>
                <p>Nombre d'avis : <?= $this->offre->nb_avis ?></p>
                <p>Moyenne&nbsp;: <?= $this->offre->note_moyenne ?? 0 ?>/5 ★</p>
                <div class="rating-distribution">
                    <?php
                    $avis = iterator_to_array(Avis::from_db_all(id_offre: $this->offre->id));
                    $avis_count_by_note = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
                    foreach ($avis as $a) {
                        ++$avis_count_by_note[$a->note];
                    }
                    ?>
                    <p>5 étoiles&nbsp;: <?= $avis_count_by_note[5] ?> avis.</p>
                    <p>4 étoiles&nbsp;: <?= $avis_count_by_note[4] ?> avis.</p>
                    <p>3 étoiles&nbsp;: <?= $avis_count_by_note[3] ?> avis.</p>
                    <p>2 étoiles&nbsp;: <?= $avis_count_by_note[2] ?> avis.</p>
                    <p>1 étoile&nbsp;: <?= $avis_count_by_note[1] ?> avis.</p>
                </div>
                <?php if (!empty($avis)) {
                    foreach ($avis as $a) { ?>
                        <div class="review">
                            <p><strong><?= h14s($a->membre_auteur->pseudo) ?></strong> - <?= h14s($a->note) ?>/5
                            <a href=""><img class="signalement-flag" src="/images/flag.svg" width="24" height="29" alt="Drapeau" title="Signaler"></a></p>
                            <p class="review-contexte">Contexte&nbsp;: <?= h14s($a->contexte) ?></p>
                            <p><?= h14s($a->commentaire) ?></p>
                            <p class="review-date"><?= h14s($a->date_experience) ?></p>
                            <?php if (
                                $this->id_membre_co !== null
                                && $a->membre_auteur === $this->id_membre_co
                            ) { ?>
                                <form method="post" action="modifier.php?id=<?= $this->offre->id ?>&avis_id=<?= $a->id ?>">
                                    <button type="submit" class="btn-modif">Modifier</button>
                                </form>
                            <?php } ?>
                        </div>
                    <?php }
                } else { ?>
                    <p>Aucun avis pour le moment.&nbsp;</p>
                <?php } ?>
            </div>
        </div>
        <?php
    }
}
