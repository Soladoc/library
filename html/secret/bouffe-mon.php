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
        <label for="code">Code</label>
        <textarea name="code" id="code"></textarea>
        <button type="submit">Exécuter</button>
    </form>
    <?php } ?>
</body>

</html>