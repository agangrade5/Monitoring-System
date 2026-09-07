import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import fs from 'fs';
import path from 'path';

function copyBackendImages() {
    return {
        name: 'copy-backend-images',
        closeBundle() {
            const srcDir = path.resolve(__dirname, 'resources/images/backend');
            const destDir = path.resolve(__dirname, 'public/assets/images/backend');

            if (fs.existsSync(srcDir)) {
                fs.mkdirSync(destDir, { recursive: true });
                fs.cpSync(srcDir, destDir, { recursive: true });
                console.log('Backend images copied to', destDir);
            }
        },
    };
}

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/backend/admin.css',
                'resources/js/backend/admin.js',
                'resources/js/backend/image-cropper.js',
                'resources/js/backend/change-password.js',
                'resources/js/backend/monitor.js',
                'resources/js/backend/showmonitor.js',
                'resources/js/backend/user.js',
                'resources/js/backend/activity-logs.js',
            ],
            buildDirectory: 'assets',
            refresh: true,
        }),
        tailwindcss(),
        copyBackendImages(),
    ],

    build: {
        outDir: 'public/assets',
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            output: {
                entryFileNames: (chunkInfo) => {
                    if (chunkInfo.facadeModuleId && chunkInfo.facadeModuleId.endsWith('.css')) {
                        return 'js/backend/admin-style.js';
                    }
                    // chunkInfo.facadeModuleId is set for entry points and dynamic imports
                    return 'js/backend/[name].js';
                },
                chunkFileNames: 'js/backend/[name].js',
                assetFileNames: (assetInfo) => {
                    const name = assetInfo.name?.replace(/\\/g, '/') || '';

                    if (name.endsWith('.css')) {
                        return 'css/backend/[name][extname]';
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
