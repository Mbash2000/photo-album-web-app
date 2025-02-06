<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$album_id = $_GET['album_id'];

$stmt = $conn->prepare("SELECT * FROM photos WHERE album_id = ?");
$stmt->bind_param("i", $album_id);
$stmt->execute();
$result = $stmt->get_result();
$photos = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($photos);
?>