import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { fileURLToPath, URL } from 'url';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
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
        alias: [
            { find: '@', replacement: fileURLToPath(new URL("./resources/js", import.meta.url)) },
            { find: '@stores', replacement: fileURLToPath(new URL("./resources/js/stores", import.meta.url)) },
            { find: 'vue', replacement: 'vue/dist/vue.esm-bundler.js' }
        ],
    },
});
