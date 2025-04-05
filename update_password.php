<?php
// Подключение к базе данных
$host = 'db';
$dbname = 'office_organizer';
$user = 'office_user';
$pass = 'office_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Создание нового хеша пароля
    $password = 'password';
    $hash = password_hash($password, PASSWORD_BCRYPT);
    
    // Обновление пароля для admin
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE login = ?");
    $stmt->execute([$hash, 'admin']);
    
    echo "Пароль обновлен.\n";
    
    // Проверка обновленного пароля
    $stmt = $pdo->query("SELECT id, login, password FROM users WHERE login = 'admin'");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "\nПроверка:\n";
    echo "ID: {$user['id']}\n";
    echo "Логин: {$user['login']}\n";
    echo "Новый хеш пароля: {$user['password']}\n";
    echo "Проверка пароля: " . (password_verify($password, $user['password']) ? 'Верно' : 'Неверно') . "\n";
    
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
}
?> 