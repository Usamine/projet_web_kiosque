// script_sign_in.js
document.addEventListener('DOMContentLoaded', function () {
    const fontAwesome = document.createElement('link');
    fontAwesome.rel = 'stylesheet';
    fontAwesome.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css';
    document.head.appendChild(fontAwesome);

    function animateForm(form) {
        form.style.opacity = '0';
        form.style.transform = 'translateY(20px)';
        form.style.transition = 'all 0.5s ease-out';
        setTimeout(() => {
            form.style.opacity = '1';
            form.style.transform = 'translateY(0)';
        }, 100);
    }

    // Inscription
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        animateForm(registerForm);
        const registerButton = document.getElementById('registerButton');
        const requiredFields = document.querySelectorAll('#registerForm [required]');

        requiredFields.forEach(field => {
            field.addEventListener('input', () => validateField(field));
            field.addEventListener('blur', () => validateField(field));
        });

        function validateField(field) {
            const errorElement = document.getElementById(`${field.id}Error`) || createErrorElement(field);
            if (field.type === 'radio') {
                validateRadioGroup(field.name);
                return;
            }
            if (field.checkValidity()) {
                field.classList.remove('invalid');
                field.classList.add('valid');
                errorElement.textContent = '';
            } else {
                field.classList.remove('valid');
                field.classList.add('invalid');
                showValidationError(field, errorElement);
            }
        }

        function validateRadioGroup(name) {
            const radioGroup = document.querySelectorAll(`input[name="${name}"]`);
            const errorElement = document.getElementById(`${name}Error`) || createErrorElement(radioGroup[0], name);
            const isChecked = Array.from(radioGroup).some(radio => radio.checked);
            radioGroup.forEach(radio => {
                radio.classList.toggle('invalid', !isChecked);
                radio.classList.toggle('valid', isChecked);
            });
            errorElement.textContent = isChecked ? '' : 'Veuillez sélectionner une option';
        }

        function createErrorElement(field, name = null) {
            const errorElement = document.createElement('small');
            errorElement.id = name ? `${name}Error` : `${field.id}Error`;
            errorElement.className = 'error-message';
            errorElement.style.color = 'red';
            errorElement.style.display = 'block';
            errorElement.style.marginTop = '5px';
            if (field.type === 'radio') {
                field.closest('.form-group').appendChild(errorElement);
            } else {
                field.insertAdjacentElement('afterend', errorElement);
            }
            return errorElement;
        }

        function showValidationError(field, errorElement) {
            if (field.validity.valueMissing) {
                errorElement.textContent = 'Ce champ est obligatoire';
            } else if (field.validity.typeMismatch) {
                errorElement.textContent = 'Format incorrect';
            } else if (field.validity.tooShort) {
                errorElement.textContent = `Trop court (min ${field.minLength} caractères)`;
            } else if (field.validity.patternMismatch && field.id === 'immatriculation') {
                errorElement.textContent = 'Format AA-123-BB ou 1234-A-56';
            }
        }

        const immatriculationField = document.getElementById('immatriculation');
        if (immatriculationField) {
            immatriculationField.addEventListener('input', function () {
                const regex = /^(([A-Za-z]{2}-\d{3}-[A-Za-z]{2})|(\d{4}-[A-Za-z]{1}-\d{2}))$/;
                this.setCustomValidity(this.value && !regex.test(this.value) ? 'Format valide : AA-123-BB ou 1234-A-56' : '');
            });
        }

        const passwordField = document.getElementById('registerPassword');
        const confirmPasswordField = document.getElementById('registerPasswordConfirm');
        if (passwordField && confirmPasswordField) {
            confirmPasswordField.addEventListener('input', function () {
                this.setCustomValidity(this.value !== passwordField.value ? 'Les mots de passe ne correspondent pas' : '');
            });
        }

        registerForm.addEventListener('submit', function (e) {
            let isValid = true;
            requiredFields.forEach(field => {
                if (field.type === 'radio') {
                    validateRadioGroup(field.name);
                    if (!document.querySelector(`input[name="${field.name}"]:checked`)) isValid = false;
                } else if (!field.checkValidity()) {
                    validateField(field);
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();
                registerForm.classList.add('shake');
                setTimeout(() => registerForm.classList.remove('shake'), 500);
            }
        });

        registerButton.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 5px 15px rgba(239, 68, 68, 0.4)';
        });
        registerButton.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    }

    // Connexion
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        animateForm(loginForm);
        const loginButton = document.getElementById('loginButton');
        const emailField = document.getElementById('loginEmail');
        const passwordField = document.getElementById('loginPassword');

        [emailField, passwordField].forEach(field => {
            field.addEventListener('input', () => validateField(field));
        });

        function validateField(field) {
            const errorElement = document.getElementById(`${field.id}Error`) || createErrorElement(field);
            if (field.checkValidity()) {
                field.classList.remove('invalid');
                errorElement.textContent = '';
            } else {
                field.classList.add('invalid');
                showValidationError(field, errorElement);
            }
        }

        function createErrorElement(field) {
            const errorElement = document.createElement('small');
            errorElement.id = `${field.id}Error`;
            errorElement.className = 'error-message';
            errorElement.style.color = 'red';
            errorElement.style.display = 'block';
            errorElement.style.marginTop = '5px';
            field.insertAdjacentElement('afterend', errorElement);
            return errorElement;
        }

        function showValidationError(field, errorElement) {
            if (field.validity.valueMissing) {
                errorElement.textContent = 'Ce champ est obligatoire';
            } else if (field.validity.typeMismatch && field.id === 'loginEmail') {
                errorElement.textContent = 'Veuillez entrer un email valide';
            }
        }

        loginForm.addEventListener('submit', function (e) {
            let isValid = true;
            [emailField, passwordField].forEach(field => {
                if (!field.value) {
                    validateField(field);
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();
                loginForm.classList.add('shake');
                setTimeout(() => loginForm.classList.remove('shake'), 500);
            }
        });

        loginButton.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 5px 15px rgba(239, 68, 68, 0.4)';
        });
        loginButton.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    }
});