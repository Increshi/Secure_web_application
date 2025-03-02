<?php

include(__DIR__ . '/../config/db.php');
include(__DIR__ . '/../config/auth.php');

header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['email'], $data['password'])) {
    die(json_encode(["error" => "Invalid input"]));
}

$email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
$password = $data['password'];

$stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    $token = generate_jwt($user['id'], $user['username']); // Generate JWT token
    echo json_encode(["token" => $token]);
} else {
    echo json_encode(["error" => "Invalid credentials"]);
}
?>

