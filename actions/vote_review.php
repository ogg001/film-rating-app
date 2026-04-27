<?php
require '../config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Musisz być zalogowany, aby głosować.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $review_id = (int)$_POST['review_id'];
    $vote = $_POST['vote'];

    if (!in_array($vote, ['like', 'dislike'])) {
        echo json_encode(['success' => false, 'error' => 'Nieprawidłowy typ głosu.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT vote FROM review_votes WHERE user_id = :user_id AND review_id = :review_id");
        $stmt->execute(['user_id' => $user_id, 'review_id' => $review_id]);
        $existingVote = $stmt->fetchColumn();

        $pdo->beginTransaction();

        if ($existingVote) {
            if ($existingVote === $vote) {
                $stmt = $pdo->prepare("DELETE FROM review_votes WHERE user_id = :user_id AND review_id = :review_id");
                $stmt->execute(['user_id' => $user_id, 'review_id' => $review_id]);

                $column = ($vote === 'like') ? 'likes' : 'dislikes';
                $stmt = $pdo->prepare("UPDATE reviews SET $column = $column - 1 WHERE id = :review_id");
                $stmt->execute(['review_id' => $review_id]);
            } else {
                $stmt = $pdo->prepare("UPDATE review_votes SET vote = :vote WHERE user_id = :user_id AND review_id = :review_id");
                $stmt->execute(['vote' => $vote, 'user_id' => $user_id, 'review_id' => $review_id]);

                $columnAdd = ($vote === 'like') ? 'likes' : 'dislikes';
                $columnRemove = ($vote === 'like') ? 'dislikes' : 'likes';

                $stmt = $pdo->prepare("UPDATE reviews SET $columnAdd = $columnAdd + 1, $columnRemove = $columnRemove - 1 WHERE id = :review_id");
                $stmt->execute(['review_id' => $review_id]);
            }
        } else {
            $stmt = $pdo->prepare("INSERT INTO review_votes (user_id, review_id, vote) VALUES (:user_id, :review_id, :vote)");
            $stmt->execute(['user_id' => $user_id, 'review_id' => $review_id, 'vote' => $vote]);

            $column = ($vote === 'like') ? 'likes' : 'dislikes';
            $stmt = $pdo->prepare("UPDATE reviews SET $column = $column + 1 WHERE id = :review_id");
            $stmt->execute(['review_id' => $review_id]);
        }

        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => 'Błąd podczas głosowania.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Nieprawidłowe żądanie.']);
}
