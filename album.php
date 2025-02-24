<?php
session_start();
require 'includes/db.php'; // Ensure this file initializes the $pdo object

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch albums for the logged-in user
$stmt = $pdo->prepare("SELECT * FROM albums WHERE user_id = ?");
$stmt->execute([$userId]);
$albums = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Albums</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="CSS/style.css">
    <script src="JS/script.js" defer></script>
</head>
<body>
    <div class="container mt-5">
        <h1>Your Albums</h1>
        <a href="dashboard.php">Back to Dashboard</a>

        <!-- Album List -->
        <div id="album-list">
            <?php if (empty($albums)): ?>
                <p>No albums found. Create one to get started!</p>
            <?php else: ?>
                <?php foreach ($albums as $album): ?>
                    <div class="album-item card mb-3">
                        <div class="card-body">
                            <h3 class="card-title"><?= htmlspecialchars($album['name']) ?></h3>
                            <button class="btn" onclick="viewAlbum(<?= $album['id'] ?>)">View Album</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Photo List -->
        <div id="photo-list" class="mt-4">
            <!-- Photos will be dynamically loaded here -->
        </div>

        <!-- Create New Album Form -->
        <h2 class="mt-4">Create New Album</h2>
        <form action="create_album.php" method="POST" class="mb-4">
            <div class="form-group">
                <input type="text" name="album_name" class="form-control" placeholder="Album Name" required>
            </div>
            <button type="submit" class="btn mt-2">Create Album</button>
        </form>

        <!-- Modal for Comments -->
        <div id="commentModal" class="modal" style="display:none;">
            <div class="modal-content">
                <form id="commentForm">
                    <textarea name="comment" class="form-control mb-2" placeholder="Add a comment..." required></textarea>
                    <button type="submit" class="btn">Post Comment</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <!-- Custom JS -->
    <script src="JS/script.js"></script>
</body>
</html>