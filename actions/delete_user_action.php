<?php
header('Content-Type: application/json');
require '../config.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Brak uprawnień!']);
    exit;
}

if (!isset($_POST['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Nieprawidłowe żądanie.']);
    exit;
}

$userId = $_POST['user_id'];

try {
    $pdo->beginTransaction();

    $selectVotesStmt = $pdo->prepare("SELECT review_id, vote FROM review_votes WHERE user_id = ?");
    $selectVotesStmt->execute([$userId]);
    $userVotes = $selectVotesStmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($userVotes as $voteRow) {
        $reviewId = $voteRow['review_id'];
        $voteType = $voteRow['vote'];

        if ($voteType === 'like') {
            $updateLikesStmt = $pdo->prepare("
                UPDATE reviews
                SET likes = GREATEST(likes - 1, 0)
                WHERE id = ?
            ");
            $updateLikesStmt->execute([$reviewId]);
        } elseif ($voteType === 'dislike') {
            $updateDislikesStmt = $pdo->prepare("
                UPDATE reviews
                SET dislikes = GREATEST(dislikes - 1, 0)
                WHERE id = ?
            ");
            $updateDislikesStmt->execute([$reviewId]);
        }
    }

    $deleteVotesStmt = $pdo->prepare("DELETE FROM review_votes WHERE user_id = ?");
    $deleteVotesStmt->execute([$userId]);

    $deleteUserStmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $deleteUserStmt->execute([$userId]);

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Użytkownik został pomyślnie usunięty wraz z jego głosami!']);
    exit;

} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Wystąpił błąd podczas usuwania użytkownika: ' . $e->getMessage()]);
    exit;
}
?>