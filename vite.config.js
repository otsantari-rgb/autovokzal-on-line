import { defineConfig } from 'vite';
import Inspect from 'vite-plugin-inspect'
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    esbuild: {
        target: 'es6',
    },
    build: {
        sourcemap: true
    },
    plugins: [
        Inspect(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            ssr: 'resources/js/ssr.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {

        },
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        cors: true,
        hmr: {
            host: 'localhost',
        },
    },
});
