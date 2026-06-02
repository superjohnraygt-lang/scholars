document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s ease';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
    
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const gwaInput = form.querySelector('input[name="gwa"]');
            if (gwaInput) {
                const gwa = parseFloat(gwaInput.value);
                if (gwa < 0.00 || gwa > 5.00) {
                    e.preventDefault();
                    alert('GWA must be between 0.00 and 5.00');
                    gwaInput.focus();
                }
            }
        });
    });
});