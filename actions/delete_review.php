<?php
require '../config.php';

if (isset($_GET['review_id'])) {
    $reviewId = (int)$_GET['review_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = :review_id");
        $stmt->execute([':review_id' => $reviewId]);

        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>