const passwordInput = document.getElementById('password');
const toggleButton = document.getElementById('togglePassword');

if (passwordInput && toggleButton) {
    toggleButton.addEventListener('click', () => {
        console.log('Orvanta: click handler fired');
        const isPasswordHidden = passwordInput.type === 'password';

        passwordInput.type = isPasswordHidden ? 'text' : 'password';

        const icon = toggleButton.querySelector('i');

        if (icon) {
            icon.classList.toggle('bi-eye', !isPasswordHidden);
            icon.classList.toggle('bi-eye-slash', isPasswordHidden);
        }

        toggleButton.setAttribute(
            'aria-label',
            isPasswordHidden ? 'Скрыть пароль' : 'Показать пароль'
        );

        toggleButton.setAttribute(
            'aria-pressed',
            String(isPasswordHidden)
        );
    });
}