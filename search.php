<?php
session_start();
require 'includes/db.php'; // Ensure this file initializes the $pdo object

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the 'query' parameter exists in the URL
$searchQuery = $_GET['query'] ?? '';

// Fetch photos based on the search query
if (!empty($searchQuery)) {
    $searchParam = "%$searchQuery%";
    $stmt = $pdo->prepare("SELECT * FROM photos WHERE tags LIKE ? OR description LIKE ?");
    $stmt->execute([$searchParam, $searchParam]);
    $photos = $stmt->fetchAll();
} else {
    $photos = []; // No search query provided
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Photos</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Search Photos</h1>
        <form action="search.php" method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" name="query" class="form-control" placeholder="Enter tags or description" value="<?= htmlspecialchars($searchQuery) ?>">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>

        <?php if (!empty($searchQuery)): ?>
            <h2>Results for "<?= htmlspecialchars($searchQuery) ?>"</h2>
            <?php if (!empty($photos)): ?>
                <div class="row">
                    <?php foreach ($photos as $photo): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <img src="<?= htmlspecialchars($photo['filename']) ?>" class="card-img-top" alt="<?= htmlspecialchars($photo['description']) ?>">
                                <div class="card-body">
                                    <p class="card-text"><?= htmlspecialchars($photo['description']) ?></p>
                                    <p class="card-text"><small class="text-muted">Tags: <?= htmlspecialchars($photo['tags']) ?></small></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-center">No photos found.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Bootstrap 5 JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>