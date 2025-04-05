<?php
try {
    // Подключение к MySQL с правильным паролем
    $pdo = new PDO(
        'mysql:host=127.0.0.1;port=3306;charset=utf8mb4',
        'root',
        '29052008Slava'  // Правильный пароль
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Подключение к MySQL успешно\n";
    
    // Создание базы данных
    $pdo->exec('CREATE DATABASE IF NOT EXISTS office_organizer CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    echo "База данных создана или уже существует\n";
    
    // Подключение к базе данных
    $pdo->exec('USE office_organizer');
    
    // Создание таблицы пользователей
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            login VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            role ENUM("admin", "user") NOT NULL DEFAULT "user",
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ');
    echo "Таблица users создана\n";
    
    // Создание таблицы статей
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS articles (
            id INT PRIMARY KEY AUTO_INCREMENT,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            image_path VARCHAR(255),
            editor_id INT NOT NULL,
            is_deleted TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (editor_id) REFERENCES users(id)
        )
    ');
    echo "Таблица articles создана\n";
    
    // Создание таблицы истории
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS article_history (
            id INT PRIMARY KEY AUTO_INCREMENT,
            article_id INT NOT NULL,
            editor_id INT NOT NULL,
            action_type ENUM("create", "edit", "delete", "restore") NOT NULL,
            action_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (article_id) REFERENCES articles(id),
            FOREIGN KEY (editor_id) REFERENCES users(id)
        )
    ');
    echo "Таблица article_history создана\n";
    
    // Создание администратора
    $stmt = $pdo->prepare('
        INSERT INTO users (login, password, full_name, role)
        VALUES ("admin", "$2y$10$8jxXgpEp6ZvuJNE8YTD.9.Lz3YV3LF/lits7FD7F3AMQFsZvZmXTi", "Administrator", "admin")
        ON DUPLICATE KEY UPDATE id=id
    ');
    $stmt->execute();
    echo "Администратор создан (если не существовал)\n";
    
    echo "\nИнициализация базы данных завершена успешно\n";
    echo "Данные для входа администратора:\n";
    echo "Логин: admin\n";
    echo "Пароль: admin123\n";
    
} catch (PDOException $e) {
    die("Ошибка подключения к MySQL: " . $e->getMessage() . "\n");
} 