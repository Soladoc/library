<?php
require_once 'component/Page.php';

$page = new Page('regarde dans la bdd');

function quote(string $name): string
{
    return '"' . str_replace('"', '""', $name) . '"';
}

require_once 'db.php';
if ($table = $_GET['table'] ?? null) {
    $results = DB\connect()->query('table ' . quote($_GET['schema'] ?: 'pact') . '.' . quote($table))->fetchAll();
    // Vérifier s'il y a des résultats
    if ($results) {
        echo "<table border='1'>";
        echo '<tr>';

        // Afficher les en-têtes du tableau en fonction des colonnes récupérées
        foreach (array_keys($results[0]) as $column) {
            echo '<th>' . htmlspecialchars($column ?? '') . '</th>';
        }
        echo '</tr>';

        // Afficher chaque ligne
        foreach ($results as $row) {
            echo '<tr>';
            foreach ($row as $value) {
                echo '<td>' . htmlspecialchars($value ?? '') . '</td>';  // Sécuriser l'affichage des données
            }
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo "<p>Aucune donnée trouvée dans la table $table.</p>";
    }
} else {
?>
<!DOCTYPE html>
<html lang="fr">

<?php $page->put_head() ?>

<body>
    <?php $page->put_header() ?>
    <main>
        <!-- Section des offres à la une -->
        <h1>regarde dans la bdd</h1>
        <section class="connexion">
            <div class="champ-connexion">
                <form action="regardeMaGrosseBdd.php" method="get" enctype="multipart/form-data">

                    <br>
                    <div class="champ"> 
                        <p>donne le nom de la table *</p>
                        <input type="text" placeholder="" id="table" name="table" required>
                        <p>et un schema</p>
                        <input type="text" placeholder="pact" id="schema" name="schema">
                    </div>

                    <button type="submit" class="btn-connexion">regarde</button>
                </form>
                <br ><br>
                </a>
                <br>
            </div>
        </section>
    </main>
    <?php $page->put_footer() ?>

</body>

</html>
<?php
}
?>