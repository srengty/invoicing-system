import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    base: '/build/',
    // build: {
    //     outDir: './public',
    //     emptyOutDir: false, // also necessary
    // },

    // server: {
    //     hmr: {
    //         host: 'finance.itc.edu.kh',
    //     },
    //     https: true,
    //     cors: {
    //         origin: [
    //             'https://finance.itc.edu.kh',
    //         ],

    //     },
    // },
    plugins: [
        laravel({
            input: 'resources/js/app.js',
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
});
