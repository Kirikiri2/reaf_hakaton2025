<?php
// Подключение к базе данных
$host = 'db';
$dbname = 'office_organizer';
$user = 'office_user';
$pass = 'office_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Получение пользователей
    $stmt = $pdo->query("SELECT id, login, password, full_name, role FROM users WHERE login IN ('admin', 'admin2')");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Пользователи:\n";
    foreach ($users as $user) {
        echo "ID: {$user['id']}\n";
        echo "Логин: {$user['login']}\n";
        echo "Пароль (хеш): {$user['password']}\n";
        echo "ФИО: {$user['full_name']}\n";
        echo "Роль: {$user['role']}\n";
        echo "-------------------\n";
    }
    
    // Проверка хеша пароля
    $password = 'password'; // Пароль для проверки
    $hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
    
    echo "\nПроверка хеша пароля:\n";
    echo "Пароль: $password\n";
    echo "Хеш: $hash\n";
    echo "Результат проверки: " . (password_verify($password, $hash) ? 'Верно' : 'Неверно') . "\n";
    
} catch (PDOException $e) {
    echo "Ошибка подключения к базе данных: " . $e->getMessage() . "\n";
}
?> 