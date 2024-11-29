<?php 





$mot_cle= $_GET["mot_cle"];
$recherche = $_GET["valider"]; 
$modif_affichage = false;



if (isset($valider)&& !empty(trim($mot_cle))) {
     $tab = DB/querry_select_offre_motcle($mot_cle);
    $modif_affichage = true;

}

?>










<form action="" name="bare_de_recherche" method="get">

<input type="text" name="mot_cle" value="<?php echo $mot_cle ?>" placeholder=">Mots-clés">
<input type="submit" name="valider" value="Recherche">
</form>



<?php 
if ($recherche) {
    ?> 
    <div id="recherche">
        <div id="offre_recherhce">
            <ol>
                <?php for ($i=0; $i < count($tab); $i++) { 
                    ?>
                    <li><?php echo $tab[$i]["titre"] ?></li>
                    <?php
                }?>

            </ol>
        </div>
    </div>
    <?php
}
?>