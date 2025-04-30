class NotificationManager {
    constructor() {
        console.log('Initialisation du gestionnaire de notifications');
        this.setupToastr();
        this.showFlashMessages();
    }

    setupToastr() {
        toastr.options = {
            closeButton: true,
            debug: false,
            newestOnTop: true,
            progressBar: true,
            positionClass: "toast-top-right",
            preventDuplicates: false,
            onclick: null,
            showDuration: "300",
            hideDuration: "1000",
            timeOut: "5000",
            extendedTimeOut: "1000",
            showEasing: "swing",
            hideEasing: "linear",
            showMethod: "fadeIn",
            hideMethod: "fadeOut"
        };
    }

    showFlashMessages() {
        const flashMessages = document.querySelectorAll('.flash-message');
        flashMessages.forEach(flash => {
            const type = flash.dataset.type || 'info';
            const message = flash.textContent;
            toastr[type](message);
        });
    }
}

// Initialiser le gestionnaire de notifications
document.addEventListener('DOMContentLoaded', () => {
    window.notificationManager = new NotificationManager();
});
