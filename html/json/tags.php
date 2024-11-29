<?php
require_once '../include/queries.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $tags = DB\query_tags($id);
    echo json_encode($tags);
} else {
    echo json_encode([]);
}
?>