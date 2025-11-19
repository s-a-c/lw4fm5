/// <reference types="vitest" />

import { defineConfig } from 'vitest/config';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
    },

    // --- Add this block for Vitest ---
    test: {
        globals: true,
        environment: 'jsdom', // Use jsdom for Alpine.js testing
        include: ['resources/**/*.{test,spec}.{js,ts,jsx,tsx}'],
        exclude: [
            '**/node_modules/**',
            '**/dist/**',
            '**/vendor/**',
            '**/.*/**',
        ],
        testTimeout: 60000, // 60 second timeout per test
        hookTimeout: 60000, // 60 second timeout for hooks
        coverage: {
            provider: 'v8',
            include: ['resources/**/*.{js,ts,jsx,tsx}'],
            exclude: [
                '**/node_modules/**',
                '**/dist/**',
                '**/vendor/**',
                '**/.*/**',
                ],
            reportsDirectory: 'resources/js/tests/coverage',
        },
    },
    // ------------------------------------

    // Optional: Add this if you want '@' to work in tests
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    // Replace import.meta.env with process.env in tests
    define: {
        'import.meta.env': 'process.env',
    },
});
