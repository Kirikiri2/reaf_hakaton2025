<?php
require_once __DIR__ . '/../../config.php';

// Проверяем авторизацию и права администратора
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: /auth/login');
    exit;
}

$pageTitle = "Редактирование статьи";

// Получаем ID статьи
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: /articles');
    exit;
}

try {
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
            $stmt = $pdo->prepare("
                UPDATE articles 
                SET title = :title, 
                    content = :content, 
                    editor_id = :editor_id,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
            ");
            
            $stmt->execute([
                ':title' => $title,
                ':content' => $content,
                ':editor_id' => $_SESSION['user_id'],
                ':id' => $id
            ]);

            header('Location: /articles/view?id=' . $id);
            exit;
        }
    } else {
        // Получаем данные статьи для редактирования
        $stmt = $pdo->prepare("
            SELECT * FROM articles 
            WHERE id = :id AND deleted_at IS NULL
        ");
        
        $stmt->execute([':id' => $id]);
        $article = $stmt->fetch();

        if (!$article) {
            header('Location: /articles');
            exit;
        }

        $title = $article['title'];
        $content = $article['content'];
    }
} catch (PDOException $e) {
    error_log("Ошибка при работе со статьей: " . $e->getMessage());
    header('Location: /articles');
    exit;
}

require_once __DIR__ . '/../../templates/layout.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/articles">Статьи</a></li>
                    <li class="breadcrumb-item"><a href="/articles/view?id=<?= $id ?>"><?= htmlspecialchars($title) ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Редактирование</li>
                </ol>
            </nav>

            <h1>Редактирование статьи</h1>

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
                           value="<?= htmlspecialchars($title) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Содержание</label>
                    <textarea class="form-control" id="content" name="content" 
                              rows="10" required><?= htmlspecialchars($content) ?></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="/articles/view?id=<?= $id ?>" class="btn btn-secondary">Отмена</a>
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                </div>
            </form>
        </div>
    </div>
</div> 