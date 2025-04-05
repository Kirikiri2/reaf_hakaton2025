<?php
require_once __DIR__ . '/../../config.php';

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login');
    exit;
}

$pageTitle = "Статьи";

try {
    $stmt = $pdo->query("
        SELECT a.*, u.full_name as editor_name 
        FROM articles a 
        LEFT JOIN users u ON a.editor_id = u.id 
        WHERE a.deleted_at IS NULL 
        ORDER BY a.created_at DESC
    ");
    $articles = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Ошибка при получении статей: " . $e->getMessage());
    $articles = [];
}

require_once __DIR__ . '/../../templates/layout.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Статьи</h1>
        <?php if ($_SESSION['is_admin']): ?>
            <a href="/articles/create" class="btn btn-primary">Создать статью</a>
        <?php endif; ?>
    </div>

    <?php if (empty($articles)): ?>
        <div class="alert alert-info">
            Статей пока нет.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($articles as $article): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($article['title']) ?></h5>
                            <p class="card-text"><?= nl2br(htmlspecialchars(substr($article['content'], 0, 200))) ?>...</p>
                            <p class="card-text">
                                <small class="text-muted">
                                    Редактор: <?= htmlspecialchars($article['editor_name']) ?><br>
                                    Дата: <?= date('d.m.Y H:i', strtotime($article['created_at'])) ?>
                                </small>
                            </p>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="btn-group">
                                <a href="/articles/view?id=<?= $article['id'] ?>" class="btn btn-sm btn-outline-primary">Просмотр</a>
                                <?php if ($_SESSION['is_admin']): ?>
                                    <a href="/articles/edit?id=<?= $article['id'] ?>" class="btn btn-sm btn-outline-secondary">Редактировать</a>
                                    <button onclick="deleteArticle(<?= $article['id'] ?>)" class="btn btn-sm btn-outline-danger">Удалить</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function deleteArticle(id) {
    if (confirm('Вы уверены, что хотите удалить эту статью?')) {
        fetch(`/articles/delete?id=${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Ошибка при удалении статьи');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ошибка при удалении статьи');
        });
    }
}
</script> 