<?php

header("Access-Control-Allow-Origin: https://localhost:3000"); // Replace with your frontend URL
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

class MoneyTransferController {
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

    public function transferMoney($receiver_username, $amount, $comment) {
        if ($amount <= 0) {
            die(json_encode(["error" => "Invalid amount"]));
        }

        try {
            $this->pdo->beginTransaction();

            // Check sender's balance
            $stmt = $this->pdo->prepare("SELECT balance FROM users WHERE id = ?");
            $stmt->execute([$this->user->user_id]);
            $sender_balance = $stmt->fetchColumn();

            if ($sender_balance < $amount) {
                die(json_encode(["error" => "Insufficient balance"]));
            }

            // Fetch receiver ID from receiver username
            $stmt = $this->pdo->prepare("SELECT id from users WHERE username = ?");
            $stmt->execute([$receiver_username]);
            $receiver_id = $stmt->fetchColumn();;

            // Deduct from sender
            $stmt = $this->pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
            $stmt->execute([$amount, $this->user->user_id]);

            // Add to receiver
            $stmt = $this->pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $stmt->execute([$amount, $receiver_id]);

            // Log transaction
            $stmt = $this->pdo->prepare("INSERT INTO transactions (sender_id, receiver_id, amount, comment) VALUES (?, ?, ?, ?)");
            $stmt->execute([$this->user->user_id, $receiver_id, $amount, htmlspecialchars($comment, ENT_QUOTES, 'UTF-8')]);

            // Log user activity
            log_user_activity($this->user->user_id, "Transferred $$amount to user $receiver_username with comment: '$comment'");

            $this->pdo->commit();
            echo json_encode(["message" => "Transfer successful"]);

        } catch (Exception $e) {
            $this->pdo->rollBack();
            http_response_code(500);
            echo json_encode(["error" => "Transaction failed",
            "message" => $e->getMessage(),  // Include the error message
            "stack_trace" => $e->getTraceAsString()]);
        }
    }
}


