<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style.css">
    <title>Création d'une offre</title>
</head>
<?php 
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    if (isset($_POST)) { ?>
        <body>
            <?php include("header.html") ?>
            <section id="titre_creation_offre" >
                <?php 
                /*
                    afin d'afficher le tire correctement j'effectu un switch sur le type d'offre
                    je ne fais pas <h1>Créer <?php echo ucfirst(str_replace('_',' ',$_POST['type_offre'])) ; \⸮><h1>
                    car il faut gérer le préfixe "un/une" qui nécéssite de toute facons un switch
                */
                $type_offre_affichable = '';
                switch ($_POST["type_offre"]) {
                    case 'spectacle':
                        $type_offre_affichable = "un spectacle";
                    break;
                    case 'parc_attraction':
                        $type_offre_affichable = "un parc d'atraction";
                    break;
                    case 'visite':
                        $type_offre_affichable = "une visite";
                    break;
                    case 'restauration':
                        $type_offre_affichable = "une restauration";
                    break;
                    case 'activite':
                        $type_offre_affichable = "une activité";
                    break;
                    default:
                        $type_offre_affichable = "une offre";
                    break;
                    }
                ?>
                <h1>Créer <?php echo $type_offre_affichable; ?></h1>
            </section>
    </body>
    <?php
    }else{ ?>
        <body>
            <h1>Erreur de methode d'acces</h1>
            <p>Une erreur dans la requette de la page est survenu merci de réessayer plus tard</p>
        </body>
    <?php }
?>
    <footer>
        <?php
            //include("footer.html"); TODO
        ?>
    </footer>

</html>