<?php
require __DIR__ . '/../config.php'; 
session_start(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['film_id'])) {


    $filmId      = $_POST['film_id'];
    $title       = trim($_POST['title']);
    $description = trim($_POST['description']);
    $releaseYear = (int)$_POST['release_year'];
    $category    = trim($_POST['category']);
    $duration    = (int)$_POST['duration'];


    $stmt = $pdo->prepare("
        SELECT poster 
        FROM films 
        WHERE id = :film_id
    ");
    $stmt->execute([':film_id' => $filmId]);
    $currentFilm = $stmt->fetch(PDO::FETCH_ASSOC);

    $existingPosterPath = $currentFilm['poster'] ?? null;
    $posterPath         = $existingPosterPath; 
    $allowedExtensions  = ['jpg', 'jpeg', 'png'];


    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath     = $_FILES['poster']['tmp_name'];
        $originalName    = basename($_FILES['poster']['name']);
        $fileExtension   = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            header("Location: /film-rating-app/admin_panel?error=invalid_poster");
            exit;
        }

        $uniqueFileName = uniqid('film_', true) . '_' . $originalName;

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

        if ($existingPosterPath && file_exists(__DIR__ . '/../' . $existingPosterPath)) {
            unlink(__DIR__ . '/../' . $existingPosterPath);
        }
    }
	
    $stmtCheck = $pdo->prepare("
        SELECT COUNT(*) 
        FROM films
        WHERE title = :title
          AND release_year = :release_year
          AND id != :film_id
    ");
    $stmtCheck->execute([
        ':title'       => $title,
        ':release_year'=> $releaseYear,
        ':film_id'     => $filmId
    ]);
    $filmExists = $stmtCheck->fetchColumn();

    if ($filmExists > 0) {
        header("Location: /film-rating-app/admin_panel?error=film_exists");
        exit;
    }

    $sql = "
        UPDATE films
        SET title        = :title,
            description  = :description,
            release_year = :release_year,
            category     = :category,
            duration     = :duration,
            poster       = :poster
        WHERE id = :id
    ";
    $stmtUpdate = $pdo->prepare($sql);
    $stmtUpdate->execute([
        ':title'       => $title,
        ':description' => $description,
        ':release_year'=> $releaseYear,
        ':category'    => $category,
        ':duration'    => $duration,
        ':poster'      => $posterPath,
        ':id'          => $filmId
    ]);

    header("Location: /film-rating-app/admin_panel?success_edit=1");
    exit;
} else {
    header("Location: /film-rating-app/admin_panel?error=invalid_request");
    exit;
}