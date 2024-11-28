<?php 
$mot_cle= $_GET["mot_cle"];
$recherche = $_GET["valider"]; 



if (isset($valider)&& !empty(trim($mot_cle))) {
    querry_select_offre_motcle($mot_cle);

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
        <div id="">
            <ol>
                <?php for ($i=0; $i < count($tab); $i++) { 
                    ?>
                    <li>resultat</li>
                    <?php
                }?>

            </ol>
        </div>
    </div>
    <?php
}
?>