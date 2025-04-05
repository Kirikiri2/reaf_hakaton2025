<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в систему</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <h1>Вход в систему</h1>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                Регистрация успешно завершена. Войдите в систему.
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="index.php?module=auth&action=login" class="auth-form">
            <div class="form-group">
                <label for="login">Логин:</label>
                <input type="text" id="login" name="login" required>
            </div>
            
            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Войти</button>
                <a href="index.php?module=auth&action=register" class="btn btn-link">Регистрация</a>
            </div>
        </form>
    </div>
    
    <script src="assets/js/validation.js"></script>
</body>
</html> 