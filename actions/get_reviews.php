<?php
require __DIR__ . '/../config.php';
header('Content-Type: application/json');

if (isset($_GET['film_id'])) {
    $filmId = (int)$_GET['film_id'];

    $stmt = $pdo->prepare("
        SELECT reviews.id, reviews.review_text, reviews.rating, users.username 
        FROM reviews
        JOIN users ON reviews.user_id = users.id
        WHERE reviews.film_id = :film_id
    ");
    
    $stmt->execute([':film_id' => $filmId]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($reviews);
}
?>