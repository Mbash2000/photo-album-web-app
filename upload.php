<?php
/*session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch albums for the logged-in user
$stmt = $conn->prepare("SELECT id, name FROM albums WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$albums = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle photo upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $album_id = $_POST['album_id'];
    $description = $_POST['description'];
    $tags = $_POST['tags'];

    // Handle file upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the file is an image
        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if ($check !== false) {
            // Resize and save the image
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                // Insert photo details into the database
                $stmt = $conn->prepare("INSERT INTO photos (album_id, filename, description, tags) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isss", $album_id, $target_file, $description, $tags);

                if ($stmt->execute()) {
                    $success_message = "Photo uploaded successfully!";
                } else {
                    $error_message = "Error uploading photo: " . $stmt->error;
                }

                $stmt->close();
            } else {
                $error_message = "Error moving uploaded file.";
            }
        } else {
            $error_message = "File is not an image.";
        }
    } else {
        $error_message = "No file uploaded or file upload error.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Photo</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>
    <div class="container">
        <h1>Upload Photo</h1>
        <?php if (isset($success_message)): ?>
            <div class="success-message"><?= $success_message ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?= $error_message ?></div>
        <?php endif; ?>
        <form action="upload.php" method="POST" enctype="multipart/form-data" onsubmit="return validateUploadForm()">
            <div class="form-group">
                <label for="album_id">Select Album</label>
                <select id="album_id" name="album_id" required>
                    <option value="">-- Select an Album --</option>
                    <?php foreach ($albums as $album): ?>
                        <option value="<?= $album['id'] ?>"><?= htmlspecialchars($album['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="photo">Choose Photo</label>
                <input type="file" id="photo" name="photo" accept="image/*" required style="padding-right: 10px;">
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="tags">Tags (comma-separated)</label>
                <input type="text" id="tags" name="tags" placeholder="e.g., nature, vacation" required>
            </div>
            <button type="submit">Upload Photo</button>
        </form>
        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
    <script src="JS/script.js"></script>
</body>
</html>*/

session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch albums for the logged-in user
$stmt = $conn->prepare("SELECT id, name FROM albums WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$albums = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle photo upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $album_id = $_POST['album_id'];
    $description = $_POST['description'];
    $tags = $_POST['tags'];

    // Check if the file input exists and there are no upload errors
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the file is an image
        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if ($check !== false) {
            // Create the uploads directory if it doesn't exist
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }

            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                // Insert photo details into the database
                $stmt = $conn->prepare("INSERT INTO photos (album_id, filename, description, tags) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isss", $album_id, $target_file, $description, $tags);

                if ($stmt->execute()) {
                    $success_message = "Photo uploaded successfully!";
                } else {
                    $error_message = "Error uploading photo: " . $stmt->error;
                }

                $stmt->close();
            } else {
                $error_message = "Error moving uploaded file.";
            }
        } else {
            $error_message = "File is not an image.";
        }
    } else {
        $error_message = "No file uploaded or file upload error.";
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
        <h1 class="text-center">Upload Photo</h1>
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?= $success_message ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= $error_message ?></div>
        <?php endif; ?>
        <form action="upload.php" method="POST" enctype="multipart/form-data" onsubmit="return validateUploadForm()">
            <div class="mb-3">
                <label for="album_id" class="form-label">Select Album</label>
                <select id="album_id" name="album_id" class="form-select" required>
                    <option value="">-- Select an Album --</option>
                    <?php foreach ($albums as $album): ?>
                        <option value="<?= $album['id'] ?>"><?= htmlspecialchars($album['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="photo" class="form-label">Choose Photo</label>
                <input type="file" id="photo" name="photo" class="form-control" accept="image/*" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="tags" class="form-label">Tags (comma-separated)</label>
                <input type="text" id="tags" name="tags" class="form-control" placeholder="e.g., nature, vacation" required>
            </div>
            <button type="submit">Upload Photo</button>
        </form>
        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </div>

    <!-- Bootstrap 5 JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <!-- Custom JS -->
    <script src="JS/script.js"></script>
</body>
</html>