import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                api: 'modern-compiler',
                // Removed 'mixed-decls' from this list
                silenceDeprecations: ['color-functions', 'global-builtin', 'import', 'legacy-js-api'],
            },
        },
    },
    server: {
        host: '127.0.0.1',
        port: 5173,
    },
});
