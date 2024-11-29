<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    require('db0.php');  // Database connection
    $user_id = $_SESSION['user_id'];
    
    $query = $dbhost->prepare("SELECT name, email FROM users WHERE id = ?");
    $query->execute([$user_id]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode([
            'username' => htmlspecialchars($user['name']),
            'email' => htmlspecialchars($user['email'])
        ]);
    } else {
        echo json_encode(['error' => 'User not found']);
    }
} else {
    echo json_encode(['error' => 'User not logged in']);
}
