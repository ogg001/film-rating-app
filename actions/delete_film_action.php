<?php
require __DIR__ . '/../config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['film_id'])) {
    $filmId = (int)$_POST['film_id'];

    $stmtPoster = $pdo->prepare("
        SELECT poster 
        FROM films
        WHERE id = :film_id
        LIMIT 1
    ");
    $stmtPoster->execute([':film_id' => $filmId]);
    $filmRow = $stmtPoster->fetch(PDO::FETCH_ASSOC);

    if ($filmRow && $filmRow['poster']) {
        $posterFile = __DIR__ . '/../' . $filmRow['poster'];
        if (file_exists($posterFile) && is_file($posterFile)) {
            unlink($posterFile);
        }
    }

    $stmtDelete = $pdo->prepare("
        DELETE FROM films 
        WHERE id = :film_id
    ");
    $stmtDelete->execute([':film_id' => $filmId]);

    header("Location: /film-rating-app/admin_panel?success_delete=1");
    exit;

} else {
    echo "Błąd: nieprawidłowe żądanie.";
}
?>