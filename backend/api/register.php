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

header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['fullname'], $data['username'], $data['email'], $data['password'])) {
    http_response_code(400);
    die(json_encode(["error" => "Invalid input"]));
}

$fullname = htmlspecialchars($data['fullname'], ENT_QUOTES, 'UTF-8');
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


$stmt = $pdo->prepare("INSERT INTO users (name, username, email, password, balance) VALUES (?, ?, ?, ?, 100)");

try {
    $stmt->execute([$fullname, $username, $email, $password]);
    http_response_code(200);
    echo json_encode(["message" => "User registered successfully"]);
} catch (PDOException $e) {
    http_response_code(400);
    echo json_encode(["error" => "Database Error Occured"]);
    // echo json_encode(["error" => $e->getMessage()]);
}
?>
