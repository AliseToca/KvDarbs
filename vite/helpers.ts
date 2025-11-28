import { createLogger, type Logger } from 'vite';
import { normalizePath } from 'vite';
import chalk from 'chalk';
import fs from 'node:fs';
import path from 'node:path';

// eslint-disable-next-line no-control-regex -- Required to strip ANSI color escape sequences from log output
const ANSI_COLOR_SEQUENCE = new RegExp('\\u001B\\[[0-9;]*m', 'g');

export function mapEntrypoints(assetPath: string, inputDir: string, prefix: string): Record<string, string> {
    const sourcePath = path.join(assetPath, inputDir);
    return Object.fromEntries(
        collectFiles(sourcePath).map((file) => {
            const relativePath = path.parse(path.relative(assetPath, file));
            const outputPath = path.join(relativePath.dir, relativePath.name);
            const key = normalizePath(`${prefix}/${outputPath}`);
            return [key, path.resolve(file)];
        })
    );
}

export function isFolder(folder: string): boolean {
    return fs.lstatSync(path.resolve(folder)).isDirectory();
}

export function combineFiles(files: string[], relativePath: string): string[] {
    return [...files, path.join(process.cwd(), relativePath)];
}

export function collectFiles(folder: string, files: string[] = []): string[] {
    const entries = fs.readdirSync(folder, { withFileTypes: true });

    for (const entry of entries) {
        const name = entry.name;

        if (name.startsWith('_')) continue;
        if (name.includes('.DS_Store')) continue;

        const fullPath = path.join(folder, name);
        if (entry.isDirectory()) {
            collectFiles(fullPath, files);
        } else if (entry.isFile()) {
            files.push(fullPath);
        }
    }

    return files;
}

export function getExtension(asset) {
    const nameParts = asset.name?.split('.') ?? [];
    return nameParts.length > 1 ? nameParts[nameParts.length - 1] : 'build';
}

// Custom logger
const base = createLogger('info', { prefix: 'vite' });
export const customLogger: Logger = {
    ...base,
    info(msg, opts) {
    const cleanMsg = msg.replace(ANSI_COLOR_SEQUENCE, '').trim();

        if (cleanMsg == '') {
            return;
        }

        if (msg.endsWith('\n')) {
            msg = msg.slice(0, -1);
        }

        if (msg.startsWith('\n')) {
            msg = msg.replace(/^\n+/, '');
        }

        if (cleanMsg.startsWith('vite v')) {
            console.log(chalk.gray(`FRONT`, process.env.NODE_ENV?.toUpperCase()));
            base.info(msg + '\n', opts);
            return;
        }

        if (cleanMsg.startsWith('VITE v')) {
            base.info('\n' + msg, opts);
            return;
        }

        if (cleanMsg.includes('server restarted')) {
            base.info(msg, opts);
            base.info('');
            return;
        }

        if (cleanMsg.startsWith('LARAVEL v') || cleanMsg.includes('server restarted.')) {
            base.info(msg + '\n', opts);
            return;
        }

        if (
            cleanMsg.includes('➜  Local:   http') ||
            cleanMsg.includes('➜  Network: http') 
        ) {
            return;
        }

        if (msg.includes('APP_URL')) {
            logURLs({
                'Local URL': process.env.APP_URL,
                'UI Library': `${process.env.APP_URL}/ui-library`,
            });
            return;
        }

        base.info(msg, opts);
    },
    warn: (msg, opts) => base.warn(msg, opts),
    warnOnce: (msg, opts) => base.warnOnce(msg, opts),
    error: (msg, opts) => base.error(msg, opts),
    clearScreen: (type) => base.clearScreen(type),
};

export function logURLs(urls: Record<string, string>): void {
    const entries = Object.entries(urls).filter(([name, url]) => name && url);

    if (entries.length === 0) return;

    const longestNameLength = Math.max(...entries.map(([name]) => name.length));

    entries.forEach(([name, url]) => {
        const nameWithColon = `${name}:`;
        const padding = longestNameLength - name.length;
        const paddedName = nameWithColon.padEnd(nameWithColon.length + padding);

        process.stdout.write(' ');
        process.stdout.write(chalk.green(' ➜  '));
        process.stdout.write(chalk.blueBright(paddedName + '  '));
        process.stdout.write(chalk.cyanBright(url) + '\n');
    });
}
