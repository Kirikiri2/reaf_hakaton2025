<?php
require_once __DIR__ . '/../../config.php';

// Проверяем авторизацию и права администратора
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
    exit;
}

// Проверяем метод запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Метод не поддерживается']);
    exit;
}

// Получаем ID статьи
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Неверный ID статьи']);
    exit;
}

try {
    // Помечаем статью как удаленную
    $stmt = $pdo->prepare("
        UPDATE articles 
        SET deleted_at = CURRENT_TIMESTAMP 
        WHERE id = :id
    ");
    
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Статья не найдена']);
    }
} catch (PDOException $e) {
    error_log("Ошибка при удалении статьи: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка при удалении статьи']);
} 