<?php

header("Access-Control-Allow-Origin: *"); // Replace with your frontend URL
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle OPTIONS request (preflight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

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
