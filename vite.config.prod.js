import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import ziggy from 'laravel-vite-plugin/ziggy';
export default defineConfig({
    server: {
        hmr: {
            host: 'finance.itc.edu.kh',
        },
        https: true,
        cors: {
            origin: [
                'https://finance.itc.edu.kh',
            ],

        },
    },
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
            detectTls: 'finance.itc.edu.kh',
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        ziggy()
    ],
});
