<?php

header("Access-Control-Allow-Origin: https://localhost:3000");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle OPTIONS request (preflight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

include(__DIR__ . '/../config/db.php');
include(__DIR__ . '/../config/auth.php');
include(__DIR__ . '/../config/logger.php');

header('Content-Type: application/json');
$headers = getallheaders();
$token = $headers["Authorization"] ?? "";

$user = verify_jwt(str_replace("Bearer ", "", $token));
if (!$user) {
    die(json_encode(["error" => "Unauthorized"]));
}

$stmt = $pdo->prepare("
    SELECT t.id, u1.username AS sender, u2.username AS receiver, t.amount, t.comment, t.timestamp
    FROM transactions t
    JOIN users u1 ON t.sender_id = u1.id
    JOIN users u2 ON t.receiver_id = u2.id
    WHERE t.sender_id = ? OR t.receiver_id = ?
    ORDER BY t.timestamp DESC
");
$stmt->execute([$user->user_id, $user->user_id]);

$transactions = $stmt->fetchAll();

log_user_activity($user->user_id, "Transaction History");

echo json_encode($transactions);
?>
