<?php
require_once __DIR__ . '/../../config.php';

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login');
    exit;
}

$pageTitle = "Просмотр статьи";

// Получаем ID статьи
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: /articles');
    exit;
}

try {
    // Получаем статью и информацию о редакторе
    $stmt = $pdo->prepare("
        SELECT a.*, u.full_name as editor_name 
        FROM articles a 
        LEFT JOIN users u ON a.editor_id = u.id 
        WHERE a.id = :id AND a.deleted_at IS NULL
    ");
    
    $stmt->execute([':id' => $id]);
    $article = $stmt->fetch();

    if (!$article) {
        header('Location: /articles');
        exit;
    }
} catch (PDOException $e) {
    error_log("Ошибка при получении статьи: " . $e->getMessage());
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
                    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($article['title']) ?></li>
                </ol>
            </nav>

            <article class="bg-white p-4 rounded shadow-sm">
                <h1 class="mb-4"><?= htmlspecialchars($article['title']) ?></h1>
                
                <div class="text-muted mb-4">
                    <small>
                        Редактор: <?= htmlspecialchars($article['editor_name']) ?><br>
                        Дата создания: <?= date('d.m.Y H:i', strtotime($article['created_at'])) ?>
                        <?php if ($article['updated_at'] != $article['created_at']): ?>
                            <br>Последнее обновление: <?= date('d.m.Y H:i', strtotime($article['updated_at'])) ?>
                        <?php endif; ?>
                    </small>
                </div>

                <div class="article-content">
                    <?= nl2br(htmlspecialchars($article['content'])) ?>
                </div>

                <?php if ($_SESSION['is_admin']): ?>
                    <div class="mt-4 pt-3 border-top">
                        <div class="btn-group">
                            <a href="/articles/edit?id=<?= $article['id'] ?>" class="btn btn-outline-primary">Редактировать</a>
                            <button onclick="deleteArticle(<?= $article['id'] ?>)" class="btn btn-outline-danger">Удалить</button>
                        </div>
                    </div>
                <?php endif; ?>
            </article>
        </div>
    </div>
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
                window.location.href = '/articles';
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