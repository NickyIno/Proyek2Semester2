/**
 * Core Application Javascript for Excel-Style PHP App
 */

document.addEventListener("DOMContentLoaded", () => {
    const menuToggle = document.getElementById("mobile-menu-toggle");
    const sidebar = document.getElementById("app-sidebar");
    
    if (menuToggle && sidebar) {
        let overlay = document.createElement("div");
        overlay.className = "overlay";
        document.body.appendChild(overlay);

        function toggleSidebar() {
            sidebar.classList.toggle("mobile-open");
            overlay.classList.toggle("show");
        }

        menuToggle.addEventListener("click", toggleSidebar);
        overlay.addEventListener("click", toggleSidebar);
    }
});

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    background: '#FFFFFF',
    color: '#212121',
    iconColor: '#217346'
});


function showSuccess(msg) {
    Toast.fire({ icon: 'success', title: msg });
}


function showError(msg) {
    Toast.fire({ icon: 'error', title: msg, iconColor: '#C62828' });
}


document.querySelectorAll('.nav-item').forEach(link => {
    if(window.location.href.includes(link.getAttribute('href'))) {
        link.classList.add('active');
    }
});
