<?php
session_start();
include 'includes/db.php'; // Make sure this file initializes a PDO connection

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $album_name = $_POST['album_name'];

    try {
        // Prepare the SQL statement
        $stmt = $pdo->prepare("INSERT INTO albums (user_id, name) VALUES (:user_id, :album_name)");
        
        // Bind the parameters
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':album_name', $album_name, PDO::PARAM_STR);
        
        // Execute the statement
        if ($stmt->execute()) {
            header("Location: album.php");
        } else {
            echo "Error: Unable to execute the query.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>