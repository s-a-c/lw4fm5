/** @format */

import { describe, it, beforeEach, expect, vi } from 'vitest';

const createRadio = (id, value) => {
  const input = document.createElement('input');
  input.type = 'radio';
  input.name = 'flavour';
  input.id = id;
  input.value = value;
  return input;
};

const setupDom = () => {
  document.body.innerHTML = '';
  const form = document.createElement('form');
  form.id = 'flavour-switcher';

  const flavors = ['auto', 'latte', 'frappe', 'macchiato', 'mocha'];
  flavors.forEach((flavor) => {
    const radio = createRadio(flavor, flavor === 'auto' ? 'auto' : flavor);
    form.appendChild(radio);
  });

  document.body.appendChild(form);
};

const fireDomReady = () => document.dispatchEvent(new Event('DOMContentLoaded'));

let mediaChangeHandler;
let prefersDark = true;

describe('catppuccin theme switcher', () => {
  beforeEach(() => {
    setupDom();
    document.documentElement.className = '';
    delete document.documentElement.dataset.theme;
    delete document.documentElement.dataset.flavor;
    delete document.documentElement.dataset.accent;
    const storage = (() => {
      let store = {};
      return {
        getItem: (key) => (key in store ? store[key] : null),
        setItem: (key, value) => {
          store[key] = String(value);
        },
        removeItem: (key) => {
          delete store[key];
        },
        clear: () => {
          store = {};
        },
      };
    })();
    vi.stubGlobal('localStorage', storage);
    Object.defineProperty(window, 'localStorage', {
      value: storage,
      configurable: true,
    });
    prefersDark = true;
    mediaChangeHandler = undefined;
    vi.stubGlobal(
      'matchMedia',
      vi.fn().mockImplementation(() => ({
        matches: prefersDark,
        addEventListener: vi.fn((event, handler) => {
          mediaChangeHandler = handler;
        }),
        removeEventListener: vi.fn(),
      })),
    );
    vi.resetModules();
  });

  it('defaults to system preference when auto is selected', async () => {
    await import('../catppuccin.js');

    fireDomReady();

    expect(document.getElementById('auto').checked).toBe(true);
    expect(document.documentElement.className).toBe('mocha');
    expect(localStorage.getItem('theme')).toBe('mocha');
    expect(localStorage.getItem('theme-auto')).toBe('true');
  });

  it('defaults to light variant when system preference is light', async () => {
    prefersDark = false;

    await import('../catppuccin.js');

    fireDomReady();

    expect(document.getElementById('auto').checked).toBe(true);
    expect(document.documentElement.className).toBe('latte');
    expect(localStorage.getItem('theme')).toBe('latte');
    expect(localStorage.getItem('theme-auto')).toBe('true');
  });

  it('respects stored manual theme selection', async () => {
    localStorage.setItem('theme', 'frappe');
    localStorage.setItem('theme-auto', 'false');

    await import('../catppuccin.js');

    fireDomReady();

    expect(document.getElementById('frappe').checked).toBe(true);
    expect(document.documentElement.className).toBe('frappe');
    expect(localStorage.getItem('theme-auto')).toBe('false');
  });

  it('updates localStorage and class when user selects a new theme', async () => {
    await import('../catppuccin.js');

    fireDomReady();

    const latteRadio = document.getElementById('latte');
    latteRadio.checked = true;
    latteRadio.dispatchEvent(new Event('change'));

    expect(localStorage.getItem('theme')).toBe('latte');
    expect(localStorage.getItem('theme-auto')).toBe('false');
    expect(document.documentElement.className).toBe('latte');
  });

  it('reacts to system preference changes when auto mode is enabled', async () => {
    localStorage.setItem('theme-auto', 'true');
    localStorage.setItem('theme', 'mocha');
    prefersDark = true;

    await import('../catppuccin.js');

    fireDomReady();
    expect(typeof mediaChangeHandler).toBe('function');

    mediaChangeHandler?.({ matches: true });
    expect(document.documentElement.className).toBe('mocha');
    mediaChangeHandler?.({ matches: false });

    expect(document.documentElement.className).toBe('latte');
    expect(localStorage.getItem('theme')).toBe('latte');
  });

  it('ignores system changes when auto mode is disabled', async () => {
    localStorage.setItem('theme-auto', 'false');
    localStorage.setItem('theme', 'mocha');

    await import('../catppuccin.js');

    fireDomReady();

    mediaChangeHandler?.({ matches: false });

    expect(document.documentElement.className).toBe('mocha');
  });

  it('ignores change events when no radio button is selected', async () => {
    await import('../catppuccin.js');

    fireDomReady();

    document.querySelectorAll('input[name="flavour"]').forEach((input) => {
      input.checked = false;
    });

    document.getElementById('latte').dispatchEvent(new Event('change'));

    expect(document.documentElement.className).toBe('mocha');
  });

  it('gracefully handles missing default radio button', async () => {
    document.getElementById('auto')?.remove();

    await import('../catppuccin.js');

    expect(() => fireDomReady()).not.toThrow();
  });

  it('does nothing when the flavour switcher form is absent', async () => {
    document.getElementById('flavour-switcher')?.remove();

    await import('../catppuccin.js');

    expect(() => fireDomReady()).not.toThrow();
  });
});
