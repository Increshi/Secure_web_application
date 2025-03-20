<?php

header("Access-Control-Allow-Origin: *"); // Replace with your frontend URL
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle OPTIONS request (preflight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../controllers/MoneyTransferController.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$receiver_id = $data['receiverId'] ?? null;
$amount = $data['amount'] ?? null;
$comment = $data['comment'] ?? '';

if (!$receiver_id || !$amount) {
    http_response_code(400);
    echo json_encode(["error" => "Receiver ID and amount are required"]);
    exit();
}

$controller = new MoneyTransferController($pdo);
$controller->transferMoney($receiver_id, (float)$amount, $comment);
?>


