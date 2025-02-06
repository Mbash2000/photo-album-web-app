<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect to the dashboard if logged in
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Photo Album App</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>
    <div class="welcome-container">
        <h1 id="welcome-text"></h1>
        <p>Manage your photos, create albums, and share memories with ease.</p>
        <div class="action-buttons">
            <a href="login.php" class="btn">Login</a>
            <a href="register.php" class="btn">Register</a>
        </div>
    </div>

    <script src="JS/script.js"></script>
</body>
</html>