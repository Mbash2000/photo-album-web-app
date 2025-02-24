<?php
session_start();

// Log the logout action to the database (optional)
if (isset($_SESSION['user_id'])) {
    include 'includes/db.php'; // Ensure this file initializes a PDO connection

    $user_id = $_SESSION['user_id'];
    $logout_time = date('Y-m-d H:i:s');

    try {
        $stmt = $pdo->prepare("INSERT INTO user_logs (user_id, action, action_time) VALUES (:user_id, 'logout', :logout_time)");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':logout_time', $logout_time, PDO::PARAM_STR);
        $stmt->execute();
    } catch (PDOException $e) {
        // Handle error (optional)
        error_log("Logout logging failed: " . $e->getMessage());
    }
}

// Destroy the session
session_destroy();

// Redirect to the homepage
header("Location: index.php");
exit();
?>