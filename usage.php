<?php
require_once __DIR__ . '/includes/WordPressDBConnector.php';

try {
    $db = WordPress_DB_Connector::getConnection();
    
    
    $stmt = $db->prepare("SELECT * FROM wp_users WHERE user_email = ?");
    $email = "admin@example.com";
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    
    $result = $stmt->get_result();
    while ($user = $result->fetch_assoc()) {
        echo "Username: " . htmlspecialchars($user['user_login']) . "<br>";
    }
    
    $stmt->close();
} catch (RuntimeException $e) {
    error_log("Database Error: " . $e->getMessage());
}