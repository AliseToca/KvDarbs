import chalk from 'chalk';

export class WatchSpinner {
    private frames = [` `, `‧`, `⊹`, `✴`, `✦`, `*`, `✦`, `✧`, `✴`, `⊹`, `‧`];
    private frameIndex = 0;
    private interval: NodeJS.Timeout | null = null;
    private isVisible = false;
    private lastActivity = Date.now();
    private static exitHandlersSetup = false;
    private static instances = new Set<WatchSpinner>();

    start(): void {
        if (this.interval) return;
        process.stdout.write('\x1b[?25l');
        WatchSpinner.instances.add(this);
        if (process.listenerCount('SIGINT') === 0) {
            WatchSpinner.exitHandlersSetup = false;
        }
        if (!WatchSpinner.exitHandlersSetup) {
            this.setupExitHandlers();
            WatchSpinner.exitHandlersSetup = true;
        }
        this.interceptConsole();
        this.interval = setInterval(() => {
            if (Date.now() - this.lastActivity > 2000) this.show();
            this.frameIndex = (this.frameIndex + 1) % this.frames.length;
        }, 100);
    }

    updateActivity(): void {
        this.lastActivity = Date.now();
    }

    get lastActivityTime(): number {
        return this.lastActivity;
    }

    stop(): void {
        if (!this.interval) return;
        clearInterval(this.interval);
        this.interval = null;
        this.clear();
        WatchSpinner.instances.delete(this);
        process.stdout.write('\x1b[?25h\n');
    }

    private interceptConsole(): void {
        (['log', 'error', 'warn'] as const).forEach((method) => {
            const original = console[method].bind(console);
            const replacement: typeof console[typeof method] = (...args) => {
                this.clear();
                this.updateActivity();
                original(...args);
            };
            console[method] = replacement;
        });
    }

    private setupExitHandlers(): void {
        const restoreAll = () => {
            WatchSpinner.instances.forEach((instance) => {
                instance.clear();
            });
            process.stdout.write('\x1b[?25h');
        };

        const handler = () => {
            restoreAll();
            process.exit(0);
        };

        process.once('SIGINT', handler);
        process.once('SIGTERM', handler);
        process.once('exit', restoreAll);
        process.once('uncaughtException', () => {
            restoreAll();
            process.exit(1);
        });
    }

    clear(): void {
        if (this.isVisible) {
            process.stdout.write('\r\x1b[K');
            this.isVisible = false;
        }
    }

    private show(): void {
        if (!this.isVisible) process.stdout.write('\n');

        process.stdout.write(this.render(this.frames[this.frameIndex]));
        this.isVisible = true;
    }

    private render(frame: string, message?: string): string {
        const display = message && message.trim().length > 0 ? message : ' Watching for file changes...';
        const prefixed = display.startsWith(' ') ? display : ` ${display}`;
        return `\r\x1b[K${chalk.cyan(frame)}${chalk.gray(prefixed)}`;
    }

    public update(frame: string, message: string): void {
        this.clear();
        process.stdout.write(this.render(frame, message));
    }

    private logOutput(frame: string, details: Record<string, unknown>): void {
        const detailMessage = Object.entries(details)
            .map(([key, value]) => `${key}: ${String(value)}`)
            .join(' ');
        process.stdout.write(this.render(frame, detailMessage));
    }
}
