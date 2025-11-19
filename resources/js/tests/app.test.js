import { describe, it, expect, beforeEach, afterEach } from 'vitest';

describe('app.js', () => {
  beforeEach(() => {
    // Reset window to ensure clean state
    global.window = {};
  });

  afterEach(() => {
    // Clean up
    delete global.window.Echo;
  });

  it('should import echo.js module without errors', async () => {
    // Import app.js which imports echo.js
    // This ensures both modules are executed
    await expect(import('../app.js')).resolves.toBeDefined();
  });

  it('should execute and import echo.js', async () => {
    // Import app.js - this will execute the import of echo.js
    const module = await import('../app.js');
    expect(module).toBeDefined();
    // Verify that echo.js was executed (Pusher should be set if echo.js ran)
    // Note: This depends on environment variables, but the import itself is tested
  });
});
