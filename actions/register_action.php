<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password_repeat = $_POST['password_repeat'];

    if ($password !== $password_repeat) {
        header("Location: /film-rating-app/register/error/password_mismatch");
        exit;
    }

    if (empty($username) || empty($password)) {
        header("Location: /film-rating-app/register/error/empty_fields");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $userExists = $stmt->fetchColumn();

        if ($userExists > 0) {
            header("Location: /film-rating-app/register/error/user_exists");
            exit;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, 'user')");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);

        if ($stmt->execute()) {
            header("Location: /film-rating-app/register/success");
            exit;
        } else {
            header("Location: /film-rating-app/register/error/server_error");
            exit;
        }
    } catch (PDOException $e) {
        error_log("Błąd podczas rejestracji: " . $e->getMessage());
        header("Location: /film-rating-app/register/error/server_error");
        exit;
    }
}
