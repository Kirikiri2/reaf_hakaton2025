document.addEventListener('DOMContentLoaded', function() {
    // Валидация логина
    const loginInput = document.getElementById('login');
    if (loginInput) {
        loginInput.addEventListener('input', function() {
            const value = this.value;
            const isValid = /^[a-zA-Z]+$/.test(value);
            this.setCustomValidity(isValid ? '' : 'Используйте только латинские буквы');
        });
    }

    // Валидация ФИО
    const fullNameInput = document.getElementById('full_name');
    if (fullNameInput) {
        fullNameInput.addEventListener('input', function() {
            const value = this.value;
            const isValid = /^[а-яА-ЯёЁ\s]+$/.test(value);
            this.setCustomValidity(isValid ? '' : 'Используйте только русские буквы');
        });
    }

    // Валидация пароля
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirm');
    
    if (passwordInput && passwordConfirmInput) {
        const validatePasswords = function() {
            const password = passwordInput.value;
            const confirm = passwordConfirmInput.value;
            
            if (password !== confirm) {
                passwordConfirmInput.setCustomValidity('Пароли не совпадают');
            } else {
                passwordConfirmInput.setCustomValidity('');
            }
            
            const isValidPassword = /^[a-zA-Z0-9\W]+$/.test(password);
            passwordInput.setCustomValidity(isValidPassword ? '' : 'Используйте латинские буквы, цифры и символы');
        };
        
        passwordInput.addEventListener('input', validatePasswords);
        passwordConfirmInput.addEventListener('input', validatePasswords);
    }
}); 