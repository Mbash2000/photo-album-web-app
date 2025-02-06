<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the 'query' parameter exists in the URL
$search_query = isset($_GET['query']) ? $_GET['query'] : '';

// Fetch photos based on the search query
if (!empty($search_query)) {
    $stmt = $conn->prepare("SELECT * FROM photos WHERE tags LIKE ? OR description LIKE ?");
    $search_param = "%$search_query%";
    $stmt->bind_param("ss", $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
    $photos = $result->fetch_all(MYSQLI_ASSOC);
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
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>
    <div class="container">
        <h1>Search Photos</h1>
        <form action="search.php" method="GET">
            <div class="form-group">
                <input type="text" name="query" placeholder="Enter tags or description" value="<?= htmlspecialchars($search_query) ?>">
            </div>
            <button type="submit">Search</button>
        </form>

        <?php if (!empty($search_query)): ?>
            <h2>Results for "<?= htmlspecialchars($search_query) ?>"</h2>
            <?php if (!empty($photos)): ?>
                <div class="photo-list">
                    <?php foreach ($photos as $photo): ?>
                        <div class="photo-item">
                            <img src="<?= htmlspecialchars($photo['filename']) ?>" alt="<?= htmlspecialchars($photo['description']) ?>">
                            <p><?= htmlspecialchars($photo['description']) ?></p>
                            <p>Tags: <?= htmlspecialchars($photo['tags']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No photos found.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>s