<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

error_log("Session data: " . print_r($_SESSION, true));
error_log("POST data: " . print_r($_POST, true));

try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;port=3306;dbname=office_organizer;charset=utf8mb4',
        'root',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
} catch (PDOException $e) {
    error_log("Database connection error: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Database connection error: ' . $e->getMessage()]);
    exit;
}

$action = $_POST['action'] ?? '';
$response = ['success' => false];

switch ($action) {
    case 'login':
        $login = $_POST['login'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($login) || empty($password)) {
            $response['error'] = 'Login and password are required';
            break;
        }
        
        $stmt = $pdo->prepare('SELECT * FROM users WHERE login = ? AND is_deleted = 0');
        $stmt->execute([$login]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $response = ['success' => true];
        } else {
            $response['error'] = 'Invalid login or password';
        }
        break;
        
    case 'register':
        $login = $_POST['login'] ?? '';
        $fullName = $_POST['full_name'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($login) || empty($fullName) || empty($password) || empty($confirmPassword)) {
            $response['error'] = 'All fields are required';
            break;
        }
        
        if (!preg_match('/^[a-zA-Z]+$/', $login)) {
            $response['error'] = 'Login must contain only Latin letters';
            break;
        }
        
        if (!preg_match('/^[а-яА-ЯёЁ\s]+$/', $fullName)) {
            $response['error'] = 'Full name must contain only Russian letters';
            break;
        }
        
        if (!preg_match('/^[a-zA-Z0-9!@#$%^&*()_+\-=\[\]{};:"\\|,.<>\/?]+$/', $password)) {
            $response['error'] = 'Password must contain only Latin letters, numbers and special characters';
            break;
        }
        
        if ($password !== $confirmPassword) {
            $response['error'] = 'Passwords do not match';
            break;
        }
        
        $stmt = $pdo->prepare('SELECT id FROM users WHERE login = ?');
        $stmt->execute([$login]);
        if ($stmt->fetch()) {
            $response['error'] = 'Login already exists';
            break;
        }
        
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare('
                INSERT INTO users (login, full_name, password, role, created_at)
                VALUES (?, ?, ?, "user", NOW())
            ');
            $stmt->execute([$login, $fullName, password_hash($password, PASSWORD_DEFAULT)]);
            
            $pdo->commit();
            $response = ['success' => true];
        } catch (Exception $e) {
            $pdo->rollBack();
            $response['error'] = 'Failed to register user';
        }
        break;
        
    case 'logout':
        session_destroy();
        $response = ['success' => true];
        break;
        
    default:
        $response['error'] = 'Invalid action';
}

header('Content-Type: application/json');
echo json_encode($response); 