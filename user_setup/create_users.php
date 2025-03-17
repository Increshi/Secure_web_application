<?php
// Wait for MySQL to be ready
$host = getenv('MYSQL_HOST') ?: 'mysql_db';
$port = getenv('MYSQL_PORT') ?: '3306';
$user = getenv('MYSQL_USER') ?: 'rushi';
$pass = getenv('MYSQL_PASSWORD') ?: 'Rushi@234';
$dbname = getenv('MYSQL_DATABASE') ?: 'app_db';

// Try to connect until successful
$maxTries = 10;
$connected = false;

echo "Waiting for MySQL to be ready...\n";

for ($i = 0; $i < $maxTries; $i++) {
    try {
        $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check if database exists, create if not
        $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
        $pdo->exec("USE $dbname");
        
        $connected = true;
        echo "Connected to MySQL successfully!\n";
        break;
    } catch (PDOException $e) {
        echo "Connection attempt $i failed: " . $e->getMessage() . ", retrying in 5 seconds...\n";
        sleep(5);
    }
}

if (!$connected) {
    echo "Failed to connect to MySQL after $maxTries attempts. Exiting.\n";
    exit(1);
}

// Check if users table exists, create if not
try {
    $result = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($result->rowCount() == 0) {
        echo "Creating users table...\n";
        $sql = "CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            balance DECIMAL(10,2) DEFAULT 100.00,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if ($pdo->exec($sql) !== false) {
            echo "Users table created successfully.\n";
        } else {
            echo "Error creating users table.\n";
            exit(1);
        }
    }
} catch (PDOException $e) {
    echo "Error checking/creating table: " . $e->getMessage() . "\n";
    exit(1);
}

// Predefined user data
$users = [
    ["Alice Johnson", "alicej", "alice@example.com", "password123"],
    ["Bob Smith", "bobsmith", "bob@example.com", "securepass"],
    ["Charlie Brown", "charlieb", "charlie@example.com", "mypassword"],
    ["David White", "davidw", "david@example.com", "pass1234"],
    ["Emma Davis", "emmad", "emma@example.com", "qwertyui"],
    ["Frank Harris", "frankh", "frank@example.com", "letmein12"],
    ["Grace Wilson", "gracew", "grace@example.com", "secureme"],
    ["Henry Moore", "henrym", "henry@example.com", "helloWorld"],
    ["Ivy Lewis", "ivyl", "ivy@example.com", "testpass"],
    ["Jack Walker", "jackw", "jack@example.com", "strongpass"],
    ["Karen Thomas", "karent", "karen@example.com", "passw0rd"],
    ["Leo Martin", "leom", "leo@example.com", "goodpass"],
    ["Mia Roberts", "miar", "mia@example.com", "mypassword1"],
    ["Noah Young", "noahy", "noah@example.com", "safe123"],
    ["Olivia King", "oliviak", "olivia@example.com", "secure567"],
    ["Peter Adams", "petera", "peter@example.com", "randompass"],
    ["Quinn Scott", "quinns", "quinn@example.com", "testsecure"],
    ["Rachel Baker", "rachelb", "rachel@example.com", "passwordsafe"],
    ["Samuel Green", "samuelg", "samuel@example.com", "mypassword2"],
    ["Tina Carter", "tinac", "tina@example.com", "strongerpass"]
];

// Insert users
foreach ($users as $userData) {
    try {
        // Check if user already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$userData[1], $userData[2]]);
        
        if ($stmt->rowCount() == 0) {
            // Hash the password
            $hashedPassword = password_hash($userData[3], PASSWORD_DEFAULT);
            
            // Insert user with name, username, email, password, and default balance of 100
            $stmt = $pdo->prepare("INSERT INTO users (name, username, email, password, balance) VALUES (?, ?, ?, ?, 100)");
            $stmt->execute([$userData[0], $userData[1], $userData[2], $hashedPassword]);
            
            echo "User {$userData[1]} created successfully.\n";
        } else {
            echo "User {$userData[1]} already exists, skipping.\n";
        }
    } catch (PDOException $e) {
        echo "Error processing user {$userData[1]}: " . $e->getMessage() . "\n";
    }
}

echo "User creation process completed.\n";
$pdo = null;