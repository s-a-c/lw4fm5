/** @format */

import { describe, it, expect, beforeEach, vi } from 'vitest';

const dispatchDomContentLoaded = () => {
  document.dispatchEvent(new Event('DOMContentLoaded'));
};

describe('app.js theme initialization', () => {
  beforeEach(() => {
    document.documentElement.dataset.theme = '';
    document.documentElement.dataset.flavor = '';
    document.documentElement.dataset.accent = '';
    document.documentElement.className = '';
    vi.resetModules();
  });

  it('applies default dataset values and dark class', async () => {
    await import('../app.js');

    dispatchDomContentLoaded();

    const root = document.documentElement;
    expect(root.dataset.theme).toBe('catppuccin');
    expect(root.dataset.flavor).toBe('mocha');
    expect(root.dataset.accent).toBe('primary');
    expect(root.classList.contains('dark')).toBe(true);
  });

  it('removes dark class for light flavors', async () => {
    const root = document.documentElement;
    root.dataset.flavor = 'latte';

    await import('../app.js');

    dispatchDomContentLoaded();

    expect(root.classList.contains('dark')).toBe(false);
  });
});
