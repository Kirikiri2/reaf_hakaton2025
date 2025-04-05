<?php
session_start();

// Проверка авторизации
if (!isset($_SESSION['user'])) {
    header('Location: /index.html');
    exit;
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель управления - Офисный веб-органайзер</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .nav-icon {
            font-size: 1.5rem;
            margin-right: 0.5rem;
        }
        .module-nav {
            background-color: #f8f9fa;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Офисный веб-органайзер</a>
            <div class="navbar-text text-white me-3">
                <?php echo htmlspecialchars($user['full_name']); ?>
            </div>
            <button id="logoutBtn" class="btn btn-outline-light">Выйти</button>
        </div>
    </nav>

    <div class="module-nav">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-md-3">
                    <a href="#" class="text-decoration-none text-dark" data-module="info">
                        <i class="bi bi-info-circle nav-icon"></i>
                        <div>Полезная информация</div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="#" class="text-decoration-none text-dark" data-module="tasks">
                        <i class="bi bi-list-task nav-icon"></i>
                        <div>Задачи</div>
                    </a>
                </div>
                <?php if ($user['role'] === 'admin'): ?>
                <div class="col-md-3">
                    <a href="#" class="text-decoration-none text-dark" data-module="admin">
                        <i class="bi bi-gear nav-icon"></i>
                        <div>Администрирование</div>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="container" id="moduleContent">
        <!-- Здесь будет загружаться содержимое модулей -->
    </div>

    <script>
        // Обработка выхода
        document.getElementById('logoutBtn').addEventListener('click', async () => {
            try {
                const response = await fetch('/auth.php', {
                    method: 'DELETE'
                });
                const data = await response.json();
                if (data.success) {
                    window.location.href = '/index.html';
                }
            } catch (error) {
                console.error('Ошибка при выходе:', error);
            }
        });

        // Загрузка модулей
        document.querySelectorAll('[data-module]').forEach(link => {
            link.addEventListener('click', async (e) => {
                e.preventDefault();
                const module = e.currentTarget.dataset.module;
                try {
                    const response = await fetch(`/modules/${module}/index.php`);
                    const content = await response.text();
                    document.getElementById('moduleContent').innerHTML = content;
                } catch (error) {
                    console.error(`Ошибка при загрузке модуля ${module}:`, error);
                }
            });
        });
    </script>
</body>
</html> 