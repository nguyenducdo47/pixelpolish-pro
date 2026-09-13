import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600, 700],
                }),
            ],
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
        host: process.env.VITE_HMR_HOST ? '0.0.0.0' : undefined,
        port: 5173,
        strictPort: true,
        origin: process.env.VITE_DEV_SERVER_URL || undefined,
        hmr: process.env.VITE_HMR_HOST
            ? { host: process.env.VITE_HMR_HOST }
            : undefined,
        watch: {
            ignored: ['**/storage/framework/views/**'],
            usePolling: process.env.VITE_POLL === '1',
        },
    },
});
