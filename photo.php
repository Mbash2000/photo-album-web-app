<?php
session_start();
require 'includes/db.php'; // Ensure this file initializes the $pdo object
require 'includes/Photo.php'; // Include the Photo class

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$photoId = $_GET['id'] ?? null; // Get photo ID from URL

if (!$photoId) {
    header("Location: index.php");
    exit();
}

$photo = new Photo($pdo); // Pass the $pdo object to the Photo class
$photoDetails = $photo->getPhotoDetails($photoId);

if (!$photoDetails) {
    header("Location: index.php");
    exit();
}

$isOwner = ($photoDetails['user_id'] == $userId);
$isPrivate = $photo->isPrivate($photoId);

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    if ($userId) {
        $comment = trim($_POST['comment']);
        if (!empty($comment)) {
            $photo->addComment($photoId, $userId, $comment);
        }
    }
}

// Handle like/unlike
if ($userId && isset($_GET['action'])) {
    if ($_GET['action'] == 'like') {
        $photo->likePhoto($photoId, $userId);
    } elseif ($_GET['action'] == 'unlike') {
        $photo->unlikePhoto($photoId, $userId);
    }
    header("Location: photo.php?id=$photoId");
    exit();
}

// Handle privacy toggle
if ($isOwner && isset($_GET['toggle_privacy'])) {
    $isPrivate = !$photo->isPrivate($photoId);
    $photo->setPrivacy($photoId, $isPrivate);
    header("Location: photo.php?id=$photoId");
    exit();
}

// Handle photo deletion via POST (instead of GET)
if ($isOwner && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    if ($photo->deletePhoto($photoId, $userId)) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = "Error deleting photo.";
    }
}

$comments = $photo->getComments($photoId);
$totalLikes = $photo->getTotalLikes($photoId);
$hasLiked = $userId ? $photo->hasLiked($photoId, $userId) : false;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo - <?= htmlspecialchars($photoDetails['description']) ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <!-- Photo Details -->
        
        <div class="card mb-4">
            <img src="<?= htmlspecialchars($photoDetails['file_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($photoDetails['description']) ?>">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($photoDetails['description']) ?></h5>
                <p class="card-text">Tags: <?= htmlspecialchars($photoDetails['tags']) ?></p>
                <p class="card-text">
                    <small class="text-muted">
                        Uploaded by: <?= htmlspecialchars($photoDetails['username']) ?> on <?= date('M d, Y', strtotime($photoDetails['created_at'])) ?>
                    </small>
                </p>
                <!-- Like/Unlike Button -->
                <?php if ($userId): ?>
                    <a href="photo.php?id=<?= $photoId ?>&action=<?= $hasLiked ? 'unlike' : 'like' ?>" class="btn btn-primary">
                        <i class="fas fa-heart"></i> <?= $hasLiked ? 'Unlike' : 'Like' ?>
                    </a>
                <?php endif; ?>
                <!-- Privacy Toggle (Only for owner) -->
                <?php if ($isOwner): ?>
                    <a href="photo.php?id=<?= $photoId ?>&toggle_privacy=1" class="btn btn-secondary">
                        <i class="fas fa-lock"></i> <?= $isPrivate ? 'Make Public' : 'Make Private' ?>
                    </a>
                    <!-- Delete Button (Only for owner) using POST -->
                    <form method="POST" action="photo.php?id=<?= $photoId ?>" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this photo?');">
                        <input type="hidden" name="delete" value="1">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                <?php endif; ?>
                <!-- Total Likes -->
                <span class="badge bg-success">
                    <i class="fas fa-heart"></i> <?= $totalLikes ?> Likes
                </span>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Comments</h5>
                <?php if ($userId): ?>
                    <form method="POST" class="mb-3">
                        <div class="input-group">
                            <textarea name="comment" class="form-control" placeholder="Add a comment..." required></textarea>
                            <button type="submit" class="btn btn-primary">Post</button>
                        </div>
                    </form>
                <?php endif; ?>
                <ul class="list-group">
                    <?php foreach ($comments as $comment): ?>
                        <li class="list-group-item">
                            <strong><?= htmlspecialchars($comment['username']) ?></strong>
                            <p><?= htmlspecialchars($comment['comment']) ?></p>
                            <small class="text-muted"><?= date('M d, Y H:i', strtotime($comment['created_at'])) ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
