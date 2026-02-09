document.addEventListener('DOMContentLoaded', function() {
    const notification = document.getElementById('notification');
    
    if (notification) {
        setTimeout(function() {
            notification.style.display = 'none';
        }, 2800);
    }
});