<?php
$title = 'Полезная информация';
ob_start();
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= $title ?></h1>
        <div>
            <a href="/articles/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Добавить статью
            </a>
            <?php if (isAdmin()): ?>
            <a href="/articles/history" class="btn btn-secondary">
                <i class="fas fa-history"></i> История изменений
            </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php
            $message = match($_GET['success']) {
                'created' => 'Статья успешно создана',
                'updated' => 'Статья успешно обновлена',
                'deleted' => 'Статья успешно удалена',
                'restored' => 'Статья успешно восстановлена',
                default => 'Операция выполнена успешно'
            };
            echo $message;
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($articles)): ?>
        <div class="alert alert-info">
            Статьи пока не добавлены
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($articles as $article): ?>
                <div class="col">
                    <div class="card h-100">
                        <?php if ($article['image_path']): ?>
                            <img src="<?= $article['image_path'] ?>" class="card-img-top" alt="<?= htmlspecialchars($article['title']) ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($article['title']) ?></h5>
                            <p class="card-text text-muted small">
                                Автор: <?= htmlspecialchars($article['author_name']) ?><br>
                                Создано: <?= date('d.m.Y H:i', strtotime($article['created_at'])) ?>
                            </p>
                            <?php if ($article['created_at'] !== $article['updated_at']): ?>
                                <p class="card-text text-muted small">
                                    Последнее изменение: <?= htmlspecialchars($article['editor_name']) ?><br>
                                    <?= date('d.m.Y H:i', strtotime($article['updated_at'])) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-transparent border-top-0">
                            <div class="btn-group w-100">
                                <a href="/articles/view?id=<?= $article['id'] ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-eye"></i> Просмотр
                                </a>
                                <a href="/articles/edit?id=<?= $article['id'] ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-edit"></i> Изменить
                                </a>
                                <button type="button" class="btn btn-outline-danger" 
                                        onclick="if(confirm('Вы уверены, что хотите удалить эту статью?')) window.location.href='/articles/delete?id=<?= $article['id'] ?>'">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require 'templates/layout.php';
?> 