<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch albums for the logged-in user
$stmt = $conn->prepare("SELECT * FROM albums WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$albums = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Albums</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/script.js" defer></script>
</head>
<body>
    <div class="container">
        <h1>Your Albums</h1>
        <a href="dashboard.php">Back to Dashboard</a>

        <!-- Album List -->
        <div id="album-list">
            <?php if (empty($albums)): ?>
                <p>No albums found. Create one to get started!</p>
            <?php else: ?>
                <?php foreach ($albums as $album): ?>
                    <div class="album-item">
                        <h3><?= htmlspecialchars($album['name']) ?></h3>
                        <button onclick="viewAlbum(<?= $album['id'] ?>)">View Album</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Photo List -->
        <div id="photo-list">
            <!-- Photos will be dynamically loaded here -->
        </div>

        <!-- Create New Album Form -->
        <h2>Create New Album</h2>
        <form action="create_album.php" method="POST">
            <input type="text" name="album_name" placeholder="Album Name" required>
            <button type="submit">Create Album</button>
        </form>
    </div>
</body>
</html>