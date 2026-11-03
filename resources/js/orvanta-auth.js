const passwordToggleButtons = document.querySelectorAll('.login-password-toggle');
passwordToggleButtons.forEach((toggleButton) => {
    const passwordId = toggleButton.getAttribute('aria-controls');
    const passwordInput = document.getElementById(passwordId);
    if (!passwordInput) {
        return;
    }
    toggleButton.addEventListener('click', () => {
        const isPasswordHidden = passwordInput.type === 'password';
        passwordInput.type = isPasswordHidden ? 'text' : 'password';
        const icon = toggleButton.querySelector('i');
        if (icon) {
            icon.classList.toggle('bi-eye', !isPasswordHidden);
            icon.classList.toggle('bi-eye-slash', isPasswordHidden);
        }
        toggleButton.setAttribute('aria-label', isPasswordHidden ? 'Скрыть пароль' : 'Показать пароль');
        toggleButton.setAttribute('aria-pressed', String(isPasswordHidden));
    });
});