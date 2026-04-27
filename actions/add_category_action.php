<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Brak dostępu']);
    exit;
}

require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name'])) {
    $name = trim($_POST['name']);

    
    if (strlen($name) > 15) {
        echo json_encode(['success' => false, 'error' => 'Nazwa kategorii nie może być dłuższa niż 15 znaków.']);
        exit;
    }

    if (!preg_match('/^[A-Za-zĄąĆćĘęŁłŃńÓóŚśŹźŻż\s]+$/', $name)) {
        echo json_encode(['success' => false, 'error' => 'Nazwa kategorii może zawierać tylko litery.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM categories WHERE name = :name');
        $stmt->bindParam(':name', $name);
        $stmt->execute();

        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'error' => 'Ta kategoria już istnieje!']);
            exit;
        }

        $stmt = $pdo->prepare('INSERT INTO categories (name) VALUES (:name)');
        $stmt->bindParam(':name', $name);

			if ($stmt->execute()) {
				$lastInsertId = $pdo->lastInsertId();
				echo json_encode(['success' => true, 'id' => $lastInsertId, 'name' => $name]);
				exit;
			} else {
				echo json_encode(['success' => false, 'error' => 'Nie udało się dodać kategorii.']);
				exit;
			}
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Błąd serwera. Spróbuj ponownie później.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Nieprawidłowe żądanie.']);
}
