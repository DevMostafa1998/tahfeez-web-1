        const passwordField = document.querySelector('#passwordField');
        const eyeIcon = document.querySelector('#eyeIcon');

        eyeIcon.addEventListener('click', function() {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
