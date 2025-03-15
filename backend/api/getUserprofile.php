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

class UserInfoController {
    private $pdo;
    private $user;
    private $token;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->authenticate();
    }

    // Authenticate the token passed in the Authorization header
    private function authenticate() {
        // Get the Authorization header from the request
        $headers = getallheaders();
        $token = $headers["Authorization"] ?? "";

        // Verify the JWT token using the verify_jwt function
        $this->user = verify_jwt(str_replace("Bearer ", "", $token));

        // If the token is invalid, return unauthorized
        if (!$this->user) {
            http_response_code(401);  // Unauthorized
            die(json_encode(["error" => "Unauthorized"]));
        }

        // Store the token for use in token blacklisting check
        $this->token = str_replace("Bearer ", "", $token);
    }

    // Fetch and return user info from the database
    public function getUserProfile() {
        // Retrieve user ID from the decoded JWT payload
        $user_id = $this->user->user_id;

        // Check if the token is blacklisted
        $stmt = $this->pdo->prepare("SELECT * FROM token_blacklist WHERE token = ?");
        $stmt->execute([$this->token]);
        if ($stmt->rowCount() > 0) {
            http_response_code(401); // Unauthorized
            echo json_encode(["error" => "Token is blacklisted"]);
            exit();
        }

        // Fetch user information from the database using the user ID
        $stmt = $this->pdo->prepare("SELECT id, name, username, email, balance, profile_image, biography FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Return the user info as a JSON response
            // Only send non-sensitive data
    $response = [
        "fullname" => $user['name'],
        "username" => $user['username'],
        "balance" => $user['balance'],
        "profile_image" => $user['profile_image'],
        "bio" => $user['biography'],
        "email" => $user['email']
    ];
    
    echo json_encode($response);  // Send the filtered response
        } else {
            http_response_code(404); // Not Found
            echo json_encode(["error" => "User not found"]);
        }
    }

    public function updateUserProfile() {
        $user_id = $this->user->user_id;

        // Check if the token is blacklisted
        $stmt = $this->pdo->prepare("SELECT * FROM token_blacklist WHERE token = ?");
        $stmt->execute([$this->token]);
        if ($stmt->rowCount() > 0) {
            http_response_code(401); // Unauthorized
            echo json_encode(["error" => "Token is blacklisted"]);
            exit();
        }

        // $data = file_get_contents("php://input");
        // $decodedData = json_decode($data, true);
        // $fname = $decodedData['name'];
        // $fullname = $_POST['name'];
        // $response = ['uname' => $fname];

        // $response = [ 'uname' => 'Vignesh 123'];
        
        // echo json_encode($response);

        // fill here to get and process the data;
    }


}

// Handle the request
$userInfoController = new UserInfoController($pdo);
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $userInfoController->getUserProfile();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userInfoController->updateUserProfile();
    // $fullname = $_POST['name'];

    // $response = [ 'uname' => $fullname];

}
?>
