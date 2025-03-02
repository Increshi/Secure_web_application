<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

class LogoutController {
    private $pdo;
    private $user;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->authenticate();
    }

    private function authenticate() {
        $headers = getallheaders();
        $token = $headers["Authorization"] ?? "";
        $this->user = verify_jwt(str_replace("Bearer ", "", $token));

        if (!$this->user) {
            http_response_code(401);
            die(json_encode(["error" => "Unauthorized"]));
        }
        $this->token = str_replace("Bearer ", "", $token);
    }

    public function logout() {
        // Decode token to get expiry time
        $decoded = verify_jwt($this->token);
        if (!$decoded || !isset($decoded->exp)) {
            die(json_encode(["error" => "Invalid token"]));
        }

        $expiry = date('Y-m-d H:i:s', $decoded->exp);

        // Store token in blacklist
        $stmt = $this->pdo->prepare("INSERT INTO token_blacklist (token, expiry) VALUES (?, ?)");
        $stmt->execute([$this->token, $expiry]);

        echo json_encode(["message" => "Logout successful. Token is blacklisted."]);
    }
}

// Handle request
$logoutController = new LogoutController($pdo);
$logoutController->logout();
?>
