<?php

header('Access-Control-Allow-Origin: http://localhost:3000'); // Replace with your frontend URL
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle OPTIONS request (preflight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

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
        $stmt = $this->pdo->prepare("SELECT name, username, email, biography AS bio, profile_image FROM users WHERE id = ?");
        $stmt->execute([$this->user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if($user['profile_image'])
            {
                $user['profile_image'] = $user['profile_image'];
            }
            else{
                $user['profile_image'] = "../images/user_image.jpg";
            }
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "User not found"]);
        }
    }

    // Fetch another user's profile by user ID
    public function getUserProfile($user_id) {
        $stmt = $this->pdo->prepare("SELECT name, username, email, biography AS bio, profile_image FROM users WHERE id != ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($user) {
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "User not found"]);
        }
    }

    // Update user profile (excluding username)
    public function updateProfile() {
        $name = $_POST['name'] ?? '';
        $bio = $_POST['biography'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($bio != '') {
            $bio = htmlspecialchars($bio, ENT_QUOTES, 'UTF-8'); // Prevent XSS
            $stmt = $this->pdo->prepare("UPDATE users SET biography = ? WHERE id = ?");
            $stmt->execute([$bio, $this->user_id]);
        }

        if ($name != '') {
            $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); // Prevent XSS
            $stmt = $this->pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
            $stmt->execute([$name, $this->user_id]);
        }

        if ($email != '') {
            $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); // Prevent XSS
            $stmt = $this->pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
            $stmt->execute([$email, $this->user_id]);
        }

        if ($password != '') {
            $password = htmlspecialchars($password, ENT_QUOTES, 'UTF-8'); // Prevent XSS
            $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$password, $this->user_id]);
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
    if (isset($_GET['user_id']) && $_GET['user_id'] == 'ALL') {
        // Viewing another user's profile
        $profileController->getUserProfile($profileController->user_id);
    } else {
        // Viewing own profile
        $profileController->getProfile();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profileController->updateProfile();
}
?>
