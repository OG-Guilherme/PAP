/**
 * theme.js - EduWeb Theme Toggle
 * Preserves scroll position on theme change and animates smoothly.
 */
(function () {
    // On page load, restore scroll position if returning from theme toggle
    var savedScroll = sessionStorage.getItem('scrollY_after_theme');
    if (savedScroll !== null) {
        window.scrollTo(0, parseInt(savedScroll, 10));
        sessionStorage.removeItem('scrollY_after_theme');
    }

    // Fade-in animation on every page load
    document.addEventListener('DOMContentLoaded', function () {
        document.body.classList.add('theme-animating');
        setTimeout(function () {
            document.body.classList.remove('theme-animating');
        }, 400);
    });

    // Expose global toggle function
    window.toggleTheme = function (url) {
        // Save current scroll position
        sessionStorage.setItem('scrollY_after_theme', window.scrollY);
        // Quick fade out then navigate
        document.body.style.transition = 'opacity 0.2s ease';
        document.body.style.opacity = '0';
        setTimeout(function () {
            window.location.href = url;
        }, 200);
    };
})();
