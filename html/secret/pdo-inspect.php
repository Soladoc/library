<?php
require_once 'db.php';

$stmt = notfalse(DB\connect()->query('select offre_get_concrete(11);'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <h1><code><?= $stmt->queryString ?></code></h1>
    <?php dbg_print($stmt->fetchAll()) ?>
</head>
<body>
    
</body>
</html>