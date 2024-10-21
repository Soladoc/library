<?php

require_once "../../db.php";
if (isset($_POST['table'])) {

    print 'Votre nom :'.$_POST['table'];
    $table = $_POST['table'];


    $pdo=db_connect();
    try {
        // Construire la requête SQL pour sélectionner toutes les lignes de la table validée
        $query = 'SELECT * FROM pact.' . $table;
        $stmt = $pdo->prepare($query);
        $stmt->execute();
    
        // Récupérer toutes les lignes de la table
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupère les données sous forme de tableau associatif
    
        // Vérifier s'il y a des résultats
        if ($results) {
            echo "<table border='1'>";
            echo "<tr>";
    
            // Afficher les en-têtes du tableau en fonction des colonnes récupérées
            foreach (array_keys($results[0]) as $column) {
                echo "<th>" . htmlspecialchars($column) . "</th>";
            }
            echo "</tr>";
    
            // Afficher chaque ligne
            foreach ($results as $row) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>"; // Sécuriser l'affichage des données
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "Aucune donnée trouvée dans la table $table.";
        }
    
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
    ?>
    $stmt = $pdo->prepare(query: 'SELECT * FROM pact.' . $table );
    $stmt->execute();

    $count = $stmt->fetchColumn(); // Récupérer le nombre de lignes
    echo "Nombre de lignes dans la table $table : $count";
}
else {
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte pro</title>
    <link rel="stylesheet" href="../style/style.css">
</head>

<body>

    <?php
        include("header.php");
    ?>

    <main>
        <!-- Section des offres à la une -->
        <h1>Créer un compte professionnel</h1>
        <section class="connexion">
                <div class="champ-connexion">
                <form action="regardeMaGrosseBdd.php" method="post" enctype="multipart/form-data">

                    <br>
                    <div class="champ">
                        <p>donne le nom de la table </p>
                        <input type="text" placeholder="" id="table" name="table" required>
                    </div>
                   
                    <button type="submit" class="btn-connexion" >regarde</button>
                </form>
            <br /><br>
            </a>
            <br>
            </div>
        </section>
    </main>
    <br>
    <br>
    <br>
    <?php
        include("footer.php");
    ?>


</body>


</html>
<?php
}
?>
