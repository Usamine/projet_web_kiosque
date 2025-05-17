document.addEventListener('DOMContentLoaded', function() {
    const notification = document.getElementById('notification');
    if (notification && notification.classList.contains('show')) {
        setTimeout(() => {
            notification.classList.remove('show');
        }, 3000);
    }
});

// Form validation and submission handling
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', (e) => {
        // Les formulaires seront traités par le PHP, nous n'avons donc pas à empêcher 
        // le comportement par défaut comme dans l'exemple original
    });
});

// Time period selector
document.querySelectorAll('.time-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        if (!btn.getAttribute('type') || btn.getAttribute('type') !== 'submit') {
            btn.parentElement.querySelectorAll('.time-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        }
    });
});

// Image upload preview
document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            const preview = this.parentElement.querySelector('.upload-preview');
            
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" alt="Aperçu">`;
            }
            
            reader.readAsDataURL(file);
        }
    });
});

// Toggle switch functionality
document.querySelectorAll('.toggle-switch input').forEach(toggle => {
    toggle.addEventListener('change', function() {
        const label = this.parentElement.nextElementSibling;
        if (label) {
            if (this.checked) {
                label.textContent = label.textContent.replace('Inactif', 'Actif');
            } else {
                label.textContent = label.textContent.replace('Actif', 'Inactif');
            }
        }
    });
});