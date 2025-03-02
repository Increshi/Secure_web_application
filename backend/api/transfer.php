<?php
require_once __DIR__ . '/../controllers/MoneyTransferController.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$receiver_id = $data['receiver_id'] ?? null;
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


