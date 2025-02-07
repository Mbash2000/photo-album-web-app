
<?php 
session_start();
include 'includes/db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
// Fetch user details
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result()->fetch_assoc();
$username = $user_result['username'];
$stmt->close();
// Fetch user stats (number of albums and photos)
$stmt = $conn->prepare("SELECT COUNT(id) as album_count FROM albums WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$album_result = $stmt->get_result()->fetch_assoc();
$album_count = $album_result['album_count'];
$stmt->close();
$stmt = $conn->prepare("SELECT COUNT(id) as photo_count FROM photos WHERE album_id IN (SELECT id FROM albums WHERE user_id = ?)");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$photo_result = $stmt->get_result()->fetch_assoc();
$photo_count = $photo_result['photo_count'];
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        main {
            background: linear-gradient(135deg, #ff9a9e, #fad0c4);
            animation: changeBackground 10s infinite;
          } 

       @keyframes changeBackground {
          0% { background: linear-gradient(135deg, #ff9a9e, #fad0c4); }
         50% { background: linear-gradient(135deg, #fad0c4, #ff9a9e); }
         100% { background: linear-gradient(135deg, #ff9a9e, #fad0c4); }
         }

        </style>
</head>

<body>
    <div class="d-flex" style="min-height: 100vh;" id="div">
        <!-- Sidebar -->
        <nav class="w-25 p-3 d-flex flex-column justify-content-between">
            <div>
                <h2 class="text-center mb-4"><i class="fas fa-camera me-2"></i>Photo Album</h2>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="dashboard.php" class="text-decoration-none text-black">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="upload.php" class="text-decoration-none text-black">
                            <i class="fas fa-upload me-2"></i>Upload Photo
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="album.php" class="text-decoration-none text-black">
                            <i class="fas fa-folder me-2"></i>Albums
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="search.php" class="text-decoration-none text-black">
                            <i class="fas fa-search me-2"></i>Search Photos
                        </a>
                    </li>
                </ul>
            </div>
            <div>
                <a href="logout.php" class="text-decoration-none" style="color: red;">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-grow-1  p-4">
            <div class="bg-white rounded shadow-sm p-4 mb-4">
                <h1 class="mb-0">Welcome Back, <?= htmlspecialchars($username) ?>!</h1>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card bg-pink text-black rounded shadow-sm">
                        <div class="card-body text-center">
                            <h3><i class="fas fa-folder me-2"></i>Albums</h3>
                            <p class="fs-4"><?= $album_count ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-pink text-black rounded shadow-sm">
                        <div class="card-body text-center">
                            <h3><i class="fas fa-image me-2"></i>Photos</h3>
                            <p class="fs-4"><?= $photo_count ?></p>
                        </div>
                    </div>
                </div>
            </inline>

            <div class="card rounded shadow-sm p-4 mt-4">
                <h2 class="mb-4"><i class="fas fa-clock me-2"></i>Recent Activity</h2>
                <ul class="list-group">
                    <li class="list-group-item">Uploaded a new photo to "Vacation Album"</li>
                    <li class="list-group-item">Created a new album "Nature"</li>
                    <li class="list-group-item">Deleted a photo from "Family Album"</li>
                </ul>
            </div>
        </main>
    </div>

    <!-- Bootstrap 5 JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>

