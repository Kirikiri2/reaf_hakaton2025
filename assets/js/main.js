// Функция для показа уведомлений
function showNotification(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
    alertDiv.style.zIndex = '1050';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Функция для подтверждения действий
function confirmAction(message) {
    return new Promise((resolve) => {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Подтверждение</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>${message}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="button" class="btn btn-primary confirm-btn">Подтвердить</button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        const modalInstance = new bootstrap.Modal(modal);
        
        modal.querySelector('.confirm-btn').addEventListener('click', () => {
            modalInstance.hide();
            resolve(true);
        });
        
        modal.addEventListener('hidden.bs.modal', () => {
            modal.remove();
            resolve(false);
        });
        
        modalInstance.show();
    });
}

// Функция для отправки AJAX запросов
async function fetchApi(url, options = {}) {
    try {
        const response = await fetch(url, {
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            ...options
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.message || 'Произошла ошибка');
        }
        
        return data;
    } catch (error) {
        showNotification(error.message, 'danger');
        throw error;
    }
}

// Функция для форматирования даты
function formatDate(date) {
    return new Date(date).toLocaleString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Обработчик контекстного меню
document.addEventListener('contextmenu', function(e) {
    const contextTarget = e.target.closest('[data-context-menu]');
    if (contextTarget) {
        e.preventDefault();
        
        const menuId = contextTarget.dataset.contextMenu;
        const menu = document.getElementById(menuId);
        
        if (menu) {
            const rect = contextTarget.getBoundingClientRect();
            menu.style.display = 'block';
            menu.style.position = 'fixed';
            menu.style.top = `${e.clientY}px`;
            menu.style.left = `${e.clientX}px`;
            
            const closeMenu = () => {
                menu.style.display = 'none';
                document.removeEventListener('click', closeMenu);
            };
            
            setTimeout(() => {
                document.addEventListener('click', closeMenu);
            });
        }
    }
});

// Инициализация тултипов и поповеров
document.addEventListener('DOMContentLoaded', function() {
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));
    
    const popovers = document.querySelectorAll('[data-bs-toggle="popover"]');
    popovers.forEach(popover => new bootstrap.Popover(popover));
});

// Функция для загрузки изображений
function uploadImage(file, endpoint = '/upload.php') {
    return new Promise((resolve, reject) => {
        const formData = new FormData();
        formData.append('file', file);
        
        fetch(endpoint, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.location) {
                resolve(data.location);
            } else {
                reject(new Error(data.message || 'Ошибка загрузки'));
            }
        })
        .catch(reject);
    });
}

// Функция для валидации форм
function validateForm(form) {
    const errors = [];
    
    // Проверка обязательных полей
    form.querySelectorAll('[required]').forEach(field => {
        if (!field.value.trim()) {
            errors.push(`Поле "${field.dataset.label || field.name}" обязательно для заполнения`);
        }
    });
    
    // Проверка email
    form.querySelectorAll('[type="email"]').forEach(field => {
        if (field.value && !field.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            errors.push('Некорректный email адрес');
        }
    });
    
    // Проверка паролей
    const password = form.querySelector('[name="password"]');
    const confirmPassword = form.querySelector('[name="confirm_password"]');
    if (password && confirmPassword && password.value !== confirmPassword.value) {
        errors.push('Пароли не совпадают');
    }
    
    return errors;
}

// Функция для обновления счетчиков
function updateCounters() {
    document.querySelectorAll('[data-counter]').forEach(async counter => {
        try {
            const response = await fetchApi(counter.dataset.counter);
            counter.textContent = response.count;
        } catch (error) {
            console.error('Ошибка обновления счетчика:', error);
        }
    });
} 