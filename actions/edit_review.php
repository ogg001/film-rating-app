<?php
require '../config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' 
    && isset($_SESSION['user_id'], $_POST['id'], $_POST['review_text'], $_POST['rating'])) {
    
    $reviewId   = (int)$_POST['id'];
    $reviewText = trim($_POST['review_text']);
    $rating     = (int)$_POST['rating'];
    $userId     = (int)$_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("
            SELECT user_id, film_id
            FROM reviews
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $reviewId, PDO::PARAM_INT);
        $stmt->execute();
        $review = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($review && $review['user_id'] == $userId) {
            $updateStmt = $pdo->prepare("
                UPDATE reviews 
                SET review_text = :review_text,
                    rating = :rating,
                    last_edited_at = NOW() 
                WHERE id = :id
            ");
            $updateStmt->execute([
                ':review_text' => $reviewText,
                ':rating'      => $rating,
                ':id'          => $reviewId
            ]);

            $filmId = (int)$review['film_id'];

            $avgStmt = $pdo->prepare("
                SELECT 
                    COALESCE(AVG(rating), 0) AS avg_rating,
                    COUNT(*) AS review_count
                FROM reviews
                WHERE film_id = :filmId
            ");
            $avgStmt->execute([':filmId' => $filmId]);
            $avgData = $avgStmt->fetch(PDO::FETCH_ASSOC);

            $averageRating = 0.0;
            $reviewCount   = 0;
            if ($avgData) {
                $averageRating = (float)$avgData['avg_rating'];
                $reviewCount   = (int)$avgData['review_count'];
            }

            echo json_encode([
                'success'         => true,
                'updatedUsername' => $_SESSION['username'],
                'lastEditedAt'    => date('Y-m-d H:i:s'),
                'averageRating'   => number_format($averageRating, 1),
                'reviewCount'     => $reviewCount
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error'   => 'Nie masz uprawnień do edycji tej recenzji.'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Wystąpił błąd serwera.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Nieprawidłowe dane wejściowe.']);
}
?>
