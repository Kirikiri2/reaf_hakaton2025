<?php
$title = 'История изменений';
ob_start();
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= $title ?></h1>
        <a href="/articles" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> К списку статей
        </a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($history)): ?>
        <div class="alert alert-info">
            История изменений пуста
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Статья</th>
                        <th>Действие</th>
                        <th>Пользователь</th>
                        <th>Дата</th>
                        <th>Изменения</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $record): ?>
                        <tr>
                            <td>
                                <a href="/articles/view?id=<?= $record['article_id'] ?>">
                                    <?= htmlspecialchars($record['title']) ?>
                                </a>
                            </td>
                            <td>
                                <?php
                                $actionClass = match($record['action_type']) {
                                    'create' => 'text-success',
                                    'edit' => 'text-primary',
                                    'delete' => 'text-danger',
                                    'restore' => 'text-warning',
                                    default => ''
                                };
                                $actionIcon = match($record['action_type']) {
                                    'create' => 'fa-plus',
                                    'edit' => 'fa-edit',
                                    'delete' => 'fa-trash',
                                    'restore' => 'fa-undo',
                                    default => ''
                                };
                                $actionText = match($record['action_type']) {
                                    'create' => 'Создание',
                                    'edit' => 'Изменение',
                                    'delete' => 'Удаление',
                                    'restore' => 'Восстановление',
                                    default => $record['action_type']
                                };
                                ?>
                                <span class="<?= $actionClass ?>">
                                    <i class="fas <?= $actionIcon ?>"></i>
                                    <?= $actionText ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($record['full_name']) ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($record['action_date'])) ?></td>
                            <td>
                                <?php if ($record['action_type'] === 'edit'): ?>
                                    <?php
                                    $oldData = json_decode($record['old_data'], true);
                                    $newData = json_decode($record['new_data'], true);
                                    $changes = [];
                                    
                                    if ($oldData['title'] !== $newData['title']) {
                                        $changes[] = 'заголовок';
                                    }
                                    if ($oldData['content'] !== $newData['content']) {
                                        $changes[] = 'содержание';
                                    }
                                    if ($oldData['image_path'] !== $newData['image_path']) {
                                        $changes[] = 'изображение';
                                    }
                                    
                                    echo implode(', ', $changes);
                                    ?>
                                <?php elseif ($record['action_type'] === 'create'): ?>
                                    Создана новая статья
                                <?php elseif ($record['action_type'] === 'delete'): ?>
                                    Статья удалена
                                <?php elseif ($record['action_type'] === 'restore'): ?>
                                    Статья восстановлена
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require 'templates/layout.php';
?> 