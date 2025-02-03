<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $album_name = $_POST['album_name'];

    $stmt = $conn->prepare("INSERT INTO albums (user_id, name) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $album_name);

    if ($stmt->execute()) {
        header("Location: album.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>