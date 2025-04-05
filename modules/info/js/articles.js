// Инициализация TinyMCE
function initTinyMCE() {
    tinymce.init({
        selector: '#content',
        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
        toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
        height: 400
    });
}

// Загрузка статей
async function loadArticles() {
    try {
        const response = await fetch('/modules/info/info_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=get_articles'
        });
        
        const data = await response.json();
        if (data.success) {
            const grid = document.getElementById('articlesGrid');
            grid.innerHTML = '';
            
            if (data.articles && data.articles.length > 0) {
                data.articles.forEach(article => {
                    const col = document.createElement('div');
                    col.className = 'col-md-4';
                    
                    const card = document.createElement('div');
                    card.className = 'card article-card';
                    card.dataset.id = article.id;
                    
                    // Добавляем обработчики для контекстного меню
                    card.addEventListener('contextmenu', showContextMenu);
                    
                    let imageHtml = '';
                    if (article.image_path) {
                        imageHtml = `<img src="/${article.image_path}" class="card-img-top" alt="${article.title}">`;
                    }
                    
                    card.innerHTML = `
                        ${imageHtml}
                        <div class="card-body">
                            <h5 class="card-title">${article.title}</h5>
                            <p class="card-text">${article.content.substring(0, 100)}...</p>
                            <div class="text-muted small">
                                <i class="bi bi-person"></i> ${article.editor_name}<br>
                                <i class="bi bi-clock"></i> ${new Date(article.created_at).toLocaleString()}
                            </div>
                        </div>
                        ${article.has_history ? '<span class="history-badge"><i class="bi bi-clock-history"></i></span>' : ''}
                    `;
                    
                    col.appendChild(card);
                    grid.appendChild(col);
                });
            } else {
                grid.innerHTML = '<div class="col-12 text-center">Нет статей</div>';
            }
        } else {
            console.error('Ошибка при загрузке статей:', data.error);
        }
    } catch (error) {
        console.error('Ошибка при загрузке статей:', error);
    }
}

// Загрузка истории
async function loadHistory() {
    try {
        const response = await fetch('modules/info/info_handler.php?action=get_history');
        const data = await response.json();
        
        if (data.success) {
            const historyTableBody = document.getElementById('historyTableBody');
            
            if (data.history.length === 0) {
                historyTableBody.innerHTML = '<tr><td colspan="5" class="text-center">История изменений пуста</td></tr>';
                return;
            }
            
            historyTableBody.innerHTML = data.history.map(item => `
                <tr>
                    <td>${new Date(item.created_at).toLocaleString()}</td>
                    <td>${getActionName(item.action)}</td>
                    <td>${item.user_name}</td>
                    <td>${item.title}</td>
                    <td>
                        ${item.can_restore ? `
                            <button class="btn btn-sm btn-success" onclick="restoreArticle(${item.article_id})">
                                Восстановить
                            </button>
                        ` : ''}
                    </td>
                </tr>
            `).join('');
        }
    } catch (error) {
        console.error('Error loading history:', error);
        alert('Ошибка при загрузке истории');
    }
}

// Показ модального окна создания статьи
function showCreateArticleModal() {
    document.getElementById('articleId').value = '';
    document.getElementById('title').value = '';
    tinymce.get('content').setContent('');
    document.getElementById('image').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('articleModal'));
    modal.show();
}

// Показ модального окна истории
function showHistoryModal() {
    loadHistory();
    const modal = new bootstrap.Modal(document.getElementById('historyModal'));
    modal.show();
}

// Редактирование статьи
async function editArticle(id) {
    try {
        const response = await fetch(`modules/info/info_handler.php?action=get_articles&id=${id}`);
        const data = await response.json();
        
        if (data.success) {
            const article = data.article;
            document.getElementById('articleId').value = article.id;
            document.getElementById('title').value = article.title;
            tinymce.get('content').setContent(article.content);
            
            const modal = new bootstrap.Modal(document.getElementById('articleModal'));
            modal.show();
        }
    } catch (error) {
        console.error('Error loading article:', error);
        alert('Ошибка при загрузке статьи');
    }
}

// Сохранение статьи
async function saveArticle() {
    console.log('Начало сохранения статьи');
    
    const form = document.getElementById('articleForm');
    if (!form.checkValidity()) {
        console.log('Форма не валидна');
        form.classList.add('was-validated');
        return;
    }
    
    const title = document.getElementById('title').value.trim();
    const content = tinymce.get('content').getContent().trim();
    
    console.log('Данные формы:', { title, content });
    
    if (!title || !content) {
        alert('Пожалуйста, заполните все обязательные поля');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'save_article');
    formData.append('title', title);
    formData.append('content', content);
    
    const articleId = document.getElementById('articleId').value;
    if (articleId) {
        formData.append('id', articleId);
    }
    
    const imageFile = document.getElementById('image').files[0];
    if (imageFile) {
        formData.append('image', imageFile);
    }
    
    try {
        console.log('Отправка запроса на сервер');
        const response = await fetch('/modules/info/info_handler.php', {
            method: 'POST',
            body: formData
        });
        
        console.log('Получен ответ от сервера');
        const data = await response.json();
        console.log('Ответ сервера:', data);
        
        if (data.success) {
            // Закрываем модальное окно
            const modalElement = document.getElementById('articleModal');
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
            
            // Очищаем форму
            form.reset();
            tinymce.get('content').setContent('');
            
            // Убираем backdrop и восстанавливаем скролл
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            
            // Обновляем список статей
            await loadArticles();
        } else {
            alert(data.error || 'Ошибка при сохранении статьи');
        }
    } catch (error) {
        console.error('Ошибка при сохранении:', error);
        alert('Ошибка при сохранении статьи');
    }
}

// Удаление статьи
async function deleteArticle(articleId) {
    try {
        const response = await fetch('info_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete_article&id=${articleId}`
        });
        
        const data = await response.json();
        if (data.success) {
            loadArticles();
        } else {
            alert(data.error || 'Ошибка при удалении статьи');
        }
    } catch (error) {
        console.error('Ошибка при удалении статьи:', error);
    }
}

// Восстановление статьи
async function restoreArticle(articleId) {
    try {
        const response = await fetch('info_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=restore_article&id=${articleId}`
        });
        
        const data = await response.json();
        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('historyModal'));
            modal.hide();
            loadArticles();
        } else {
            alert(data.error || 'Ошибка при восстановлении статьи');
        }
    } catch (error) {
        console.error('Ошибка при восстановлении статьи:', error);
    }
}

// Получение названия действия
function getActionName(action) {
    const actions = {
        'create': 'Создание',
        'update': 'Обновление',
        'delete': 'Удаление',
        'restore': 'Восстановление'
    };
    return actions[action] || action;
}

// Загрузка статей при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    // Загрузка статей при загрузке страницы
    loadArticles();

    // Обработчик выхода
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

    // Обработчик отправки формы
    const form = document.getElementById('articleForm');
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            await saveArticle();
        });
    }

    // Закрытие контекстного меню при клике вне его
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.context-menu') && !e.target.closest('.article-card')) {
            hideContextMenu();
        }
    });
});

// Показать контекстное меню
function showContextMenu(e) {
    e.preventDefault();
    
    const contextMenu = document.getElementById('contextMenu');
    const articleId = this.dataset.id;
    
    // Устанавливаем позицию меню
    contextMenu.style.display = 'block';
    contextMenu.style.left = e.pageX + 'px';
    contextMenu.style.top = e.pageY + 'px';
    
    // Добавляем обработчики для пунктов меню
    const menuItems = contextMenu.querySelectorAll('.context-menu-item');
    menuItems.forEach(item => {
        item.onclick = () => handleContextMenuAction(item.dataset.action, articleId);
    });
}

// Скрыть контекстное меню
function hideContextMenu() {
    const contextMenu = document.getElementById('contextMenu');
    contextMenu.style.display = 'none';
}

// Обработка действий контекстного меню
async function handleContextMenuAction(action, articleId) {
    hideContextMenu();
    
    switch (action) {
        case 'edit':
            await loadArticleForEdit(articleId);
            break;
        case 'history':
            await loadArticleHistory(articleId);
            break;
        case 'delete':
            if (confirm('Вы уверены, что хотите удалить эту статью?')) {
                await deleteArticle(articleId);
            }
            break;
    }
}

// Загрузка статьи для редактирования
async function loadArticleForEdit(articleId) {
    try {
        const response = await fetch('info_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=get_articles&id=${articleId}`
        });
        
        const data = await response.json();
        if (data.success && data.article) {
            document.getElementById('articleId').value = data.article.id;
            document.getElementById('title').value = data.article.title;
            tinymce.get('content').setContent(data.article.content);
            
            document.getElementById('articleModalTitle').textContent = 'Редактирование статьи';
            const modal = new bootstrap.Modal(document.getElementById('articleModal'));
            modal.show();
        }
    } catch (error) {
        console.error('Ошибка при загрузке статьи:', error);
    }
}

// Загрузка истории статьи
async function loadArticleHistory(articleId) {
    try {
        const response = await fetch('/info/handler', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=get_history&article_id=${articleId}`
        });
        
        const data = await response.json();
        if (data.success) {
            const historyList = document.getElementById('historyList');
            historyList.innerHTML = '';
            
            data.history.forEach(item => {
                const historyItem = document.createElement('div');
                historyItem.className = 'list-group-item';
                
                let actionText = '';
                switch (item.action_type) {
                    case 'create': actionText = 'Создание'; break;
                    case 'edit': actionText = 'Редактирование'; break;
                    case 'delete': actionText = 'Удаление'; break;
                    case 'restore': actionText = 'Восстановление'; break;
                }
                
                historyItem.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">${actionText}</h6>
                            <small class="text-muted">
                                <i class="bi bi-person"></i> ${item.user_name}
                                <i class="bi bi-clock ms-2"></i> ${new Date(item.action_date).toLocaleString()}
                            </small>
                        </div>
                        ${item.action_type === 'delete' && isWithinWeek(item.action_date) ? 
                            `<button class="btn btn-sm btn-outline-primary" onclick="restoreArticle(${item.article_id})">
                                <i class="bi bi-arrow-counterclockwise"></i> Восстановить
                            </button>` : ''}
                    </div>
                `;
                
                historyList.appendChild(historyItem);
            });
            
            const modal = new bootstrap.Modal(document.getElementById('historyModal'));
            modal.show();
        }
    } catch (error) {
        console.error('Ошибка при загрузке истории:', error);
    }
}

// Проверка, прошла ли неделя с даты
function isWithinWeek(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
    return date > weekAgo;
} 