<?php
require dirname(__DIR__) . '/config.php';
session_start();

if (isset($_SESSION['user_id']) && isset($_POST['review_text']) && isset($_POST['rating']) && isset($_POST['film_id'])) {
    $user_id = $_SESSION['user_id'];
    $review_text = trim($_POST['review_text']);
    $rating = (int)$_POST['rating'];
    $film_id = (int)$_POST['film_id'];

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reviews WHERE user_id = :user_id AND film_id = :film_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':film_id', $film_id, PDO::PARAM_INT);
    $stmt->execute();
    $reviewExists = $stmt->fetchColumn();

    if ($reviewExists > 0) {
        header("Location: /film-rating-app/film/$film_id?error=already_reviewed");
        exit;
    }

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO reviews (user_id, film_id, review_text, rating, created_at) 
            VALUES (:user_id, :film_id, :review_text, :rating, NOW())"
        );
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':film_id', $film_id, PDO::PARAM_INT);
        $stmt->bindParam(':review_text', $review_text, PDO::PARAM_STR);
        $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: /film-rating-app/film/$film_id?success_review=1");
        exit;
    } catch (PDOException $e) {
        error_log("Błąd podczas dodawania recenzji: " . $e->getMessage());
        header("Location: /film-rating-app/film/$film_id?error=server_error");
        exit;
    }
} else {
    header("Location: /film-rating-app/film/$film_id?error=invalid_request");
    exit;
}