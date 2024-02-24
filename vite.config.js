import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import vuetify from 'vite-plugin-vuetify';
import mkcert from'vite-plugin-mkcert';

export default defineConfig({
    server: {
        host: "0.0.0.0",
        port: 4444,
        https: true,
        hmr: {
            host: 'localhost',
            port: 4444,
            },
    },
    plugins: [
        mkcert(),
        laravel({
            input: ['resources/css/app.css',
                    'resources/js/app.js'],
            refresh: true,
        }),
        vue({
        resolve: name => {
            const pages = import.meta.glob('resources/js/Pages/**/*.vue', { eager: true })
            return pages[`resources/js/Pages/${name}.vue`]
          }
        }),
        vuetify({ autoImport: true }),  
    ],
});