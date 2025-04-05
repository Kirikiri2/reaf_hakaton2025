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
    <title>Офисный органайзер</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .navbar {
            background-color: #0d6efd;
            padding: 1rem;
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .nav-link:hover {
            color: rgba(255,255,255,0.8) !important;
        }
        .main-content {
            padding: 20px;
        }
    </style>
</head>
<body>
    <!-- Навигационная панель -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="/organizer">
                <i class="bi bi-calendar3"></i> Органайзер
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/organizer/info">
                            <i class="bi bi-info-circle"></i> Статьи
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/organizer/tasks">
                            <i class="bi bi-list-task"></i> Задачи
                        </a>
                    </li>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/organizer/admin">
                            <i class="bi bi-gear"></i> Управление
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex">
                    <span class="navbar-text me-3">
                        <i class="bi bi-person"></i> 
                        <?php echo htmlspecialchars($_SESSION['role'] === 'admin' ? 'Администратор' : 'Пользователь'); ?>
                    </span>
                    <button class="btn btn-outline-light" id="logoutBtn">
                        <i class="bi bi-box-arrow-right"></i> Выход
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Основной контент -->
    <div class="container main-content">
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-list-task"></i> Задачи
                        </h5>
                        <p class="card-text">Управление задачами и проектами</p>
                        <a href="/organizer/tasks" class="btn btn-primary">Перейти к задачам</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-info-circle"></i> Полезная информация
                        </h5>
                        <p class="card-text">База знаний и справочные материалы</p>
                        <a href="/organizer/info" class="btn btn-primary">Перейти к информации</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('logoutBtn').addEventListener('click', async function(e) {
            e.preventDefault();
            try {
                const response = await fetch('/auth/handler', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=logout'
                });
                
                const data = await response.json();
                if (data.success) {
                    window.location.href = '/auth';
                }
            } catch (error) {
                console.error('Ошибка при выходе:', error);
            }
        });
    </script>
</body>
</html> 