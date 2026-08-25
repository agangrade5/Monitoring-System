import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/backend/admin.css',
                'resources/js/backend/admin.js',
                'resources/images/backend/user1-128x128.jpg',
                'resources/images/backend/user2-160x160.jpg',
                'resources/images/backend/user3-128x128.jpg',
                'resources/images/backend/user8-128x128.jpg',
            ],
            refresh: true,
        }),

        tailwindcss(),
    ],

    build: {
        outDir: 'public/assets',
        emptyOutDir: false,

        rollupOptions: {
            output: {
                entryFileNames: 'js/[name].js',

                chunkFileNames: 'js/[name].js',

                assetFileNames: (assetInfo) => {
                    const name = assetInfo.name?.replace(/\\/g, '/') || '';

                    if (name.endsWith('.css')) {
                        return 'css/backend/[name][extname]';
                    }

                    if (/\.(png|jpe?g|gif|svg|webp|avif)$/i.test(name)) {
                        return 'images/backend/[name][extname]';
                    }

                    if (/\.(woff2?|ttf|otf|eot)$/i.test(name)) {
                        return 'fonts/backend/[name][extname]';
                    }

                    return 'assets/backend/[name][extname]';
                },
            },
        },
    },
});
