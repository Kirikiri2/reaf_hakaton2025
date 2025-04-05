<?php
session_start();

// Временное решение - установка тестового пользователя
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // ID администратора из таблицы users
}

// Отладочная информация
error_log("Session data: " . print_r($_SESSION, true));
error_log("POST data: " . print_r($_POST, true));

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    error_log("User not authorized");
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Подключение к базе данных
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

// Обработка запросов
$action = $_POST['action'] ?? '';
$response = ['success' => false];

switch ($action) {
    case 'get_articles':
        $articleId = $_POST['article_id'] ?? null;
        
        if ($articleId) {
            $stmt = $pdo->prepare('SELECT * FROM articles WHERE id = ? AND is_deleted = 0');
            $stmt->execute([$articleId]);
            $article = $stmt->fetch();
            
            if ($article) {
                $response = ['success' => true, 'article' => $article];
            } else {
                $response['error'] = 'Article not found';
            }
        } else {
            $stmt = $pdo->prepare('SELECT * FROM articles WHERE is_deleted = 0 ORDER BY created_at DESC');
            $stmt->execute();
            $articles = $stmt->fetchAll();
            
            $response = ['success' => true, 'articles' => $articles];
        }
        break;
        
    case 'save_article':
        $articleId = $_POST['article_id'] ?? null;
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $image = $_FILES['image'] ?? null;
        
        if (empty($title) || empty($content)) {
            $response['error'] = 'Title and content are required';
            break;
        }
        
        try {
            $pdo->beginTransaction();
            
            $imagePath = null;
            if ($image && $image['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/uploads/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileName = uniqid() . '_' . basename($image['name']);
                $imagePath = 'uploads/' . $fileName;
                
                if (!move_uploaded_file($image['tmp_name'], $uploadDir . $fileName)) {
                    throw new Exception('Failed to upload image');
                }
            }
            
            if ($articleId) {
                $stmt = $pdo->prepare('
                    UPDATE articles 
                    SET title = ?, content = ?, updated_at = NOW()
                    ' . ($imagePath ? ', image = ?' : '') . '
                    WHERE id = ? AND is_deleted = 0
                ');
                
                $params = [$title, $content];
                if ($imagePath) {
                    $params[] = $imagePath;
                }
                $params[] = $articleId;
                
                $stmt->execute($params);
            } else {
                $stmt = $pdo->prepare('
                    INSERT INTO articles (title, content, image, created_at, updated_at)
                    VALUES (?, ?, ?, NOW(), NOW())
                ');
                $stmt->execute([$title, $content, $imagePath]);
            }
            
            $pdo->commit();
            $response = ['success' => true];
        } catch (Exception $e) {
            $pdo->rollBack();
            $response['error'] = 'Failed to save article: ' . $e->getMessage();
        }
        break;
        
    case 'delete_article':
        $articleId = $_POST['article_id'] ?? null;
        
        if (!$articleId) {
            $response['error'] = 'Article ID is required';
            break;
        }
        
        try {
            $stmt = $pdo->prepare('UPDATE articles SET is_deleted = 1, deleted_at = NOW() WHERE id = ?');
            $stmt->execute([$articleId]);
            
            $response = ['success' => true];
        } catch (Exception $e) {
            $response['error'] = 'Failed to delete article: ' . $e->getMessage();
        }
        break;
        
    case 'restore_article':
        $articleId = $_POST['article_id'] ?? null;
        
        if (!$articleId) {
            $response['error'] = 'Article ID is required';
            break;
        }
        
        try {
            $stmt = $pdo->prepare('
                UPDATE articles 
                SET is_deleted = 0, deleted_at = NULL 
                WHERE id = ? AND deleted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ');
            $stmt->execute([$articleId]);
            
            if ($stmt->rowCount() > 0) {
                $response = ['success' => true];
            } else {
                $response['error'] = 'Article cannot be restored or not found';
            }
        } catch (Exception $e) {
            $response['error'] = 'Failed to restore article: ' . $e->getMessage();
        }
        break;
        
    case 'get_history':
        $article_id = $_POST['article_id'] ?? null;
        if (!$article_id) {
            $response['error'] = 'Article ID is required';
            break;
        }
        
        $stmt = $pdo->prepare('
            SELECT h.*, u.full_name as user_name
            FROM article_history h
            JOIN users u ON h.editor_id = u.id
            WHERE h.article_id = ?
            ORDER BY h.action_date DESC
        ');
        $stmt->execute([$article_id]);
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $response = ['success' => true, 'history' => $history];
        break;
        
    default:
        $response['error'] = 'Invalid action';
}

header('Content-Type: application/json');
echo json_encode($response); 