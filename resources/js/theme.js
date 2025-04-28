document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('darkModeSwitch');
    const body = document.body;
    const navbar = document.getElementById('mainNavbar');

    // Function to enable dark mode
    function enableDarkMode() {
        body.classList.remove('bg-body-secondary');
        body.classList.add('bg-secondary', 'text-light');
        navbar.classList.remove('bg-secondary');
        navbar.classList.add('bg-black');
        localStorage.setItem('darkMode', 'enabled'); // Save preference
        if (toggle) toggle.checked = true; // Set the switch ON
    }

    // Function to disable dark mode
    function disableDarkMode() {
        body.classList.remove('bg-secondary', 'text-light');
        body.classList.add('bg-body-secondary');
        navbar.classList.remove('bg-secondary');
        navbar.classList.add('bg-black');
        localStorage.setItem('darkMode', 'disabled'); // Save preference
        if (toggle) toggle.checked = false; // Set the switch OFF
    }

    // Load preference on a page load
    if (localStorage.getItem('darkMode') === 'enabled') {
        enableDarkMode();
    } else {
        disableDarkMode();
    }

    // Listen for a toggle switch
    if (toggle) {
        toggle.addEventListener('change', function () {
            if (this.checked) {
                enableDarkMode();
            } else {
                disableDarkMode();
            }
        });
    }
});
