<?php
require dirname(__DIR__) . '/config.php';
header('Content-Type: application/json');
session_start();

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin' && isset($_POST['review_id'])) {
    $reviewId = (int)$_POST['review_id'];

    try {
        $stmt = $pdo->prepare("SELECT film_id FROM reviews WHERE id = :reviewId");
        $stmt->bindParam(':reviewId', $reviewId, PDO::PARAM_INT);
        $stmt->execute();
        $reviewRow = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmtDel = $pdo->prepare("DELETE FROM reviews WHERE id = :reviewId");
        $stmtDel->bindParam(':reviewId', $reviewId, PDO::PARAM_INT);
        $stmtDel->execute();

        if ($reviewRow) {
            $filmId = (int)$reviewRow['film_id'];
            $stmtAvg = $pdo->prepare("
                SELECT 
                    COALESCE(AVG(rating), 0) AS avg_rating,
                    COUNT(*) AS review_count
                FROM reviews
                WHERE film_id = :filmId
            ");
            $stmtAvg->bindParam(':filmId', $filmId, PDO::PARAM_INT);
            $stmtAvg->execute();
            $avgData = $stmtAvg->fetch(PDO::FETCH_ASSOC);

            $averageRating = 0.0;
            $reviewCount   = 0;
            if ($avgData) {
                $averageRating = (float)$avgData['avg_rating'];
                $reviewCount   = (int)$avgData['review_count'];
            }

            echo json_encode([
                'success'       => true,
                'averageRating' => number_format($averageRating, 1),
                'reviewCount'   => $reviewCount
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'averageRating' => '0.0',
                'reviewCount'   => 0
            ]);
        }
    } catch (Exception $e) {
        error_log("Błąd podczas usuwania recenzji: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Wystąpił problem z serwerem.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Nieprawidłowe żądanie lub brak uprawnień.']);
}