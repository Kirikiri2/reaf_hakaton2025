-- Создание базы данных
CREATE DATABASE IF NOT EXISTS office_organizer CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE office_organizer;

-- Создание таблицы пользователей
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    login VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Создание таблицы статей
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
) ENGINE=InnoDB;

-- Создание таблицы истории изменений статей
CREATE TABLE IF NOT EXISTS article_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    article_id INT NOT NULL,
    editor_id INT NOT NULL,
    action_type ENUM('create', 'edit', 'delete', 'restore') NOT NULL,
    action_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id),
    FOREIGN KEY (editor_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- Создание таблицы задач
CREATE TABLE IF NOT EXISTS tasks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('new', 'in_progress', 'completed', 'deferred') NOT NULL DEFAULT 'new',
    priority ENUM('low', 'medium', 'high') NOT NULL DEFAULT 'medium',
    creator_id INT NOT NULL,
    assignee_id INT,
    due_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (creator_id) REFERENCES users(id),
    FOREIGN KEY (assignee_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- Создание таблицы комментариев к задачам
CREATE TABLE IF NOT EXISTS task_comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    task_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- Создание таблицы истории изменений задач
CREATE TABLE IF NOT EXISTS task_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    task_id INT NOT NULL,
    user_id INT NOT NULL,
    action_type ENUM('create', 'edit', 'status_change', 'assign', 'comment') NOT NULL,
    old_value TEXT,
    new_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- Создание администратора по умолчанию
-- Пароль: admin123 (хеш для PHP password_hash с PASSWORD_DEFAULT)
INSERT INTO users (login, password, full_name, role) VALUES 
('admin', '$2y$10$8jxXgpEp6ZvuJNE8YTD.9.Lz3YV3LF/lits7FD7F3AMQFsZvZmXTi', 'Administrator', 'admin')
ON DUPLICATE KEY UPDATE id = id;

-- Создание индексов для оптимизации
CREATE INDEX idx_users_login ON users(login);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_articles_created_at ON articles(created_at);
CREATE INDEX idx_tasks_status ON tasks(status);
CREATE INDEX idx_tasks_due_date ON tasks(due_date);
CREATE INDEX idx_tasks_assigned_to ON tasks(assignee_id); 