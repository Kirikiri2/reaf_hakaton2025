<?php
// Настройки базы данных
define('DB_HOST', 'localhost'); // Хост базы данных
define('DB_NAME', 'office_organizer'); // Имя базы данных
define('DB_USER', 'root'); // Пользователь базы данных
define('DB_PASS', ''); // Пароль базы данных

// Пути
define('BASE_PATH', __DIR__);
define('UPLOAD_PATH', BASE_PATH . '/uploads');

// Настройки сессии
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);
session_start();

// Подключение к базе данных
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $db = new PDO($dsn, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    error_log("Ошибка подключения к БД: " . $e->getMessage());
    die("Ошибка подключения к базе данных");
}

// Функции безопасности
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Функции авторизации
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /auth/login');
        exit();
    }
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Функции валидации
function validateLogin($login) {
    return preg_match('/^[a-zA-Z]+$/', $login);
}

function validateName($name) {
    return preg_match('/^[а-яА-ЯёЁ\s]+$/u', $name);
}

function validatePassword($password) {
    return preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&.])[A-Za-z\d@$!%*#?&.]{8,}$/', $password);
}

// Создание директории для загрузок, если её нет
if (!file_exists(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0777, true);
}

// Настройки безопасности
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Настройки отображения ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);
?> 