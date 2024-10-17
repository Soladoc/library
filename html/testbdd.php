<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php require '../db.php';
    $pdo = db_connect();
    foreach (
        $pdo->query(
            'table pg_catalog.pg_tables',
            PDO::FETCH_ASSOC
        ) as $row
    ) { ?>
        <pre><?php print_r($row) ?></pre>
    <?php } ?>
</body>
</html>