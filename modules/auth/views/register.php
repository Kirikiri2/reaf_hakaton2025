<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <h1>Регистрация</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="index.php?module=auth&action=register" class="auth-form">
            <div class="form-group">
                <label for="login">Логин (только латинские буквы):</label>
                <input type="text" id="login" name="login" required pattern="[a-zA-Z]+" 
                       value="<?php echo isset($_POST['login']) ? htmlspecialchars($_POST['login']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="full_name">ФИО (только русские буквы):</label>
                <input type="text" id="full_name" name="full_name" required
                       value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
                <small>Используйте латинские буквы, цифры и символы</small>
            </div>
            
            <div class="form-group">
                <label for="password_confirm">Подтверждение пароля:</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                <a href="index.php?module=auth&action=login" class="btn btn-link">Войти</a>
            </div>
        </form>
    </div>
    
    <script src="assets/js/validation.js"></script>
</body>
</html> 