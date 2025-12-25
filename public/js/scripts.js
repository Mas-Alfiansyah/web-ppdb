// public/js/scripts.js
$(document).ready(function() {
    $('#sidebar-toggle, #sidebar-overlay').on('click', function() {
        $('#sidebar').toggleClass('-translate-x-full');
        $('#sidebar-overlay').toggleClass('hidden');
    });
});
