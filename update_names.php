<?php
// Подключение к базе данных
$host = 'db';
$dbname = 'office_organizer';
$user = 'office_user';
$pass = 'office_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Обновление значений полей full_name
    $stmt = $pdo->prepare("UPDATE users SET full_name = ? WHERE login = ?");
    
    // Для admin
    $adminName = 'Администратор';
    $stmt->execute([$adminName, 'admin']);
    
    // Для admin2
    $admin2Name = 'Администратор 2';
    $stmt->execute([$admin2Name, 'admin2']);
    
    echo "Значения полей full_name обновлены.\n";
    
    // Проверка обновленных значений
    $stmt = $pdo->query("SELECT id, login, full_name FROM users WHERE login IN ('admin', 'admin2')");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nОбновленные значения:\n";
    foreach ($users as $user) {
        echo "ID: {$user['id']}\n";
        echo "Логин: {$user['login']}\n";
        echo "ФИО: {$user['full_name']}\n";
        echo "-------------------\n";
    }
    
} catch (PDOException $e) {
    echo "Ошибка подключения к базе данных: " . $e->getMessage() . "\n";
}
?> 