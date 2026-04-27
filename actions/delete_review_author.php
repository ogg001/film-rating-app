<?php
require dirname(__DIR__) . '/config.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && isset($_POST['review_id'])) {
    $user_id = $_SESSION['user_id'];
    $review_id = (int)$_POST['review_id'];

    try {
        $stmt = $pdo->prepare("SELECT user_id, film_id FROM reviews WHERE id = :review_id");
        $stmt->bindParam(':review_id', $review_id, PDO::PARAM_INT);
        $stmt->execute();
        $review = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($review && $review['user_id'] == $user_id) {
            $filmId = (int)$review['film_id'];

            $deleteStmt = $pdo->prepare("DELETE FROM reviews WHERE id = :review_id");
            $deleteStmt->bindParam(':review_id', $review_id, PDO::PARAM_INT);
            $deleteStmt->execute();

            $checkLogStmt = $pdo->prepare("
                SELECT id 
                FROM user_activity_log 
                WHERE user_id = :user_id 
                  AND film_id = :film_id 
                  AND action_type = 'delete_review'
            ");
            $checkLogStmt->execute([
                'user_id' => $user_id,
                'film_id' => $filmId
            ]);
            $existingLog = $checkLogStmt->fetch(PDO::FETCH_ASSOC);

            if ($existingLog) {
                $updateLogStmt = $pdo->prepare("
                    UPDATE user_activity_log 
                    SET created_at = NOW() 
                    WHERE id = :id
                ");
                $updateLogStmt->execute(['id' => $existingLog['id']]);
            } else {
                $logStmt = $pdo->prepare("
                    INSERT INTO user_activity_log (user_id, action_type, film_id, created_at) 
                    VALUES (:user_id, 'delete_review', :film_id, NOW())
                ");
                $logStmt->execute([
                    'user_id' => $user_id,
                    'film_id' => $filmId
                ]);
            }

            $stmtAvg = $pdo->prepare("
                SELECT 
                    COALESCE(AVG(rating), 0) AS avg_rating,
                    COUNT(*) AS review_count
                FROM reviews
                WHERE film_id = :film_id
            ");
            $stmtAvg->bindParam(':film_id', $filmId, PDO::PARAM_INT);
            $stmtAvg->execute();
            $avgData = $stmtAvg->fetch(PDO::FETCH_ASSOC);

            $averageRating = 0.0;
            $reviewCount   = 0;
            if ($avgData) {
                $averageRating = (float)$avgData['avg_rating'];
                $reviewCount   = (int)$avgData['review_count'];
            }

            echo json_encode([
                'success'        => true,
                'averageRating'  => number_format($averageRating, 1),
                'reviewCount'    => $reviewCount
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'error' => 'Nie masz uprawnień do usunięcia tej recenzji.'
            ]);
        }
    } catch (PDOException $e) {
        error_log("Błąd podczas usuwania recenzji: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Wystąpił problem z serwerem.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Nieprawidłowe żądanie.']);
}