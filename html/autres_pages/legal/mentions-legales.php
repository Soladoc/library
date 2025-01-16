<?php
require_once 'component/Page.php';
require_once 'Parsedown.php';

$page = new Page('Mentions légales', main_class: 'text');

// 1 - 1.5 page


$page->put( function(){
    $pd = new Parsedown();
    ?>
    <section class="centrer-enfants" style="text-align: center;">
    <?=
        $pd->text(file_get_contents('doc/mentions-legales.md', use_include_path: true));
    ?>
    </section>
    <?php
    }
);
?>
