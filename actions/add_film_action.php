<?php
require __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$pdo) {
        header("Location: /film-rating-app/admin_panel?error=server_error");
        exit;
    }

    $title        = trim($_POST['title']);
    $description  = trim($_POST['description']);
    $release_year = (int)$_POST['release_year'];
    $category     = trim($_POST['category']);
    $duration     = (int)$_POST['duration'];
    $posterPath   = null;

    $allowedExtensions = ['jpg', 'jpeg', 'png'];

    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath     = $_FILES['poster']['tmp_name'];
        $originalFileName = basename($_FILES['poster']['name']);
        $fileExtension   = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            header("Location: /film-rating-app/admin_panel?error=invalid_poster");
            exit;
        }

        $uniqueFileName = uniqid('film_', true) . '_' . $originalFileName;

        $uploadDir = __DIR__ . '/../uploads/';
        $destPath  = $uploadDir . $uniqueFileName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (!move_uploaded_file($fileTmpPath, $destPath)) {
            header("Location: /film-rating-app/admin_panel?error=upload_failed");
            exit;
        }

        $posterPath = 'uploads/' . $uniqueFileName;

    } elseif (isset($_FILES['poster']) && $_FILES['poster']['error'] !== UPLOAD_ERR_NO_FILE) {
        header("Location: /film-rating-app/admin_panel?error=upload_failed");
        exit;
    }

    if (!is_numeric($category)) {
        header("Location: /film-rating-app/admin_panel?error=invalid_category");
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM films 
        WHERE title = :title 
          AND release_year = :release_year
    ");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':release_year', $release_year);
    $stmt->execute();
    $filmExists = $stmt->fetchColumn();

    if ($filmExists > 0) {
        header("Location: /film-rating-app/admin_panel?error=film_exists");
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO films (title, description, release_year, category, duration, poster)
            VALUES (:title, :description, :release_year, :category, :duration, :poster)
        ");

        $stmt->bindParam(':title',        $title);
        $stmt->bindParam(':description',  $description);
        $stmt->bindParam(':release_year', $release_year);
        $stmt->bindParam(':category',     $category);
        $stmt->bindParam(':duration',     $duration);

        if ($posterPath === null) {
            $stmt->bindValue(':poster', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':poster', $posterPath);
        }

        $stmt->execute();

        header("Location: /film-rating-app/admin_panel?success=1");
        exit;

    } catch (PDOException $e) {
        header("Location: /film-rating-app/admin_panel?error=server_error");
        exit;
    }

} else {
    header("Location: /film-rating-app/admin_panel?error=invalid_request");
    exit;
}
