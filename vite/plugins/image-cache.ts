import type { Plugin } from 'vite';
import { statSync } from 'fs';
import { unlink, rm } from 'fs/promises';
import { glob } from 'glob';
import path from 'path';
import { existsSync } from 'fs';
import chalk from 'chalk';
import { debugLog, logError, statusLine, watchMode } from './plugin-utils';
import { getViteWatcher } from './vite-watcher';

interface ImageCacheOptions {
    /**
     * Directory containing source images
     * @default 'resources/assets/images'
     */
    sourceDir?: string;

    /**
     * Directory containing processed/cached images
     * @default 'storage/app/public/assets'
     */
    cacheDir?: string;

    /**
     * Image file extensions to watch
     * @default ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']
     */
    extensions?: string[];

    /**
     * Clear all cache on build start
     * @default true
     */
    clearOnBuild?: boolean;

    /**
     * Maximum allowed timestamp drift (ms) between source image and cached variant
     * before the variant is considered stale.
     * @default 10000
     */
    timestampToleranceMs?: number;

    /**
     * Limit for concurrent delete operations when purging stale variants.
     * Helps avoid overwhelming file system on large batches.
     * @default 8
     */
    maxConcurrentDeletes?: number;

    /**
     * Enable debug logging
     * @default false
     */
    debug?: boolean;
}

interface InternalConfig {
    sourceDir: string;
    cacheDir: string;
    extensions: string[];
    clearOnBuild: boolean;
    timestampToleranceMs: number;
    maxConcurrentDeletes: number;
    debug: boolean;
}

class ImageCacheManager {
    private readonly config: InternalConfig;
    private readonly debug: boolean;
    public clearedCount: number;
    public processed: boolean;

    constructor(options: ImageCacheOptions) {
        this.config = {
            sourceDir: options.sourceDir ?? 'resources/assets/images',
            cacheDir: options.cacheDir ?? 'storage/app/public/assets',
            extensions: options.extensions ?? ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'],
            clearOnBuild: options.clearOnBuild ?? true,
            timestampToleranceMs: options.timestampToleranceMs ?? 10000,
            maxConcurrentDeletes: options.maxConcurrentDeletes ?? 8,
            debug: options.debug ?? false,
        };

        this.debug = this.config.debug;
        this.clearedCount = 0;
        this.processed = false;
    }

    private showStatusMessage(message: string): void {
        if (!watchMode || statusLine.isInitialBuildActive) {
            statusLine.replaceToolLine('ImageCache', message);
        } else {
            statusLine.finish(message);
        }
    }

    private getCachePathForImage(filename: string): string {
        const basename = path.basename(filename, path.extname(filename));
        const dirname = path.dirname(filename);

        return dirname === '.'
            ? path.join(this.config.cacheDir, basename)
            : path.join(this.config.cacheDir, dirname, basename);
    }

    private async findCachedVariants(cacheDir: string): Promise<string[]> {
        try {
            const cachePattern = path.join(cacheDir, '*');
            return await glob(cachePattern);
        } catch (error) {
            debugLog('ImageCache: error finding cached variants', error, this.debug);
            return [];
        }
    }

    private isOutsideSource(absoluteSourceDir: string, absolutePath: string): boolean {
        const relative = path.relative(absoluteSourceDir, absolutePath);
        return relative.startsWith('..') || path.isAbsolute(relative);
    }

    private variantIsStale(sourceTimestamp: number, cachedTimestamp: number): boolean {
        return Math.abs(sourceTimestamp - cachedTimestamp) > this.config.timestampToleranceMs;
    }

    private async deleteFilesWithLimit(files: string[]): Promise<void> {
        const limit = Math.max(1, this.config.maxConcurrentDeletes);
        let index = 0;
        const workers: Promise<void>[] = [];

        const run = async () => {
            while (index < files.length) {
                const file = files[index++];
                try {
                    await unlink(file);
                } catch (err) {
                    const errorMessage = err instanceof Error ? err.message : String(err);
                    debugLog('ImageCache: error deleting file', { file, error: errorMessage }, this.debug);
                }
            }
        };

        for (let i = 0; i < limit; i++) {
            workers.push(run());
        }
        await Promise.all(workers);
    }

    public showClearMessage(): void {
        if (!this.processed) return;

        const message = this.clearedCount === 0
            ? chalk.green('✓ ') + 'ImageCache: no stale cache found'
            : chalk.green('✓ ') + `ImageCache: invalidated ${this.clearedCount} variant${this.clearedCount !== 1 ? 's' : ''}`;

        this.showStatusMessage(message);
        this.processed = false;
    }

    public async clearCache(): Promise<void> {
        this.processed = true;

        try {
            if (existsSync(this.config.cacheDir)) {
                await rm(this.config.cacheDir, { recursive: true, force: true });
                this.clearedCount = 1;
                debugLog('ImageCache: cache directory cleared', { cacheDir: this.config.cacheDir }, this.debug);
            } else {
                this.clearedCount = 0;
                debugLog('ImageCache: no cache directory found', { cacheDir: this.config.cacheDir }, this.debug);
            }
        } catch (error) {
            logError('ImageCache: failed to clear cache', error, this.debug);
            const message = chalk.red('✗ ') + `ImageCache: failed (${error})`;
            this.showStatusMessage(message);
            throw error;
        }
    }

    public async invalidateStaleCache(): Promise<void> {
        this.processed = true;
        this.clearedCount = 0;

        try {
            const absoluteSourceDir = path.resolve(process.cwd(), this.config.sourceDir);

            if (!existsSync(absoluteSourceDir)) {
                debugLog('ImageCache: source directory does not exist', { sourceDir: absoluteSourceDir }, this.debug);
                return;
            }

            // Find all source images
            const imagePattern = path.join(absoluteSourceDir, `**/*.{${this.config.extensions.join(',')}}`);
            const sourceImages = await glob(imagePattern);

            debugLog('ImageCache: found source images', { count: sourceImages.length }, this.debug);

            if (sourceImages.length === 0) {
                return;
            }

            let totalInvalidated = 0;

            for (const absoluteFilePath of sourceImages) {
                try {
                    const filename = path.relative(this.config.sourceDir, absoluteFilePath);
                    const cacheDir = this.getCachePathForImage(filename);
                    const cachedFiles = await this.findCachedVariants(cacheDir);

                    if (cachedFiles.length === 0) {
                        continue;
                    }

                    const sourceStats = statSync(absoluteFilePath);
                    const sourceTimestamp = sourceStats.mtimeMs;

                    const filesToDelete: string[] = [];
                    for (const cachedFile of cachedFiles) {
                        try {
                            const cachedStats = statSync(cachedFile);
                            const cachedTimestamp = cachedStats.mtimeMs;
                            if (this.variantIsStale(sourceTimestamp, cachedTimestamp)) {
                                filesToDelete.push(cachedFile);
                            }
                        } catch (err) {
                            const errorMessage = err instanceof Error ? err.message : String(err);
                            debugLog('ImageCache: error checking cached file', { cachedFile, error: errorMessage }, this.debug);
                            continue;
                        }
                    }

                    if (filesToDelete.length > 0) {
                        await this.deleteFilesWithLimit(filesToDelete);

                        totalInvalidated += filesToDelete.length;
                        debugLog('ImageCache: invalidated cache variants', { filename, count: filesToDelete.length }, this.debug);
                    }
                } catch (error: unknown) {
                    const errorMessage = error instanceof Error ? error.message : String(error);
                    debugLog('ImageCache: error processing image', { file: absoluteFilePath, error: errorMessage }, this.debug);
                    continue;
                }
            }

            this.clearedCount = totalInvalidated;
            debugLog('ImageCache: completed stale cache check', { totalInvalidated, totalImages: sourceImages.length }, this.debug);
        } catch (error: unknown) {
            const errorMessage = error instanceof Error ? error.message : String(error);
            logError('ImageCache: stale cache check failed', { error: errorMessage }, this.debug);
            const message = chalk.red('✗ ') + `ImageCache: failed (${errorMessage})`;
            this.showStatusMessage(message);
            throw error;
        }
    }

    public async invalidateCache(filePath: string): Promise<void> {
        try {
            // Normalize and validate path - prevent directory traversal
            const normalizedPath = path.normalize(filePath);
            const absoluteSourceDir = path.resolve(process.cwd(), this.config.sourceDir);
            const absoluteFilePath = path.resolve(process.cwd(), normalizedPath);

            if (this.isOutsideSource(absoluteSourceDir, absoluteFilePath)) {
                debugLog('ImageCache: path outside source directory', { filePath }, this.debug);
                return;
            }

            if (!existsSync(absoluteFilePath)) {
                debugLog('ImageCache: source file does not exist', { filePath }, this.debug);
                return;
            }

            const filename = path.relative(this.config.sourceDir, normalizedPath);
            const cacheDir = this.getCachePathForImage(filename);
            const cachedFiles = await this.findCachedVariants(cacheDir);

            if (cachedFiles.length === 0) {
                debugLog('ImageCache: no cached variants found', { filename }, this.debug);
                return;
            }

            const sourceStats = statSync(absoluteFilePath);
            const sourceTimestamp = sourceStats.mtimeMs;

            const filesToDelete: string[] = [];
            for (const cachedFile of cachedFiles) {
                try {
                    const cachedStats = statSync(cachedFile);
                    const cachedTimestamp = cachedStats.mtimeMs;
                    if (this.variantIsStale(sourceTimestamp, cachedTimestamp)) {
                        filesToDelete.push(cachedFile);
                    }
                } catch (err) {
                    const errorMessage = err instanceof Error ? err.message : String(err);
                    debugLog('ImageCache: error checking cached file', { cachedFile, error: errorMessage }, this.debug);
                    continue;
                }
            }

            if (filesToDelete.length > 0) {
                await this.deleteFilesWithLimit(filesToDelete);

                const message = chalk.green('✓ ') + `ImageCache: cleared ${filesToDelete.length} variants for ${chalk.gray(`./${filename}`)}`;
                statusLine.finish(message);
                debugLog('ImageCache: invalidated cache', { filename, count: filesToDelete.length }, this.debug);
            }
        } catch (error: unknown) {
            const errorMessage = error instanceof Error ? error.message : String(error);
            logError('ImageCache: cache invalidation failed', { error: errorMessage }, this.debug);
        }
    }

    public shouldProcessFile(filePath: string): boolean {
        if (!filePath.startsWith(this.config.sourceDir)) {
            return false;
        }

        const ext = path.extname(filePath).slice(1).toLowerCase();
        return this.config.extensions.includes(ext);
    }

    public getSourceDir(): string {
        return this.config.sourceDir;
    }

    public isClearOnBuildEnabled(): boolean {
        return this.config.clearOnBuild;
    }
}

let initialClearTimeout: NodeJS.Timeout | null = null;
const cacheTimeouts = new Map<string, NodeJS.Timeout>();

export default function imageCache(options: ImageCacheOptions = {}): Plugin {
    const manager = new ImageCacheManager(options);
    const debug = options.debug ?? false;
    let isInitialRun = true;

    return {
        name: 'vite-plugin-image-cache',
        enforce: 'pre',

        async buildStart() {
            debugLog('ImageCache plugin buildStart', { clearOnBuild: manager.isClearOnBuildEnabled(), watchMode }, debug);

            if (!manager.isClearOnBuildEnabled()) return;

            if (watchMode) {
                if (isInitialRun) {
                    clearTimeout(initialClearTimeout);
                    initialClearTimeout = setTimeout(async () => {
                        try {
                            statusLine.show(chalk.cyan('→ Running ImageCache...'));
                            await manager.invalidateStaleCache();
                            manager.showClearMessage();
                        } finally {
                            initialClearTimeout = null;
                            isInitialRun = false;
                        }
                    }, 300);
                }

                debugLog('ImageCache: setting up file watcher', { sourceDir: manager.getSourceDir() }, debug);

                // Set up file watcher for image changes
                getViteWatcher().addCallback(async (event) => {
                    if (!manager.shouldProcessFile(event.file)) {
                        debugLog('ImageCache: file skipped', { file: event.file }, debug);
                        return;
                    }

                    debugLog('ImageCache: file change detected', { file: event.file }, debug);

                    // Clear existing timeout for this specific file
                    const existingTimeout = cacheTimeouts.get(event.file);
                    if (existingTimeout) {
                        clearTimeout(existingTimeout);
                    }

                    // Set new timeout for this file
                    const timeout = setTimeout(async () => {
                        try {
                            await manager.invalidateCache(event.file);
                        } finally {
                            cacheTimeouts.delete(event.file);
                        }
                    }, 300);

                    cacheTimeouts.set(event.file, timeout);
                });
            } else {
                // Build mode - check all images and invalidate stale cache
                statusLine.show(chalk.cyan('→ Running ImageCache...'));
                await manager.invalidateStaleCache();
                manager.showClearMessage();
            }
        },

        buildEnd() {
            debugLog('ImageCache plugin buildEnd', undefined, debug);

            // Clear all pending timeouts
            if (initialClearTimeout) {
                clearTimeout(initialClearTimeout);
                initialClearTimeout = null;
            }

            cacheTimeouts.forEach(timeout => clearTimeout(timeout));
            cacheTimeouts.clear();

            isInitialRun = true;
        }
    };
}