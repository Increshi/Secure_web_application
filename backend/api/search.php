<?php
require_once __DIR__ . '/../controllers/UserSearchController.php';

header('Content-Type: application/json');

$query = $_GET['query'] ?? '';

if (!$query) {
    http_response_code(400);
    echo json_encode(["error" => "Query parameter is required"]);
    exit();
}

$controller = new UserSearchController($pdo);
$controller->searchUsers($query);
?>
