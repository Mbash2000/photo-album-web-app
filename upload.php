<?php
session_start();
require 'includes/db.php'; // Ensure this file initializes the $pdo object

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch albums for the logged-in user
$stmt = $pdo->prepare("SELECT id, name FROM albums WHERE user_id = ?");
$stmt->execute([$userId]);
$albums = $stmt->fetchAll();

// Handle photo upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $albumId = $_POST['album_id'];
    $description = $_POST['description'];
    $tags = $_POST['tags'];

    // Check if the file input exists and there are no upload errors
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($_FILES["photo"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if the file is an image
        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if ($check !== false) {
            // Create the uploads directory if it doesn't exist
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
                // Insert photo details into the database
                $stmt = $pdo->prepare("INSERT INTO photos (user_id, album_id, filename, description, tags) VALUES (?, ?, ?, ?, ?)");
                if ($stmt->execute([$userId, $albumId, $targetFile, $description, $tags])) {
                    $successMessage = "Photo uploaded successfully!";
                } else {
                    $errorMessage = "Error uploading photo.";
                }
            } else {
                $errorMessage = "Error moving uploaded file.";
            }
        } else {
            $errorMessage = "File is not an image.";
        }
    } else {
        $errorMessage = "No file uploaded or file upload error.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Photo</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center text-dark">Upload Photo</h1>
        <?php if (isset($successMessage)): ?>
            <div class="alert alert-success"><?= $successMessage ?></div>
        <?php endif; ?>
        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-danger"><?= $errorMessage ?></div>
        <?php endif; ?>
        <form action="upload.php" method="POST" enctype="multipart/form-data" onsubmit="return validateUploadForm()">
            <div class="mb-3">
                <label for="album_id" class="form-label"><b>Select Album</b></label>
                <select id="album_id" name="album_id" class="form-select" required>
                    <option value="">-- Select an Album --</option>
                    <?php foreach ($albums as $album): ?>
                        <option value="<?= $album['id'] ?>"><?= htmlspecialchars($album['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="photo" class="form-label"><b>Choose Photo</b></label>
                <input type="file" id="photo" name="photo" class="form-control" accept="image/*" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label"><b>Description</b></label>
                <textarea id="description" name="description" class="form-control" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="tags" class="form-label"><b>Tags</b></label>
                <input type="text" id="tags" name="tags" class="form-control" placeholder="e.g., nature, vacation" required>
            </div>
            <button type="submit" class="btn">Upload Photo</button>
        </form>
        <p class="mt-3"><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
    <!-- Bootstrap 5 JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <!-- Custom JS -->
    <script src="JS/script.js"></script>
</body>
</html>