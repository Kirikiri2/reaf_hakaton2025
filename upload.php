<?php
require_once 'config.php';

// Проверка авторизации
if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Проверка наличия файла
if (!isset($_FILES['file'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No file uploaded']);
    exit;
}

$file = $_FILES['file'];
$fileName = $file['name'];
$fileTmpName = $file['tmp_name'];
$fileError = $file['error'];
$fileSize = $file['size'];
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

// Проверка расширения файла
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
if (!in_array($fileExt, $allowedExtensions)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid file type']);
    exit;
}

// Проверка размера файла (максимум 5MB)
if ($fileSize > 5 * 1024 * 1024) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'File too large']);
    exit;
}

// Проверка ошибок загрузки
if ($fileError !== UPLOAD_ERR_OK) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Upload failed']);
    exit;
}

// Генерация уникального имени файла
$newFileName = uniqid('img_', true) . '.' . $fileExt;
$uploadPath = UPLOAD_PATH . '/images/' . $newFileName;

// Создание директории, если она не существует
if (!file_exists(UPLOAD_PATH . '/images')) {
    mkdir(UPLOAD_PATH . '/images', 0777, true);
}

// Перемещение файла
if (move_uploaded_file($fileTmpName, $uploadPath)) {
    header('Content-Type: application/json');
    echo json_encode([
        'location' => '/uploads/images/' . $newFileName
    ]);
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Failed to save file']);
} 