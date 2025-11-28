import { debugLog, watchMode } from './plugin-utils';
import path from 'node:path';
import type { Plugin } from 'vite';
import { ViteWatcher, getViteWatcher } from './watcher';
export { getViteWatcher, ViteWatcher, type FileEvent, type FileCallback } from './vite-watcher';
export { WatchSpinner } from './vite-watcher/spinner';

export function watcherPlugin(options: { debug?: boolean; directories?: string[] } = {}): Plugin {
    const { debug = false, directories = ['resources'] } = options;

    return {
        name: 'vite-watcher',
        apply: 'serve',
        enforce: 'pre',

        configResolved() {
            ViteWatcher.reset();
        },

        buildStart() {
            if (watchMode) {
                getViteWatcher().init({ debug, directories });

                if (debug) {
                    debugLog('watcherPlugin buildStart in watch mode', { directories }, true);
                }

                const relativeDirs = getViteWatcher()
                    .getWatchedDirectories()
                    .map((dir) => path.relative(process.cwd(), dir));
                for (let i = 0; i < relativeDirs.length; i++) {
                    relativeDirs[i] = `./${relativeDirs[i]}/`;
                }
            }
        },

        handleHotUpdate({ file, server }) {
            if (file.endsWith('.blade.php')) {
                server.ws.send({
                    type: 'full-reload',
                    path: '*',
                });
            }
        },

        buildEnd() {
            ViteWatcher.reset();
        },
    };
}
