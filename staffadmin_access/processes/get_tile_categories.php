<?php
// get_tile_categories.php
require_once '../../connection/connection.php';
$stmt = $db_connection->query('SELECT category_id, category_name FROM tile_categories ORDER BY category_name ASC');
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
