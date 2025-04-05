<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;port=3306;dbname=office_organizer;charset=utf8mb4',
        'root',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    echo "Подключение успешно!\n";
    
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Существующие таблицы:\n";
    print_r($tables);
    
} catch (PDOException $e) {
    echo "Ошибка подключения: " . $e->getMessage() . "\n";
} 