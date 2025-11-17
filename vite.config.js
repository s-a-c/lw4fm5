/// <reference types="vitest" />

import { defineConfig } from 'vitest/config'; // <-- Change this import
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
    },
    // ------------------------------------

    // Optional: Add this if you want '@' to work in tests
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
});
