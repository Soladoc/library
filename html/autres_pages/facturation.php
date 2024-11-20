<!DOCTYPE html>
<html lang="fr">

<?php put_head("Facturation") ?>

<body>
    <?php require 'component/header.php' ?>
    <main>
        <?php
            $OFFRES = query_offres($id_professionnel);
            while ($offer = $OFFRES_HORS_LIGNE->fetch()) {
                echo "<pre>";
                print_r($offer);
                echo "</pre>";
            }
            $OFFRES_HORS_LIGNE->closeCursor()
        ?>
    </main>
</body>
</html>