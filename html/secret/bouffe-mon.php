<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    if ($_POST) { ?>
        <pre><samp><?php eval($_POST['code']) ?></samp></pre>
    <?php } else { ?>
    <form method="post">
        <p><label for="code">Code</label></p>
        <p><textarea name="code" id="code" rows="23" cols="120"></textarea></p>
        <p><button type="submit">Exécuter</button></p>
    </form>
    <?php } ?>
</body>

</html>