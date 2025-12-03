import { defineConfig } from 'vite';
import { eslintPlugin, prettierPlugin, stylelintPlugin, svgSpritePlugin, watcherPlugin, imageCachePlugin } from './vite/plugins';
import { getExtension, mapEntrypoints, customLogger } from './vite/helpers';
import { viteStaticCopy } from 'vite-plugin-static-copy';
import laravel from 'laravel-vite-plugin';
import dotenv from 'dotenv';
import path from 'node:path';
import svgLoader from 'vite-svg-loader';
import vue from '@vitejs/plugin-vue';

const assetPath = `resources/assets`;
const bladePath = `resources/views`;

const jsSource = `${assetPath}/js`;
const scssSource = `${assetPath}/scss`;
const svgSource = `${assetPath}/svg`;

const jsControllers = mapEntrypoints(jsSource, 'controllers', 'js');
const scssControllers = mapEntrypoints(scssSource, 'controllers', 'css');

export default defineConfig({
    appType: 'mpa',
    customLogger: customLogger,
    css: {
        devSourcemap: true,
        transformer: 'postcss',
    },
    plugins: [
        {
            name: 'env-config',
            configResolved() {
                dotenv.config({ quiet: true });
            }
        },
        vue(),
        laravel({
            input: [
                'resources/js/app.js',
                'resources/scss/style.scss',

            ],
            buildDirectory: 'front',
            refresh: true,
        }),
        watcherPlugin({
            directories: [jsSource, scssSource, svgSource, bladePath],
        }),
        imageCachePlugin({
            sourceDir: 'resources/assets/images',
            cacheDir: 'storage/app/public/assets',
            clearOnBuild: true, // Clear all cache on build start
        }),
        prettierPlugin({
            directories: [assetPath, bladePath],
            extensions: ['js', 'ts', 'css', 'scss', 'vue', 'json', 'php'],
            write: true,
            validate: true,
        }),
        svgSpritePlugin({
            svgSource,
            inputDir: 'sprite-icons',
            dontStripColor: ['color-*.svg'],
        }),
        eslintPlugin({
            include: ['resources/assets/js/**/*.{js,ts}'],
            failOnError: true,
            lintAllOnSave: true,
        }),
        stylelintPlugin({
            include: ['resources/assets/scss/**/*.{scss,css}'],
            failOnError: true,
            lintAllOnSave: true,
        }),
        viteStaticCopy({
            silent: process.env.NODE_ENV == 'development',
            targets: [
                // {
                //     src: 'resources/assets/images/*',
                //     dest: 'images',
                // },
                {
                    src: 'resources/assets/svg/*',
                    dest: 'svg',
                },
            ],
        }),
        svgLoader({
            svgo: false,
        }),
    ],
    resolve: {
        alias: {
            '@root': path.resolve(__dirname, `${jsSource}`) + '/',
            '@controllers': path.resolve(__dirname, `${jsSource}/controllers`),
            '@helpers': path.resolve(__dirname, `${jsSource}/helpers`),
            '@definitions': path.resolve(__dirname, `${jsSource}/definitions`),
            '@modules': path.resolve(__dirname, `${jsSource}/modules`),
            '@utilities': path.resolve(__dirname, `${jsSource}/utilities`),
            '@scss': path.resolve(__dirname, `${scssSource}`) + '/',
            '@environment': path.resolve(__dirname, `${scssSource}/environment`),
        },
    },
    build: {
        sourcemap: false,
        rollupOptions: {
            input: {
                admin: `resources/css/filament/admin/theme.css`,
                abovethefold: `${scssSource}/abovethefold.scss`,
                style: `${scssSource}/style.scss`,
                fonts: `${scssSource}/fonts.scss`,
                app: `${jsSource}/app.ts`,
                sprite: `${svgSource}/sprite.svg?asset`,
                ...jsControllers,
                ...scssControllers,
            },
            output: {
                dir: 'public/front',
                assetFileNames: (assetInfo) => {
                    const ext = getExtension(assetInfo);
                    const name = assetInfo?.name ?? '';

                    if (name.startsWith('css/')) {
                        return `[name].[hash].css`;
                    }

                    if (/(css)$/.test(ext)) {
                        return 'css/[name].[hash][extname]';
                    }

                    if (/(woff|woff2|ttf)$/.test(ext)) {
                        return 'fonts/[name][extname]';
                    }

                    if (name.endsWith('sprite.svg')) {
                        return 'svg/[name].[hash][extname]';
                    }

                    if (/(svg)$/.test(ext)) {
                        return 'svg/[name].[hash][extname]';
                    }

                    return `[extname]/[name].[hash][extname]`;
                },
                entryFileNames: (chunkInfo) => {
                    const name = chunkInfo?.name ?? '';

                    if (name.startsWith('js/')) {
                        return `[name].[hash].js`;
                    }

                    return `js/[name].[hash].js`;
                },
                chunkFileNames: 'chunks/[name].[hash].js',
                globals: {},
                // manualChunks: (id) => {
                //     if (id.includes('node_modules')) {
                //         return 'vendor';
                //     }
                // },
            },
        },
    },
});
