<?php
require_once __DIR__ . '/../../config.php';

// Проверяем авторизацию и права администратора
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: /auth/login');
    exit;
}

$pageTitle = "Создание статьи";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $errors = [];

    if (empty($title)) {
        $errors[] = "Заголовок не может быть пустым";
    }
    if (empty($content)) {
        $errors[] = "Содержание не может быть пустым";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO articles (title, content, editor_id) 
                VALUES (:title, :content, :editor_id)
            ");
            
            $stmt->execute([
                ':title' => $title,
                ':content' => $content,
                ':editor_id' => $_SESSION['user_id']
            ]);

            header('Location: /articles');
            exit;
        } catch (PDOException $e) {
            error_log("Ошибка при создании статьи: " . $e->getMessage());
            $errors[] = "Произошла ошибка при сохранении статьи";
        }
    }
}

require_once __DIR__ . '/../../templates/layout.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1>Создание новой статьи</h1>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" class="mt-4">
                <div class="mb-3">
                    <label for="title" class="form-label">Заголовок</label>
                    <input type="text" class="form-control" id="title" name="title" 
                           value="<?= htmlspecialchars($title ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Содержание</label>
                    <textarea class="form-control" id="content" name="content" rows="10" 
                              required><?= htmlspecialchars($content ?? '') ?></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="/articles" class="btn btn-secondary">Отмена</a>
                    <button type="submit" class="btn btn-primary">Создать статью</button>
                </div>
            </form>
        </div>
    </div>
</div> 