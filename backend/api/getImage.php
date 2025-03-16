<?php
header('Access-Control-Allow-Origin: http://localhost:3000'); // Replace with your frontend URL
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle OPTIONS request (preflight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include necessary files for JWT, DB connection, and token verification
require_once __DIR__ . '/../config/db.php';  // Assuming your DB connection file
require_once __DIR__ . '/../config/auth.php';  // JWT utility functions (verify_jwt)

// Serve image from a specific path
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['image'])) {
    $image_name = $_GET['image'];
    $image_path = __DIR__ . '/../uploads/' . $image_name;

    if (file_exists($image_path)) {
        // Set the appropriate content type for the image
        header('Content-Type: ' . mime_content_type($image_path));
        readfile($image_path);
        exit;
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Image not found"]);
    }
}


?>
