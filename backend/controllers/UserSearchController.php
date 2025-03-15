<?php

header('Access-Control-Allow-Origin: http://localhost:3000'); // Replace with your frontend URL
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle OPTIONS request (preflight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/logger.php';

class UserSearchController {
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
    }

    public function searchUsers($query) {
        $query = htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); // Prevent XSS

        // Debugging: Check if headers were already sent
        // if (headers_sent($file, $line)) {
        //     die("Headers already sent in $file on line $line");
        // }
        $stmt = $this->pdo->prepare("SELECT id, username, email FROM users WHERE username LIKE ? OR email LIKE ?");
        $stmt->execute(["%$query%", $query]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        

        // Log activity
        log_user_activity($this->user->user_id, "User searched for: $query");


        echo json_encode($results);
        exit();
    }
}



