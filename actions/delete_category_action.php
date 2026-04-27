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

    try {
        $pdo->beginTransaction();

        $getCategoryIdStmt = $pdo->prepare('SELECT id FROM categories WHERE name = :name');
        $getCategoryIdStmt->execute([':name' => $name]);
        $categoryId = $getCategoryIdStmt->fetchColumn();

        if (!$categoryId) {
            echo json_encode(['success' => false, 'error' => 'Kategoria nie istnieje.']);
            $pdo->rollBack();
            exit;
        }

        $defaultCategory = 'Nieokreślona';
        $getDefaultCategoryStmt = $pdo->prepare('SELECT id FROM categories WHERE name = :defaultName');
        $getDefaultCategoryStmt->execute([':defaultName' => $defaultCategory]);
        $defaultCategoryId = $getDefaultCategoryStmt->fetchColumn();

        if (!$defaultCategoryId) {
            $insertDefaultStmt = $pdo->prepare('INSERT INTO categories (name) VALUES (:defaultName)');
            $insertDefaultStmt->execute([':defaultName' => $defaultCategory]);
            $defaultCategoryId = $pdo->lastInsertId();
        }

        $updateFilmsStmt = $pdo->prepare('UPDATE films SET category = :defaultCategoryId WHERE category = :categoryId');
        $updateFilmsStmt->execute([
            ':defaultCategoryId' => $defaultCategoryId,
            ':categoryId' => $categoryId
        ]);

        $deleteCategoryStmt = $pdo->prepare('DELETE FROM categories WHERE id = :categoryId');
        $deleteCategoryStmt->execute([':categoryId' => $categoryId]);

        $pdo->commit();

        echo json_encode(['success' => true, 'name' => $name, 'message' => 'Kategoria została usunięta, a filmy przypisano do "Nieokreślona".']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Błąd usuwania kategorii: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Błąd serwera.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Nieprawidłowe żądanie.']);
}
