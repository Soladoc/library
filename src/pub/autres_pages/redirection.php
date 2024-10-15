<!--
    @brief: fichier qui redirige un professionel vers la pagef de création adapté a l'offre qu'il 
    shouaite crééer
    @author: Benjamin dummont-Girard
--> 
<?php
    if (isset($_POST)) {
        switch ($_POST['type_offre']) {
            case 'spectacle':
                header("location : creation_spectacle.php");
            break;
            case 'parc_atraction':
                header("location : creation_spectacle.php");
            break;
            case 'visites':
                header("location : creation_spectacle.php");
            break;
            case 'restauration':
                header("location : creation_spectacle.php");
            break;
            case 'activite':
                header("location : creation_spectacle.php");
            break;
            default:
                //TODO fais une erreur g encore la flemme mais c pas grave g confiance en toi brother!
            break;
                
        }
    }else {
        // TODO fais une erreur la g la flemme
    }
?>