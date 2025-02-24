<?php
session_start();
include 'includes/db.php'; // Ensure this file initializes a PDO connection

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$album_id = $_GET['album_id'];

try {
    // Prepare the SQL statement
    $stmt = $pdo->prepare("SELECT * FROM photos WHERE album_id = :album_id");
    
    // Bind the parameter
    $stmt->bindParam(':album_id', $album_id, PDO::PARAM_INT);
    
    // Execute the statement
    $stmt->execute();
    
    // Fetch all results as an associative array
    $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Output the results as JSON
    echo json_encode($photos);
} catch (PDOException $e) {
    // Handle any errors
    echo json_encode(['error' => $e->getMessage()]);
}
?>