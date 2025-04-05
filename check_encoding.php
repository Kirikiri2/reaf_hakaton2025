<?php
// Подключение к базе данных
$host = 'db';
$dbname = 'office_organizer';
$user = 'office_user';
$pass = 'office_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Получение информации о кодировке базы данных
    $stmt = $pdo->query("SELECT @@character_set_database, @@collation_database");
    $charsetInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Кодировка базы данных: {$charsetInfo['@@character_set_database']}\n";
    echo "Коллация базы данных: {$charsetInfo['@@collation_database']}\n\n";
    
    // Получение информации о кодировке таблицы users
    $stmt = $pdo->query("SHOW TABLE STATUS WHERE Name = 'users'");
    $tableInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Кодировка таблицы users: {$tableInfo['Collation']}\n\n";
    
    // Получение пользователей
    $stmt = $pdo->query("SELECT id, login, password, full_name, role FROM users WHERE login IN ('admin', 'admin2')");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Пользователи:\n";
    foreach ($users as $user) {
        echo "ID: {$user['id']}\n";
        echo "Логин: {$user['login']}\n";
        echo "Пароль (хеш): {$user['password']}\n";
        echo "ФИО (hex): " . bin2hex($user['full_name']) . "\n";
        echo "ФИО (utf8): " . mb_convert_encoding($user['full_name'], 'UTF-8', 'auto') . "\n";
        echo "Роль: {$user['role']}\n";
        echo "-------------------\n";
    }
    
} catch (PDOException $e) {
    echo "Ошибка подключения к базе данных: " . $e->getMessage() . "\n";
}
?> 