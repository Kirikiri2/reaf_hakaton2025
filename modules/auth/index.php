<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: /organizer');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .auth-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
        }
        .error-message {
            color: #dc3545;
            margin-top: 10px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="auth-container">
            <h2 class="text-center mb-4">Авторизация</h2>
            <form id="loginForm">
                <div class="mb-3">
                    <label for="login" class="form-label">Логин</label>
                    <input type="text" class="form-control" id="login" name="login" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Пароль</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Войти</button>
                </div>
                <div class="text-center mt-3">
                    <a href="#" id="showRegister">Регистрация</a>
                </div>
                <div class="error-message" id="errorMessage"></div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="registerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Регистрация</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="registerForm">
                        <div class="mb-3">
                            <label for="regLogin" class="form-label">Логин</label>
                            <input type="text" class="form-control" id="regLogin" name="login" required>
                        </div>
                        <div class="mb-3">
                            <label for="fullName" class="form-label">ФИО</label>
                            <input type="text" class="form-control" id="fullName" name="full_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="regPassword" class="form-label">Пароль</label>
                            <input type="password" class="form-control" id="regPassword" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Подтверждение пароля</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                        </div>
                        <div class="error-message" id="registerErrorMessage"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const showRegisterLink = document.getElementById('showRegister');
            const registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
            const errorMessage = document.getElementById('errorMessage');
            const registerErrorMessage = document.getElementById('registerErrorMessage');

            showRegisterLink.addEventListener('click', function(e) {
                e.preventDefault();
                registerModal.show();
            });

            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(loginForm);
                formData.append('action', 'login');

                fetch('/modules/auth/auth_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = '/organizer';
                    } else {
                        errorMessage.textContent = data.error;
                        errorMessage.style.display = 'block';
                    }
                })
                .catch(error => {
                    errorMessage.textContent = 'Произошла ошибка при авторизации';
                    errorMessage.style.display = 'block';
                });
            });

            registerForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(registerForm);
                formData.append('action', 'register');

                fetch('/modules/auth/auth_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        registerModal.hide();
                        window.location.href = '/organizer';
                    } else {
                        registerErrorMessage.textContent = data.error;
                        registerErrorMessage.style.display = 'block';
                    }
                })
                .catch(error => {
                    registerErrorMessage.textContent = 'Произошла ошибка при регистрации';
                    registerErrorMessage.style.display = 'block';
                });
            });
        });
    </script>
</body>
</html> 