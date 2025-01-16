<?php
require_once 'component/Page.php';
require_once 'Parsedown.php';

$page = new Page('Conditions Générales d\'Utilisation', main_class: 'text');

// 4 pages
// communes au membre et pro

$page->put( function(){
    $pd = new Parsedown();
    ?>
    <section class="centrer-enfants mention-legales-sections" >
        <div>
            <?=
                $pd->text(file_get_contents('doc/cgv.md', use_include_path: true));
            ?>     
        </div>
    </section>
    <?php
    }
);
?>