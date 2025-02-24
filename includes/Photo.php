<?php
// includes/Photo.php

class Photo {
    private $pdo;

    // Constructor to inject the PDO object
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Upload a photo
    public function upload($userId, $albumId, $description, $tags, $filePath) {
        $stmt = $this->pdo->prepare('INSERT INTO photos (user_id, album_id, description, tags, file_path) VALUES (?, ?, ?, ?, ?)');
        return $stmt->execute([$userId, $albumId, $description, $tags, $filePath]);
    }

    // Get photo details by ID
    public function getPhotoDetails($photoId) {
        $stmt = $this->pdo->prepare('SELECT p.*, u.username FROM photos p JOIN users u ON p.user_id = u.id WHERE p.id = ?');
        $stmt->execute([$photoId]);
        return $stmt->fetch();
    }

    // Get all photos uploaded by a user
    public function getUserPhotos($userId) {
        $stmt = $this->pdo->prepare('SELECT * FROM photos WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    // Get photos for an album
    public function getPhotos($albumId) {
        $stmt = $this->pdo->prepare('SELECT * FROM photos WHERE album_id = ?');
        $stmt->execute([$albumId]);
        return $stmt->fetchAll();
    }

    // Delete a photo (only if the user owns it)
    public function deletePhoto($photoId, $userId) {
        $stmt = $this->pdo->prepare('DELETE FROM photos WHERE id = ? AND user_id = ?');
        return $stmt->execute([$photoId, $userId]);
    }

    // Get comments for a photo
    public function getComments($photoId) {
        $stmt = $this->pdo->prepare('SELECT c.id, c.comment, u.username, c.created_at FROM comments c JOIN users u ON c.user_id = u.id WHERE c.photo_id = ? ORDER BY c.created_at DESC');
        $stmt->execute([$photoId]);
        return $stmt->fetchAll();
    }

    // Add a comment to a photo
    public function addComment($photoId, $userId, $comment) {
        $stmt = $this->pdo->prepare('INSERT INTO comments (photo_id, user_id, comment) VALUES (?, ?, ?)');
        return $stmt->execute([$photoId, $userId, $comment]);
    }

    // Like a photo
    public function likePhoto($photoId, $userId) {
        $stmt = $this->pdo->prepare('INSERT IGNORE INTO likes (photo_id, user_id) VALUES (?, ?)');
        return $stmt->execute([$photoId, $userId]);
    }

    // Unlike a photo
    public function unlikePhoto($photoId, $userId) {
        $stmt = $this->pdo->prepare('DELETE FROM likes WHERE photo_id = ? AND user_id = ?');
        return $stmt->execute([$photoId, $userId]);
    }

    // Check if a user has liked a photo
    public function hasLiked($photoId, $userId) {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) AS count FROM likes WHERE photo_id = ? AND user_id = ?');
        $stmt->execute([$photoId, $userId]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    // Get total likes for a photo
    public function getTotalLikes($photoId) {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) AS count FROM likes WHERE photo_id = ?');
        $stmt->execute([$photoId]);
        $result = $stmt->fetch();
        return $result['count'];
    }

    // Update photo privacy
    public function setPrivacy($photoId, $isPrivate) {
        $stmt = $this->pdo->prepare('UPDATE photos SET is_private = ? WHERE id = ?');
        return $stmt->execute([$isPrivate, $photoId]);
    }

    // Check if a photo is private
    public function isPrivate($photoId) {
        $stmt = $this->pdo->prepare('SELECT is_private FROM photos WHERE id = ?');
        $stmt->execute([$photoId]);
        $result = $stmt->fetch();
        return $result['is_private'];
    }
}
?>