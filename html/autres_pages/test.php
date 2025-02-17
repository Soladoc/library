<?php

use function Auth\exiger_connecte_membre;

require_once 'component/Page.php';
require_once 'auth.php';
require_once 'redirect.php';
require_once 'model/Compte.php';
require_once 'model/Offre.php';
require_once 'util.php';
require_once 'home/sql/unite.bash';

$page = new Page("Test Benji");
$test = 1;

$page->put(function(){
    $id = Auth\exiger_connecte_membre();

    $utilisateur = Compte::from_db($id);
    
    //ne passe pas cette étape si l'utilisateur n'est pas Snoozy
    if ($utilisateur->prenom !== 'Benjamin' ) {
        echo 'Utilisateur non autorisé sry';
        redirect_to('index.php');
        exit;
    }

    //TODO
    function resetDb(string $script_name = null ,string $nom = null){
        if ($script_name == null) {
            $script_name = 'unite.bash';
        }else{
            $script_name = '../../'. $script_name;
        }

        if ($nom == null) {
            $nom = 'reset.sql';
        } else {
            $nom = $nom.'.sql';
        }
        

        system("./".$script_name.">". $nom);
        $sql = file_get_contents($nom);
        $stmt = exec($sql);
        DB\transaction($stmt);
        unlink($nom);
        echo 'deja je suis dedans';
        return 1;
    }

    if (!isset($_GET['fonction'])) {
    ?>
        <section>
            <a class="btn-more-info bouton_principale_pro" href="test.php?fonction=reset">Réinitialiser la base de données</a>
        </section>
    <?php
    }elseif ($_GET['fonction'] == 'reset') {
        resetDb();
    }

    


});

?>