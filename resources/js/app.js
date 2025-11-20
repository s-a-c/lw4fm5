/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';

/**
 * Initialize theme from data attributes set by the theme script.
 * This ensures the theme is properly initialized even if the inline script
 * hasn't run yet or if there are timing issues.
 */
document.addEventListener('DOMContentLoaded', () => {
    const root = document.documentElement;

    // Ensure theme attributes are set if they're missing
    if (!root.dataset.theme) {
        root.dataset.theme = 'catppuccin';
    }
    if (!root.dataset.flavor) {
        root.dataset.flavor = 'mocha';
    }
    if (!root.dataset.accent) {
        root.dataset.accent = 'primary';
    }

    // Ensure dark class is set based on flavor
    const flavor = root.dataset.flavor;
    const lightFlavors = ['latte', 'lotus', 'day', 'ayu-light'];
    if (lightFlavors.includes(flavor)) {
        root.classList.remove('dark');
    } else {
        root.classList.add('dark');
    }
});
