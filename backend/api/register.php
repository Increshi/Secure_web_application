<?php
include(__DIR__ . '/../config/db.php');

header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['username'], $data['email'], $data['password'])) {
    http_response_code(400);
    die(json_encode(["error" => "Invalid input"]));
}

$username = htmlspecialchars($data['username'], ENT_QUOTES, 'UTF-8'); // Prevents XSS
$email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
$password = password_hash($data['password'], PASSWORD_BCRYPT);

if (!$email) {
    http_response_code(400);
    die(json_encode(["error" => "Invalid email format"]));
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
$stmt->execute([$username, $email]);
$exists = $stmt->fetchColumn();

if ($exists > 0) {
    http_response_code(400);
    die(json_encode(["error" => "Username or Email already taken"]));
}


$stmt = $pdo->prepare("INSERT INTO users (username, email, password, balance) VALUES (?, ?, ?, 100)");

try {
    $stmt->execute([$username, $email, $password]);
    http_response_code(200);
    echo json_encode(["message" => "User registered successfully"]);
} catch (PDOException $e) {
    http_response_code(400);
    echo json_encode(["error" => "Database error occurred"]);
}
?>
