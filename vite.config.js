/// <reference types="vitest" />

import { defineConfig } from 'vitest/config';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";
import { glob } from 'glob';
import path from 'path';

// Automatically discover all CSS and JS files
const cssFiles = glob.sync('resources/css/**/*.css').map(file => path.resolve(file));
const jsFiles = glob.sync('resources/js/**/*.js', {
    ignore: ['**/tests/**', '**/*.test.js', '**/*.spec.js']
}).map(file => path.resolve(file));

export default defineConfig({
    plugins: [
        laravel({
            input: [...cssFiles, ...jsFiles],
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
