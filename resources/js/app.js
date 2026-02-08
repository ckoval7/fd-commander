import './bootstrap';

// Set initial theme on page load (this is redundant with inline script but kept for fallback)
let theme = localStorage.getItem('theme');
if (!theme) {
    theme = 'light';
    localStorage.setItem('theme', theme);
}
document.documentElement.setAttribute('data-theme', theme);
