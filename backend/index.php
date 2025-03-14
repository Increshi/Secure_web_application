<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Get the requested API route
$request = $_GET['request'] ?? '';

// Define valid API endpoints
$routes = [
    'register' => 'api/register.php',
    'login' => 'api/login.php',
    'get_profile' => 'api/profile.php',
    'update_profile' => 'api/profile.php',
    'transfer_money' => 'api/transfer.php',
    'search_user' => 'api/search.php',
    'transaction_history' => 'api/transactions.php',
    'logout' => 'api/LogoutController.php'
    
];

// Check if request exists in routes
if (array_key_exists($request, $routes)) {
    require_once $routes[$request];
} else {
    echo json_encode(["error" => "Invalid API request"]);
}
?>
