import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    base: 'https://finance.itc.edu.kh/',
    build: {
        outDir: './public',
        emptyOutDir: false, // also necessary
    },
    server: {
        host: 'https://finance.itc.edu.kh/'
    },
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
            detectTls: 'finance.itc.edu.kh'
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
});
