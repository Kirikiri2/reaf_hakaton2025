<?php
// Проверка авторизации
if (!isLoggedIn()) {
    header('Location: /auth/login');
    exit;
}

// Обработка действий
switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = sanitize($_POST['title']);
            $content = $_POST['content']; // Контент от TinyMCE не нужно санитизировать
            $image = null;
            
            // Загрузка изображения
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = uploadImage($_FILES['image'], 'articles');
                if ($uploadResult['success']) {
                    $image = $uploadResult['path'];
                } else {
                    $error = $uploadResult['error'];
                    require 'modules/articles/create.php';
                    exit;
                }
            }
            
            try {
                $stmt = $db->prepare("
                    INSERT INTO articles (title, content, image_path, created_at, updated_at, created_by, last_edited_by) 
                    VALUES (?, ?, ?, NOW(), NOW(), ?, ?)
                ");
                
                $userId = $_SESSION['user_id'];
                $stmt->execute([$title, $content, $image, $userId, $userId]);
                $articleId = $db->lastInsertId();
                
                // Запись в историю
                $stmt = $db->prepare("
                    INSERT INTO article_history (article_id, user_id, action_type, action_date, new_data) 
                    VALUES (?, ?, 'create', NOW(), ?)
                ");
                
                $newData = json_encode([
                    'title' => $title,
                    'content' => $content,
                    'image_path' => $image
                ]);
                
                $stmt->execute([$articleId, $userId, $newData]);
                
                header('Location: /articles?success=created');
                exit;
            } catch (PDOException $e) {
                $error = 'Ошибка при создании статьи';
                require 'modules/articles/create.php';
                exit;
            }
        } else {
            require 'modules/articles/create.php';
        }
        break;
        
    case 'edit':
        $id = (int)($_GET['id'] ?? 0);
        
        if (!$id) {
            header('Location: /articles');
            exit;
        }
        
        // Получение статьи
        $stmt = $db->prepare("
            SELECT a.*, u.full_name as author_name, e.full_name as editor_name 
            FROM articles a 
            LEFT JOIN users u ON a.created_by = u.id 
            LEFT JOIN users e ON a.last_edited_by = e.id 
            WHERE a.id = ? AND a.is_deleted = 0
        ");
        $stmt->execute([$id]);
        $article = $stmt->fetch();
        
        if (!$article) {
            header('Location: /articles');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = sanitize($_POST['title']);
            $content = $_POST['content'];
            $oldData = json_encode([
                'title' => $article['title'],
                'content' => $article['content'],
                'image_path' => $article['image_path']
            ]);
            
            // Загрузка нового изображения
            $image = $article['image_path'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = uploadImage($_FILES['image'], 'articles');
                if ($uploadResult['success']) {
                    $image = $uploadResult['path'];
                } else {
                    $error = $uploadResult['error'];
                    require 'modules/articles/edit.php';
                    exit;
                }
            }
            
            try {
                $stmt = $db->prepare("
                    UPDATE articles 
                    SET title = ?, content = ?, image_path = ?, updated_at = NOW(), last_edited_by = ? 
                    WHERE id = ?
                ");
                
                $userId = $_SESSION['user_id'];
                $stmt->execute([$title, $content, $image, $userId, $id]);
                
                // Запись в историю
                $stmt = $db->prepare("
                    INSERT INTO article_history (article_id, user_id, action_type, action_date, old_data, new_data) 
                    VALUES (?, ?, 'edit', NOW(), ?, ?)
                ");
                
                $newData = json_encode([
                    'title' => $title,
                    'content' => $content,
                    'image_path' => $image
                ]);
                
                $stmt->execute([$id, $userId, $oldData, $newData]);
                
                header('Location: /articles?success=updated');
                exit;
            } catch (PDOException $e) {
                $error = 'Ошибка при обновлении статьи';
                require 'modules/articles/edit.php';
                exit;
            }
        } else {
            require 'modules/articles/edit.php';
        }
        break;
        
    case 'delete':
        $id = (int)($_GET['id'] ?? 0);
        
        if (!$id) {
            header('Location: /articles');
            exit;
        }
        
        try {
            // Получение данных статьи перед удалением
            $stmt = $db->prepare("SELECT * FROM articles WHERE id = ? AND is_deleted = 0");
            $stmt->execute([$id]);
            $article = $stmt->fetch();
            
            if (!$article) {
                header('Location: /articles');
                exit;
            }
            
            // Мягкое удаление
            $stmt = $db->prepare("
                UPDATE articles 
                SET is_deleted = 1, deleted_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            
            // Запись в историю
            $stmt = $db->prepare("
                INSERT INTO article_history (article_id, user_id, action_type, action_date, old_data) 
                VALUES (?, ?, 'delete', NOW(), ?)
            ");
            
            $oldData = json_encode([
                'title' => $article['title'],
                'content' => $article['content'],
                'image_path' => $article['image_path']
            ]);
            
            $stmt->execute([$id, $_SESSION['user_id'], $oldData]);
            
            header('Location: /articles?success=deleted');
            exit;
        } catch (PDOException $e) {
            header('Location: /articles?error=delete_failed');
            exit;
        }
        break;
        
    case 'restore':
        $id = (int)($_GET['id'] ?? 0);
        
        if (!$id) {
            header('Location: /articles/history');
            exit;
        }
        
        try {
            // Восстановление статьи
            $stmt = $db->prepare("
                UPDATE articles 
                SET is_deleted = 0, deleted_at = NULL 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            
            // Запись в историю
            $stmt = $db->prepare("
                INSERT INTO article_history (article_id, user_id, action_type, action_date) 
                VALUES (?, ?, 'restore', NOW())
            ");
            $stmt->execute([$id, $_SESSION['user_id']]);
            
            header('Location: /articles?success=restored');
            exit;
        } catch (PDOException $e) {
            header('Location: /articles/history?error=restore_failed');
            exit;
        }
        break;
        
    case 'history':
        // Получение истории изменений
        try {
            $stmt = $db->prepare("
                SELECT h.*, a.title, u.full_name 
                FROM article_history h 
                JOIN articles a ON h.article_id = a.id 
                JOIN users u ON h.user_id = u.id 
                ORDER BY h.action_date DESC
            ");
            $stmt->execute();
            $history = $stmt->fetchAll();
            
            require 'modules/articles/history.php';
        } catch (PDOException $e) {
            $error = 'Ошибка при получении истории';
            require 'modules/articles/history.php';
        }
        break;
        
    case 'view':
        $id = (int)($_GET['id'] ?? 0);
        
        if (!$id) {
            header('Location: /articles');
            exit;
        }
        
        // Получение статьи
        $stmt = $db->prepare("
            SELECT a.*, u.full_name as author_name, e.full_name as editor_name 
            FROM articles a 
            LEFT JOIN users u ON a.created_by = u.id 
            LEFT JOIN users e ON a.last_edited_by = e.id 
            WHERE a.id = ? AND a.is_deleted = 0
        ");
        $stmt->execute([$id]);
        $article = $stmt->fetch();
        
        if (!$article) {
            header('Location: /articles');
            exit;
        }
        
        require 'modules/articles/view.php';
        break;
        
    default:
        // Список статей
        try {
            $stmt = $db->prepare("
                SELECT a.*, u.full_name as author_name, e.full_name as editor_name 
                FROM articles a 
                LEFT JOIN users u ON a.created_by = u.id 
                LEFT JOIN users e ON a.last_edited_by = e.id 
                WHERE a.is_deleted = 0 
                ORDER BY a.created_at DESC
            ");
            $stmt->execute();
            $articles = $stmt->fetchAll();
            
            require 'modules/articles/list.php';
        } catch (PDOException $e) {
            $error = 'Ошибка при получении списка статей';
            require 'modules/articles/list.php';
        }
        break;
} 