<?php
header('Content-Type: application/json');
require '../config.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Brak uprawnień!']);
    exit;
}

if (isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    $isBlocked = $_POST['is_blocked'] ?? 0;

    $stmt = $pdo->prepare("UPDATE users SET is_blocked = ? WHERE id = ?");
    $stmt->execute([$isBlocked, $userId]);

    $message = $isBlocked ? 'Użytkownik został zablokowany!' : 'Użytkownik został odblokowany!';
    echo json_encode(['success' => true, 'message' => $message, 'is_blocked' => $isBlocked]);
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Nieprawidłowe żądanie.']);
    exit;
}
?>