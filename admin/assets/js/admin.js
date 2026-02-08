/**
 * Admin Dashboard Core Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
    // Current Navigation Highlighting (Already handled by PHP but good to have JS hooks)
    const currentPath = window.location.pathname.split('/').pop();

    // Auto-dismiss Alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert, [style*="background: rgba(0, 229, 255, 0.1)"]');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.6s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 600);
        }, 5000);
    });

    // Confirmation for Delete Actions (Standardized)
    const deleteButtons = document.querySelectorAll('[onclick*="confirm"]');
    deleteButtons.forEach(btn => {
        btn.removeAttribute('onclick');
        btn.addEventListener('click', (e) => {
            if (!confirm('Are you sure you want to perform this action? This cannot be undone.')) {
                e.preventDefault();
            }
        });
    });

    // Image Preview for File Inputs
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    // Look for closest img preview
                    const preview = this.parentElement.parentElement.querySelector('img');
                    if (preview) {
                        preview.src = e.target.result;
                    }
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
});
