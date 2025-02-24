<?php
session_start();
require 'includes/db.php'; // Ensure this file initializes the $pdo object

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Handle photo deletion via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_photo_id'])) {
    $delete_photo_id = $_POST['delete_photo_id'];
    // Delete the photo only if it belongs to an album owned by the current user.
    $stmt = $pdo->prepare("DELETE FROM photos WHERE id = ? AND album_id IN (SELECT id FROM albums WHERE user_id = ?)");
    $stmt->execute([$delete_photo_id, $userId]);
    header("Location: dashboard.php");
    exit();
}

// Fetch user details
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$userId]);
$userResult = $stmt->fetch();
$username = $userResult['username'];

// Fetch user stats (number of albums and photos)
$stmt = $pdo->prepare("SELECT COUNT(id) as album_count FROM albums WHERE user_id = ?");
$stmt->execute([$userId]);
$albumResult = $stmt->fetch();
$albumCount = $albumResult['album_count'];

$stmt = $pdo->prepare("SELECT COUNT(id) as photo_count FROM photos WHERE album_id IN (SELECT id FROM albums WHERE user_id = ?)");
$stmt->execute([$userId]);
$photoResult = $stmt->fetch();
$photoCount = $photoResult['photo_count'];

// Fetch user's photos (join photos and albums so that we only get photos from the user's albums)
$stmt = $pdo->prepare("SELECT photos.id, photos.file_path, photos.description 
                       FROM photos 
                       JOIN albums ON photos.album_id = albums.id
                       WHERE albums.user_id = ?");
$stmt->execute([$userId]);
$userPhotos = $stmt->fetchAll();
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

        /* Sidebar styling */
        nav {
            width: 250px;
            transition: width 0.3s ease;
            position: relative;
        }

        nav.collapsed {
            width: 60px;
        }

        nav.collapsed .nav-text {
            display: none;
        }

        nav.collapsed h2 {
            font-size: 1.2rem;
            text-align: center;
        }

        nav.collapsed .fa-camera {
            margin-right: 0;
        }

        nav.collapsed ul li a {
            justify-content: center;
        }

        .toggle-btn {
            cursor: pointer;
            position: absolute;
            top: 2px;
            right: -0.5px;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 30%;
            padding: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="d-flex" style="min-height: 100vh;">
        <!-- Sidebar -->
        <nav id="sidebar" class="p-3 d-flex flex-column justify-content-between">
            <!-- Toggle Button -->
            <div class="toggle-btn" onclick="toggleSidebar()" style="background-color: #fad0c4;">
                <i class="fas fa-bars"></i>
            </div>

            <div>
                <h2 class="text-center mb-4"><i class="fas fa-camera me-2"></i><span class="nav-text">Photo Album</span></h2>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="dashboard.php" class="text-decoration-none text-black d-flex align-items-center">
                            <i class="fas fa-home me-2"></i><span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="upload.php" class="text-decoration-none text-black d-flex align-items-center">
                            <i class="fas fa-upload me-2"></i><span class="nav-text">Upload Photo</span>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="album.php" class="text-decoration-none text-black d-flex align-items-center">
                            <i class="fas fa-folder me-2"></i><span class="nav-text">Albums</span>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="search.php" class="text-decoration-none text-black d-flex align-items-center">
                            <i class="fas fa-search me-2"></i><span class="nav-text">Search Photos</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div>
                <a href="logout.php" class="text-decoration-none d-flex align-items-center" style="color: red;">
                    <i class="fas fa-sign-out-alt me-2"></i><span class="nav-text">Logout</span>
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-grow-1 p-4">
            <div class="bg-white rounded shadow-sm p-4 mb-4">
                <h1 class="mb-0">Welcome Back, <?= htmlspecialchars($username) ?>!</h1>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card bg-pink text-black rounded shadow-sm">
                        <div class="card-body text-center">
                            <a href="album.php" class="text-decoration-none text-black">
                                <h3><i class="fas fa-folder me-2"></i>Albums</h3>
                                <p class="fs-4"><?= $albumCount ?></p>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-pink text-black rounded shadow-sm">
                        <div class="card-body text-center">
                            <a href="photo.php" class="text-decoration-none text-black">
                                <h3><i class="fas fa-image me-2"></i>Photos</h3>
                                <p class="fs-4"><?= $photoCount ?></p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
                <!-- My Photos Section -->
            <div class="card rounded shadow-sm p-4 mt-4">
                <h2 class="mb-4"><i class="fas fa-images me-2"></i>My Photos</h2>
                <?php if (!empty($userPhotos)): ?>
                    <div class="row">
                        <?php foreach ($userPhotos as $photo): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card">
                                    <!-- Clicking the image goes to the photo detail page -->
                                    <a href="photo.php?id=<?= htmlspecialchars($photo['id']) ?>">
                                        <img src="<?= htmlspecialchars($photo['file_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($photo['description']) ?>">
                                    </a>
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($photo['description']) ?></h5>
                                        <a href="photo.php?id=<?= htmlspecialchars($photo['id']) ?>" class="btn btn-primary">View</a>
                                        <!-- Delete Button using POST -->
                                        <form method="POST" action="dashboard.php" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this photo?');">
                                            <input type="hidden" name="delete_photo_id" value="<?= htmlspecialchars($photo['id']) ?>">
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>You have not uploaded any photos yet.</p>
                <?php endif; ?>
            </div>
            <!-- Recent Activity Section -->
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
        <!-- Footer -->
    <footer class="bg-light text-center py-3 mt-4">
        <div class="container">
            <p class="mb-0">&copy; <?= date("Y") ?> Photo Album. All rights reserved.</p>
        </div>
    </footer>
    <!-- Bootstrap 5 JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
        }
    </script>
</body>
</html>
