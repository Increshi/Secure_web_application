<?php
session_start();

require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    $uploadDir = 'uploads/';
    $fileName = basename($_FILES['profile_image']['name']);
    $targetPath = $uploadDir . $user_id . "_" . time() . "_" . $fileName;

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($_FILES['profile_image']['type'], $allowedTypes)) {
        echo json_encode(["message" => "Invalid file type"]);
        exit();
    }

    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
        $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
        $stmt->execute([$targetPath, $user_id]);

        echo json_encode(["message" => "Profile image updated", "image_url" => $targetPath]);
    } else {
        echo json_encode(["message" => "File upload failed"]);
    }
}
?>
