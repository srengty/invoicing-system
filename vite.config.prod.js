import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    base: '/build/',
    // build: {
    //     outDir: './public',
    //     emptyOutDir: false, // also necessary
    // },
    build: {
        manifest: true,
        outDir: 'public/build',
        rollupOptions: {
            input: 'resources/js/app.js',
        },
    },
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
        vue(),
    ],
});
