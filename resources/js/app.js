/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 *
 * @format
 */

import './echo';

/**
 * Initialize theme from data attributes set by the theme script.
 * This ensures the theme is properly initialized even if the inline script
 * hasn't run yet or if there are timing issues.
 */
const lightFlavors = ['latte', 'lotus', 'day', 'light', 'ayu-light'];

const ensureFluxAppearanceHook = () => {
  window.Flux = window.Flux ?? {};

  if (typeof window.Flux.applyAppearance === 'function') {
    return;
  }

  window.Flux.applyAppearance = (mode) => {
    const prefersDark = window.matchMedia?.('(prefers-color-scheme: dark)').matches ?? false;
    const shouldUseDark = mode === 'dark' || (mode === 'system' && prefersDark);

    document.documentElement.classList.toggle('dark', shouldUseDark);
  };
};

ensureFluxAppearanceHook();

const normalizePayload = (detail) => {
  if (!detail) {
    return {};
  }

  if (Array.isArray(detail)) {
    if (detail.length === 1 && typeof detail[0] === 'object') {
      return detail[0];
    }

    const [theme, flavor, accent] = detail;
    return { theme, flavor, accent };
  }

  if (typeof detail === 'object') {
    return detail;
  }

  return {};
};

const applyThemeToDom = ({ theme, flavor, accent, isLight }, preserveExisting = false) => {
  const root = document.documentElement;

  // Handle 'none' theme (system default) - remove data-theme attribute
  if (theme === 'none' || theme === null || theme === undefined) {
    if (!preserveExisting || root.dataset.theme) {
      root.removeAttribute('data-theme');
      root.removeAttribute('data-flavor');
      root.removeAttribute('data-accent');
      // For system default, let OS/browser preference control dark mode
      // Don't force dark class - let prefers-color-scheme handle it
      return;
    }
  }

  // Set theme attributes (preserve existing if preserveExisting is true)
  if (theme !== undefined && theme !== null) {
    if (!preserveExisting || !root.dataset.theme) {
      root.dataset.theme = theme;
    }
  } else if (!preserveExisting && !root.dataset.theme) {
    root.dataset.theme = 'catppuccin';
  }

  if (flavor !== undefined && flavor !== null) {
    if (!preserveExisting || !root.dataset.flavor) {
      root.dataset.flavor = flavor;
    }
  } else if (!preserveExisting && !root.dataset.flavor) {
    root.dataset.flavor = 'mocha';
  }

  if (accent !== undefined && accent !== null) {
    if (!preserveExisting || !root.dataset.accent) {
      root.dataset.accent = accent;
    }
  } else if (!preserveExisting && !root.dataset.accent) {
    root.dataset.accent = 'primary';
  }

  // Update dark class based on isLight property if provided (server-side calculation)
  // Otherwise fall back to flavor-based detection
  if (isLight !== undefined && isLight !== null) {
    if (isLight) {
      root.classList.remove('dark');
    } else {
      root.classList.add('dark');
    }
  } else {
    // Fallback: Always update dark class based on current flavor value
    const currentFlavor = root.dataset.flavor || 'mocha';
    if (lightFlavors.includes(currentFlavor)) {
      root.classList.remove('dark');
    } else {
      root.classList.add('dark');
    }
  }
};

document.addEventListener('DOMContentLoaded', () => {
  // Read existing server-side attributes first (T015: DO NOT overwrite if present)
  const root = document.documentElement;
  const existingTheme = root.dataset.theme;
  const existingFlavor = root.dataset.flavor;
  const existingAccent = root.dataset.accent;

  // Only set defaults if attributes are missing (preserve existing)
  if (!existingTheme || !existingFlavor || !existingAccent) {
    applyThemeToDom(
      {
        theme: existingTheme,
        flavor: existingFlavor,
        accent: existingAccent,
      },
      true,
    ); // preserveExisting = true
  } else {
    // Ensure dark class is set correctly based on existing flavor
    if (lightFlavors.includes(existingFlavor)) {
      root.classList.remove('dark');
    } else {
      root.classList.add('dark');
    }
  }
});

window.__lastThemeEvent = null;
window.__lastJsError = null;
const saveTimers = new Map();
const retryTimers = new Map();
let toastHost = null;

window.addEventListener('theme-updated', (event) => {
  const detail = normalizePayload(event.detail);
  window.__lastThemeEvent = detail;

  // Measure DOM update performance (T027e, FR-101)
  const domStartTime = performance?.now() ?? Date.now();

  applyThemeToDom({
    theme: detail.theme,
    flavor: detail.flavor,
    accent: detail.accent,
    isLight: detail.isLight,
  });

  const domEndTime = performance?.now() ?? Date.now();
  const domUpdateTime = domEndTime - domStartTime;

  // Send performance metrics to server (T027e, FR-101)
  sendPerformanceMetrics('theme_change', domUpdateTime);

  // ARIA live region announcement (T013e, FR-023)
  announceThemeChange(detail);
});

window.__liveThemePreview = (detail) => {
  const normalized = normalizePayload(detail);

  // Measure DOM update performance (T027e, FR-101)
  const domStartTime = performance?.now() ?? Date.now();

  applyThemeToDom(normalized);

  const domEndTime = performance?.now() ?? Date.now();
  const domUpdateTime = domEndTime - domStartTime;

  // Send performance metrics to server (T027e, FR-101)
  sendPerformanceMetrics('theme_preview', domUpdateTime);

  announceThemeChange(normalized);
};

// Store correlation ID for performance tracking (T027e, FR-101)
let performanceCorrelationId = null;

// Listen for correlation ID from Livewire (T027e, FR-101)
window.addEventListener('theme-performance-correlation', (event) => {
  performanceCorrelationId = event.detail?.correlationId || null;
});

/**
 * Send performance metrics to server (T027e, FR-101).
 */
function sendPerformanceMetrics(operation, domUpdateTime) {
  // Send to performance tracking endpoint
  fetch('/themes/performance', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
      'X-Requested-With': 'XMLHttpRequest',
    },
    body: JSON.stringify({
      operation,
      dom_update_time: domUpdateTime,
      correlation_id: performanceCorrelationId, // T027e, FR-101 - for matching server metrics
      timestamp: new Date().toISOString(),
    }),
  }).catch((err) => {
    // Silently fail - performance tracking should not break functionality
    console.debug('Failed to send performance metrics:', err);
  });

  // Clear correlation ID after use
  performanceCorrelationId = null;
}

const announceThemeChange = (detail) => {
  const liveRegion = document.getElementById('theme-announcements');
  if (!liveRegion) {
    return;
  }

  const theme = detail.theme || 'Catppuccin';
  const flavor = detail.flavor || 'Mocha';
  const accent = detail.accent || 'Primary';

  // Format theme name (capitalize first letter)
  const themeName = theme.charAt(0).toUpperCase() + theme.slice(1);
  const flavorName = flavor.charAt(0).toUpperCase() + flavor.slice(1);
  const accentName = accent.charAt(0).toUpperCase() + accent.slice(1);

  // Announce theme change (T013e, FR-023)
  liveRegion.textContent = `Theme changed to ${themeName} ${flavorName} with ${accentName} accent.`;
};

const captureJsError = (payload) => {
  const message = payload.message ?? null;

  if (message && message.includes('CSP Parser Error')) {
    return;
  }

  window.__lastJsError = {
    message,
    filename: payload.filename ?? null,
    lineno: payload.lineno ?? null,
    colno: payload.colno ?? null,
    stack: payload.stack ?? null,
    type: payload.type ?? 'error',
  };
};

window.addEventListener('error', (event) => {
  captureJsError({
    message: event?.message ?? null,
    filename: event?.filename ?? null,
    lineno: event?.lineno ?? null,
    colno: event?.colno ?? null,
    stack: event?.error?.stack ?? null,
    type: 'error',
  });
});

window.addEventListener('unhandledrejection', (event) => {
  const reason = event?.reason;
  const message = typeof reason === 'string' ? reason : (reason?.message ?? null);
  const stack = typeof reason === 'object' && reason ? (reason.stack ?? null) : null;

  captureJsError({
    message,
    filename: null,
    lineno: null,
    colno: null,
    stack,
    type: 'unhandledrejection',
  });
});

const ensureToastHost = () => {
  if (toastHost) {
    return toastHost;
  }

  toastHost = document.createElement('div');
  toastHost.className = 'fixed bottom-6 right-6 z-[9999] flex flex-col items-end gap-2';
  toastHost.setAttribute('aria-live', 'polite');
  toastHost.setAttribute('role', 'status');

  document.body?.appendChild(toastHost);

  return toastHost;
};

const showAppearanceToast = ({ message, variant = 'info', duration = 3000 }) => {
  if (!message) {
    return;
  }

  if (!document.body) {
    window.addEventListener('DOMContentLoaded', () => showAppearanceToast({ message, variant, duration }), { once: true });

    return;
  }

  const host = ensureToastHost();

  if (!host) {
    return;
  }

  const toast = document.createElement('div');
  toast.className = [
    'pointer-events-auto w-72 rounded-lg border px-4 py-3 text-sm shadow-lg transition duration-200',
    variant === 'error' ? 'bg-rose-600/95 text-white border-rose-300' : 'bg-zinc-900/95 text-white border-zinc-700',
  ].join(' ');
  toast.tabIndex = 0;
  toast.textContent = message;

  host.appendChild(toast);

  const timeoutId = window.setTimeout(() => {
    toast.remove();
  }, duration);

  toast.addEventListener('click', () => {
    window.clearTimeout(timeoutId);
    toast.remove();
  });
};

document.addEventListener('keydown', (event) => {
  if (event.key === 'Escape' && toastHost) {
    toastHost.innerHTML = '';
  }
});

document.addEventListener('appearance-save-debounced', (event) => {
  const detail = normalizePayload(event.detail);
  const componentId = detail?.componentId;

  if (!componentId || !window.Livewire) {
    return;
  }

  if (saveTimers.has(componentId)) {
    window.clearTimeout(saveTimers.get(componentId));
  }

  const timeoutId = window.setTimeout(() => {
    window.Livewire.find(componentId)?.call('performSave');
  }, 300);

  saveTimers.set(componentId, timeoutId);
});

document.addEventListener('appearance-save-retry', (event) => {
  const detail = normalizePayload(event.detail);
  const componentId = detail?.componentId;
  const delay = detail?.delayMs ?? 1000;

  if (!componentId || !window.Livewire) {
    return;
  }

  if (retryTimers.has(componentId)) {
    window.clearTimeout(retryTimers.get(componentId));
  }

  const timeoutId = window.setTimeout(() => {
    window.Livewire.find(componentId)?.call('retrySave');
  }, delay);

  retryTimers.set(componentId, timeoutId);
});

document.addEventListener('appearance-toast', (event) => {
  const detail = normalizePayload(event.detail);
  showAppearanceToast({
    message: detail.message,
    variant: detail.variant ?? 'info',
    duration: detail.duration ?? 3000,
  });
});
