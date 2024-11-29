<?php
require_once 'util.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2>GET</h2>
    <?php dbg_print($_GET) ?>
    <h2>POST</h2>
    <?php dbg_print($_POST) ?>
    <h2>FILES</h2>
    <?php dbg_print($_FILES) ?>
</body>
</html>