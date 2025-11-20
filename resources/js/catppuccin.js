/**
 * Catppuccin Theme Switcher
 *
 * Handles theme switching between Catppuccin flavours:
 * - Latte (light)
 * - Frappé (dark purple)
 * - Macchiato (dark blue)
 * - Mocha (darkest)
 * - Auto (follows system preference)
 */

document.addEventListener('DOMContentLoaded', () => {
  function handleThemeChange() {
    const checkedInput = document.querySelector('input[name="flavour"]:checked');
    if (!checkedInput) return;

    const theme = checkedInput.value;
    if (theme === 'auto') {
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      const defaultTheme = prefersDark ? 'mocha' : 'latte';
      document.documentElement.className = defaultTheme;
      localStorage.setItem('theme', defaultTheme);
      localStorage.setItem('theme-auto', 'true');
    } else {
      document.documentElement.className = theme;
      localStorage.setItem('theme', theme);
      localStorage.setItem('theme-auto', 'false');
    }
  }

  function checkRadioButton(radioButton) {
    if (!radioButton) return;
    if (!radioButton.checked) {
      radioButton.checked = true;
    }
    handleThemeChange();
  }

  // Attach event listeners to all radio buttons
  const form = document.getElementById('flavour-switcher');
  if (form) {
    const radioSelectors = form.querySelectorAll('input[type="radio"]');
    radioSelectors.forEach((selector) => {
      selector.addEventListener('change', handleThemeChange);
    });
  }

  // Initialize theme on page load
  const isThemeAuto = localStorage.getItem('theme-auto');
  if (isThemeAuto === 'true') {
    checkRadioButton(document.getElementById('auto'));
  } else {
    const theme = localStorage.getItem('theme');
    if (theme && ['latte', 'frappe', 'macchiato', 'mocha'].includes(theme)) {
      checkRadioButton(document.getElementById(theme));
    } else {
      // Default to auto if no valid theme is stored
      checkRadioButton(document.getElementById('auto'));
    }
  }

  // Listen for system theme changes when in auto mode
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
  prefersDark.addEventListener('change', (event) => {
    const isThemeAuto = localStorage.getItem('theme-auto');
    if (isThemeAuto === 'true') {
      const defaultTheme = event.matches ? 'mocha' : 'latte';
      document.documentElement.className = defaultTheme;
      localStorage.setItem('theme', defaultTheme);
    }
  });
});
