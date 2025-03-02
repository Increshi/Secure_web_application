<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Ensure JWT library is included
require_once __DIR__ . '/../config/db.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Content-Type: application/json");

class ProfileController {
    private $pdo;
    public $user_id; // Public to access outside class if needed

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->authenticate();
    }

    private function authenticate() {
        $headers = apache_request_headers();
        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(["message" => "Unauthorized"]);
            exit();
        }

        $authHeader = $headers['Authorization'];
        $jwt = str_replace("Bearer ", "", $authHeader);

        try {
            $secret_key = "your_secret_key"; // Set this securely
            $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));
            $this->user_id = $decoded->user_id;
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(["message" => "Invalid Token"]);
            exit();
        }
    }

    // Fetch the logged-in user's profile
    public function getProfile() {
        $stmt = $this->pdo->prepare("SELECT username, email, biography AS bio, profile_image FROM users WHERE id = ?");
        $stmt->execute([$this->user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "User not found"]);
        }
    }

    // Fetch another user's profile by user ID
    public function getUserProfile($user_id) {
        $stmt = $this->pdo->prepare("SELECT username, biography AS bio, profile_image FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "User not found"]);
        }
    }

    // Update user profile (excluding username)
    public function updateProfile() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($data['biography'])) {
            $bio = htmlspecialchars($data['biography'], ENT_QUOTES, 'UTF-8'); // Prevent XSS
            $stmt = $this->pdo->prepare("UPDATE users SET biography = ? WHERE id = ?");
            $stmt->execute([$bio, $this->user_id]);
        }

        if (!empty($_FILES['profile_image']['name'])) {
            $this->uploadProfileImage($_FILES['profile_image']);
        }

        echo json_encode(["message" => "Profile updated successfully"]);
    }

    // Upload and store profile image securely
    private function uploadProfileImage($file) {
        $target_dir = __DIR__ . "/../uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $valid_extensions = ["jpg", "jpeg", "png"];
        if (!in_array($imageFileType, $valid_extensions)) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid file format. Allowed: JPG, JPEG, PNG"]);
            exit();
        }

        $new_filename = uniqid() . "." . $imageFileType;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            $stmt = $this->pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
            $stmt->execute([$new_filename, $this->user_id]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "File upload failed"]);
            exit();
        }
    }
}

// Create Controller instance and handle requests
$profileController = new ProfileController($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['user_id']) && $_GET['user_id'] != $profileController->user_id) {
        // Viewing another user's profile
        $profileController->getUserProfile($_GET['user_id']);
    } else {
        // Viewing own profile
        $profileController->getProfile();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profileController->updateProfile();
}
?>
