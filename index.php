<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /modules/auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'user';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Офисный органайзер</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .module-card {
            height: 100%;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .module-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <i class="bi bi-calendar3"></i> Органайзер
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="/modules/info">
                            <i class="bi bi-info-circle"></i> Статьи
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/modules/tasks">
                            <i class="bi bi-list-task"></i> Задачи
                        </a>
                    </li>
                    <?php if ($role === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/modules/admin">
                            <i class="bi bi-gear"></i> Управление
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex">
                    <span class="navbar-text me-3">
                        <i class="bi bi-person"></i> 
                        <?php echo $role === 'admin' ? 'Администратор' : 'Пользователь'; ?>
                    </span>
                    <a href="/modules/auth/logout.php" class="btn btn-outline-light">
                        <i class="bi bi-box-arrow-right"></i> Выход
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container main-content">
        <h1 class="mb-4">Добро пожаловать в офисный органайзер</h1>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card module-card" onclick="window.location.href='/modules/info'">
                    <div class="card-body text-center">
                        <i class="bi bi-info-circle display-4 mb-3"></i>
                        <h5 class="card-title">Полезная информация</h5>
                        <p class="card-text">Создание и управление статьями</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card module-card" onclick="window.location.href='/modules/tasks'">
                    <div class="card-body text-center">
                        <i class="bi bi-list-task display-4 mb-3"></i>
                        <h5 class="card-title">Задачи</h5>
                        <p class="card-text">Управление задачами и проектами</p>
                    </div>
                </div>
            </div>
            
            <?php if ($role === 'admin'): ?>
            <div class="col-md-4">
                <div class="card module-card" onclick="window.location.href='/modules/admin'">
                    <div class="card-body text-center">
                        <i class="bi bi-gear display-4 mb-3"></i>
                        <h5 class="card-title">Управление</h5>
                        <p class="card-text">Администрирование системы</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 