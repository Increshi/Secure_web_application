<?php
include 'db.php';

function log_user_activity($user_id, $page) {
    global $pdo;
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $timestamp = date('Y-m-d H:i:s');

    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, webpage, timestamp, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $page, $timestamp, $ip_address]);
}
?>
