<?php
require '../config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $userId = (int)$_SESSION['user_id'];
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];

    if (empty($currentPassword) || empty($newPassword)) {
        echo json_encode(['success' => false, 'error' => 'Wszystkie pola są wymagane.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :user_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(['success' => false, 'error' => 'Użytkownik nie istnieje.']);
            exit;
        }

        if (!password_verify($currentPassword, $user['password'])) {
            echo json_encode(['success' => false, 'error' => 'Nieprawidłowe aktualne hasło.']);
            exit;
        }

        if (strlen($newPassword) < 8) {
            echo json_encode(['success' => false, 'error' => 'Nowe hasło musi mieć co najmniej 8 znaków.']);
            exit;
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $updateStmt = $pdo->prepare("UPDATE users SET password = :new_password WHERE id = :user_id");
        $updateStmt->bindParam(':new_password', $hashedPassword, PDO::PARAM_STR);
        $updateStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        if ($updateStmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Hasło zostało pomyślnie zmienione.']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Wystąpił błąd podczas zmiany hasła.']);
        }
    } catch (PDOException $e) {
        error_log("Błąd podczas zmiany hasła: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Wystąpił problem z serwerem.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Nieprawidłowe żądanie.']);
}
