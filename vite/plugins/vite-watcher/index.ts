import { debugLog, statusLine } from '../plugin-utils';
import { WatchSpinner } from './spinner';
import { FSWatcher, watch } from 'node:fs';
import fs from 'node:fs';
import path from 'node:path';
import chalk from 'chalk';

export interface FileEvent {
    type: 'change' | 'rename';
    action: 'created' | 'modified' | 'deleted';
    file: string;
    path: string;
}

export type FileCallback = (event: FileEvent) => void;

export class ViteWatcher {
    private static instance: ViteWatcher | null = null;
    private watchers: Map<string, FSWatcher> = new Map<string, FSWatcher>();
    private callbacks: FileCallback[] = [];
    private watchedDirectories: string[] = [];
    private debug = false;
    private spinner = new WatchSpinner();
    private pendingChanges: FileEvent[] = [];
    private batchTimeout: NodeJS.Timeout | null = null;
    private loggingEnabled = false;
    private fileStates = new Map<string, boolean>(); // Track file existence
    private suppressedFiles = new Map<string, number>(); // Track files to suppress for a short period
    private ignoredPatterns = [
        /\.tmp\./,
        /~$/,
        /\.swp$/,
        /\.swo$/,
        /\.DS_Store$/,
        /\.git\//,
        /node_modules\//,
        /\.vscode\//,
        /\.idea\//,
        /thumbs\.db$/i,
        /\.lock$/,
        /package-lock\.json$/,
        /\.log$/,
        /\.cache\//,
        /vendor\//,
        /\/sprite.svg$/,
    ];

    static getInstance(directories?: string[]): ViteWatcher {
        if (!this.instance) this.instance = new ViteWatcher();
        if (directories) {
            this.instance.updateDirectories(directories);
        }
        return this.instance;
    }

    init(options: { debug?: boolean; directories?: string[] } = {}): void {
        const { debug, directories } = options;

        if (typeof debug === 'boolean') {
            this.setDebug(debug);
        }

        if (directories && directories.length) {
            this.updateDirectories(directories);
        }

        debugLog(
            'ViteWatcher initialized',
            {
                debug: this.debug,
                directories: this.watchedDirectories,
                activeWatchers: this.watchers.size,
            },
            this.debug
        );
    }

    setDebug(enabled: boolean): void {
        this.debug = enabled;
    }

    addCallback(callback: FileCallback): void {
        this.callbacks.push(callback);

        if (this.watchers.size === 0) {
            this.startWatching();
        }
    }

    suppressFileChanges(filePath: string, durationMs = 200): void {
        const normalizedPath = path.relative(process.cwd(), filePath);
        this.suppressedFiles.set(normalizedPath, Date.now() + durationMs);
    }

    updateDirectories(directories: string[]): void {
        const normalizedDirs = directories.map((dir) => path.resolve(dir));

        const currentDirs = Array.from(this.watchers.keys()).sort();
        const newDirs = normalizedDirs.sort();

        if (JSON.stringify(currentDirs) === JSON.stringify(newDirs)) {
            return;
        }

        this.watchedDirectories = directories;

        if (this.callbacks.length > 0) {
            this.stopWatching();
            this.startWatching();
        }
    }

    startWatching(): void {
        const projectRoot = process.cwd();

        this.loggingEnabled = false;

        for (const directory of this.watchedDirectories) {
            const dir = path.resolve(directory);

            if (!fs.existsSync(dir)) {
                debugLog(`Directory does not exist: ${dir}`, {}, this.debug);
                continue;
            }

            const stat = fs.lstatSync(dir);
            if (!stat.isDirectory()) {
                debugLog(`Path is not a directory: ${dir}`, {}, this.debug);
                continue;
            }

            if (this.watchers.has(dir)) continue;

            try {
                debugLog(`Starting to watch: ${path.relative(projectRoot, dir)}`, {}, this.debug);

                const watcher = watch(dir, { recursive: true }, (eventType, filename) => {
                    if (filename) {
                        const absolutePath = path.join(dir, filename);
                        const relativeToProject = path.relative(projectRoot, absolutePath);

                        const event: FileEvent = {
                            type: eventType as 'change' | 'rename',
                            action: this.determineFileAction(absolutePath, eventType),
                            file: relativeToProject,
                            path: absolutePath,
                        };

                        if (!statusLine.isInitialBuildActive) {
                            this.loggingEnabled = true;
                        }

                        this.processBatchedChanges(event);
                    }
                });

                this.watchers.set(dir, watcher);

                watcher.on('error', (error) => {
                    debugLog(`File watcher error for ${dir}`, { error }, this.debug);
                    this.watchers.delete(dir);
                });
            } catch (error) {
                debugLog(`Failed to watch directory: ${dir}`, { error }, this.debug);
            }
        }

        if (this.watchers.size === 0) {
            debugLog('Warning: No directories are being watched', {}, this.debug);
        } else {
            debugLog(
                `File watcher active for ${this.watchers.size} director${this.watchers.size === 1 ? 'y' : 'ies'}`,
                {},
                this.debug
            );

            if (!statusLine.isInitialBuildActive) {
                this.spinner.start();
            }
        }
    }

    getWatchedDirectories(): string[] {
        return Array.from(this.watchers.keys());
    }

    startSpinner(): void {
        if (this.watchers.size > 0) {
            this.spinner.start();
        }
    }

    cleanup(): void {
        this.spinner.stop();
        this.stopWatching();
        this.callbacks.length = 0;
        this.watchedDirectories.length = 0;
        this.pendingChanges.length = 0;
        this.fileStates.clear();
        this.suppressedFiles.clear();
        if (this.batchTimeout) {
            clearTimeout(this.batchTimeout);
            this.batchTimeout = null;
        }
        this.loggingEnabled = false;
    }

    static reset(): void {
        if (this.instance) {
            this.instance.cleanup();
            this.instance = null;
        }
    }

    private stopWatching(): void {
        this.spinner.stop();

        for (const [dir, watcher] of this.watchers.entries()) {
            try {
                watcher.close();
            } catch (error) {
                debugLog(`Error closing watcher for ${dir}`, { error }, this.debug);
            }
        }
        this.watchers.clear();
    }

    private determineFileAction(absolutePath: string, eventType: string): 'created' | 'modified' | 'deleted' {
        const fileExists = fs.existsSync(absolutePath);
        const wasTracked = this.fileStates.has(absolutePath);
        const existedBefore = this.fileStates.get(absolutePath) || false;

        if (eventType === 'change') {
            // File content changed
            this.fileStates.set(absolutePath, true);
            return 'modified';
        }

        if (eventType === 'rename') {
            if (fileExists) {
                if (!wasTracked || !existedBefore) {
                    // File was created
                    this.fileStates.set(absolutePath, true);
                    return 'created';
                } else {
                    // File was renamed/moved but still exists
                    this.fileStates.set(absolutePath, true);
                    return 'modified';
                }
            } else {
                // File was deleted or renamed away
                this.fileStates.set(absolutePath, false);
                return 'deleted';
            }
        }

        // Fallback
        this.fileStates.set(absolutePath, fileExists);
        return fileExists ? 'modified' : 'deleted';
    }

    private processBatchedChanges(event: FileEvent): void {
        this.pendingChanges.push(event);

        if (this.pendingChanges.length === 0) return;

        // Clear existing batch timeout
        clearTimeout(this.batchTimeout);

        // Set new batch timeout
        this.batchTimeout = setTimeout(
            () => {
                // Deduplicate changes by file path, keeping the latest event for each file
                const uniqueChanges = new Map<string, FileEvent>();
                this.pendingChanges.forEach((event) => {
                    uniqueChanges.set(event.file, event);
                });

                const deduplicatedChanges = Array.from(uniqueChanges.values());

                const now = Date.now();

                const isSuppressed = (filePath: string): boolean => {
                    const suppressedUntil = this.suppressedFiles.get(filePath);
                    if (suppressedUntil && now < suppressedUntil) {
                        return true;
                    }
                    if (suppressedUntil) {
                        this.suppressedFiles.delete(filePath);
                    }
                    return false;
                };

                const visibleChanges = deduplicatedChanges.filter((event) => {
                    if (this.ignoredPatterns.some((pattern) => pattern.test(event.file))) {
                        return false;
                    }
                    if (isSuppressed(event.file)) {
                        return false;
                    }
                    return true;
                });

                if (visibleChanges.length > 0 && this.loggingEnabled) {
                    this.logFileChanges(visibleChanges);
                }

                deduplicatedChanges.forEach((event) => {
                    if (isSuppressed(event.file)) {
                        return;
                    }

                    this.callbacks.forEach((callback) => callback(event));
                });

                // Clear pending changes
                this.pendingChanges.length = 0;
                this.batchTimeout = null;
            },
            this.pendingChanges.length > 1 ? 200 : 0
        );
    }

    private logFileChanges(changes: FileEvent[]): void {
        console.clear();
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', {
            hour12: true,
            hour: 'numeric',
            minute: '2-digit',
            second: '2-digit',
        });

        this.spinner.updateActivity();

        // Clear the current line (removes "Watching for file changes..." message)
        process.stdout.write('\r\x1b[K');

        if (changes.length === 1) {
            // Single file change - use action-specific format
            const event = changes[0];
            const actionText = this.getActionText(event.action);
            const actionColor = this.getActionColor(event.action);

            const message = `${chalk.gray(timeString)} ${actionColor(`⟡ ${actionText}: `)}${chalk.gray(`./${event.file}`)}`;
            statusLine.finish(message);
        } else {
            // Multiple file changes - group by action
            const grouped = this.groupChangesByAction(changes);

            Object.entries(grouped).forEach(([action, files]) => {
                const actionText = this.getActionText(action as 'created' | 'modified' | 'deleted');
                const actionColor = this.getActionColor(action as 'created' | 'modified' | 'deleted');

                if (files.length === 1) {
                    const message = `${chalk.gray(timeString)} ${actionColor(`⟡ ${actionText}: `)}${chalk.gray(`./${files[0].file}`)}`;
                    statusLine.finish(message);
                } else {
                    const message = `${chalk.gray(timeString)} ${actionColor(`⟡ ${files.length} files ${action}: `)}`;
                    statusLine.finish(message);
                    files.forEach((event) => {
                        const message = `  ${chalk.gray(`./${event.file}`)}`;
                        statusLine.finish(message);
                    });
                }
            });
        }
    }

    private getActionText(action: 'created' | 'modified' | 'deleted'): string {
        switch (action) {
            case 'created':
                return 'File created';
            case 'modified':
                return 'File modified';
            case 'deleted':
                return 'File deleted';
        }
    }

    private getActionColor(action: 'created' | 'modified' | 'deleted') {
        switch (action) {
            case 'created':
                return chalk.green;
            case 'modified':
                return chalk.cyan;
            case 'deleted':
                return chalk.red;
        }
    }

    private groupChangesByAction(changes: FileEvent[]): Record<string, FileEvent[]> {
        const grouped: Record<string, FileEvent[]> = {};

        changes.forEach((event) => {
            if (!grouped[event.action]) {
                grouped[event.action] = [];
            }
            grouped[event.action].push(event);
        });

        return grouped;
    }
}

export const getViteWatcher = () => ViteWatcher.getInstance();
