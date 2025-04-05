<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /auth');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Информационный раздел</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tinymce@6.8.2/skins/ui/oxide/skin.min.css" rel="stylesheet">
    <link href="/modules/info/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Статьи</h1>
            <button class="btn btn-primary" id="newArticleBtn">Новая статья</button>
        </div>
        
        <div id="articlesList" class="row"></div>
    </div>

    <div class="modal fade" id="articleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Новая статья</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="articleForm">
                        <input type="hidden" id="articleId">
                        <div class="mb-3">
                            <label for="articleTitle" class="form-label">Заголовок</label>
                            <input type="text" class="form-control" id="articleTitle" required>
                        </div>
                        <div class="mb-3">
                            <label for="articleImage" class="form-label">Изображение</label>
                            <input type="file" class="form-control" id="articleImage" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label for="articleContent" class="form-label">Содержание</label>
                            <textarea class="form-control" id="articleContent"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" form="articleForm" class="btn btn-primary">Сохранить</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewArticleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.2/tinymce.min.js"></script>
    <script src="/modules/info/js/articles.js"></script>
</body>
</html>