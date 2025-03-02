<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


require_once __DIR__ . '/../vendor/autoload.php';
$secret_key = "your_secret_key"; // Change this in production

function generate_jwt($user_id, $username) {
    global $secret_key;
    $payload = [
        "user_id" => $user_id,
        "username" => $username,
        "exp" => time() + 3600 // Token expires in 1 hour
    ];
    return JWT::encode($payload, $secret_key, 'HS256');
}


function verify_jwt($token) {
    global $pdo;

    try {
        $secret_key = "your_secret_key"; // Ensure this is the same key used to generate JWT
        $decoded = Firebase\JWT\JWT::decode($token, new Firebase\JWT\Key($secret_key, 'HS256'));

        // Check if token is blacklisted
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM token_blacklist WHERE token = ?");
        $stmt->execute([$token]);
        if ($stmt->fetchColumn() > 0) {
            http_response_code(401);
            die(json_encode(["error" => "Token is blacklisted"]));
        }

        return $decoded;
    } catch (Exception $e) {
        return false;
    }
}

?>
