<?php
require 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($user['is_blocked']) {
            $_SESSION['error_message'] = "Twoje konto zostało zablokowane przez administratora.";
            header("Location: /film-rating-app/login");
            exit;
        }

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: /film-rating-app/");
            exit;
        }
    }

    $_SESSION['error_message'] = "Nieprawidłowa nazwa użytkownika lub hasło.";
    header("Location: /film-rating-app/login");
    exit;
}
?>