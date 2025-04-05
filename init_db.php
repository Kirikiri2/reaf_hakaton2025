<?php
// Параметры подключения к базе данных
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Подключение к MySQL
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to MySQL successfully\n";
    
    // Читаем SQL-файл
    $sql = file_get_contents('database.sql');
    
    // Выполняем SQL-запросы
    $pdo->exec($sql);
    
    echo "Database initialized successfully\n";
    echo "Default admin credentials:\n";
    echo "Login: admin\n";
    echo "Password: admin123\n";
    
} catch (PDOException $e) {
    die("Database initialization failed: " . $e->getMessage() . "\n");
} 