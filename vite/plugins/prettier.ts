import chalk from 'chalk';
import fs from 'node:fs';
import path from 'node:path';
import prettier, { type Options as PrettierOptions } from 'prettier';
import type { Plugin } from 'vite';

import {
    createBuildError,
    createPluginRunner,
    debugLog,
    logError,
    logInfo,
    matchPattern,
    printUnifiedLintReport,
    recordLintFailure,
    UnifiedFileResult,
    watchMode,
} from './plugin-utils';
import { getViteWatcher } from './vite-watcher';

interface PrettierPluginOptions {
    directories?: string[];
    extensions?: string[];
    ignore?: string[];
    ignorePath?: string | undefined;
    write?: boolean;
    validate?: boolean;
    failOnError?: boolean;
    debug?: boolean;
}

interface FormatSingleFileResult {
    success: boolean;
    formatted: boolean;
    error?: string;
    filePath: string;
    fileResult?: UnifiedFileResult;
}

export function prettierPlugin(options: PrettierPluginOptions = {}): Plugin {
    const {
        directories = ['resources'],
        extensions = ['js', 'ts', 'jsx', 'tsx', 'css', 'scss', 'less', 'html', 'vue', 'json', 'md', 'php'],
        ignore = ['node_modules/**/*', 'vendor/**/*', 'public/build/**/*', 'storage/**/*'],
        ignorePath = '.gitignore',
        write = false,
        failOnError = false,
        validate = true,
        debug = process.env.VITE_LINT_DEBUG === 'true' || options.debug === true,
    } = options;

    const runner = createPluginRunner('Prettier');
    const manualConfig: PrettierOptions | null = null;

    interface PrettierFormattedError extends Error {
        loc?: {
            start?: {
                line?: number;
                column?: number;
            };
        };
    }

    const toPrettierError = (err: unknown): PrettierFormattedError => {
        if (err instanceof Error) {
            return err as PrettierFormattedError;
        }

        const message = err === null || err === undefined ? 'Unknown Prettier error' : String(err);
        return new Error(message) as PrettierFormattedError;
    };

    const formatSingle = async (filePath: string): Promise<FormatSingleFileResult> => {
        const relativePath = path.relative(process.cwd(), filePath);

        try {
            const info = await prettier.getFileInfo(filePath, { ignorePath });

            if (info.ignored) {
                debugLog(`File ignored by Prettier: ${relativePath}`, undefined, debug);
                return {
                    success: true,
                    formatted: false,
                    error: 'File is ignored',
                    filePath: relativePath,
                };
            }

            if (!info.inferredParser) {
                debugLog(`No parser available for: ${relativePath}`, undefined, debug);
                return {
                    success: true,
                    formatted: false,
                    error: 'No parser available',
                    filePath: relativePath,
                };
            }

            const source = fs.readFileSync(filePath, 'utf8');
            const resolvedConfig = manualConfig
                ? await Promise.resolve(manualConfig)
                : (await prettier.resolveConfig(filePath)) || {};

            const formatted = await prettier.format(source, { ...resolvedConfig, filepath: filePath });

            if (formatted !== source) {
                debugLog(`File needs formatting: ${relativePath}`, undefined, debug);

                if (write) {
                    fs.writeFileSync(filePath, formatted, 'utf8');
                    // Suppress file change events for this file temporarily AFTER writing
                    debugLog(`File formatted and written: ${relativePath}`, undefined, debug);
                    getViteWatcher().suppressFileChanges(filePath, 1000);
                    const message = `formatted file: ${chalk.gray(`./${relativePath}`)}`;
                    runner.success(message, { persist: false });

                    return {
                        success: true,
                        formatted: true,
                        filePath: relativePath,
                    };
                } else {
                    
                    const fileResult: UnifiedFileResult = {
                        filePath,
                        messages: [
                            {
                                line: null,
                                column: null,
                                severity: 'error',
                                message: 'File is not formatted according to Prettier rules',
                                ruleId: 'prettier-format',
                            },
                        ],
                    };

                    printUnifiedLintReport('Prettier', [fileResult], { runner, persist: false });

                    return {
                        success: true,
                        formatted: false,
                        error: 'File needs formatting',
                        filePath: relativePath,
                        fileResult,
                    };
                }
            } else {
                debugLog(`File already formatted: ${relativePath}`, undefined, debug);
                return {
                    success: true,
                    formatted: false,
                    filePath: relativePath,
                };
            }
        } catch (err) {
            const error = toPrettierError(err);

            debugLog(`Prettier error for ${relativePath}`, error, debug);
            logError('Prettier formatting error', error);

            const loc = error.loc?.start;
            const fileResult: UnifiedFileResult = {
                filePath,
                messages: [
                    {
                        line: loc?.line ?? null,
                        column: loc?.column ?? null,
                        severity: 'error',
                        message: error.message || 'Unknown Prettier error',
                        ruleId: 'prettier-parse',
                    },
                ],
            };

            return {
                success: false,
                formatted: false,
                error: error.message,
                filePath: relativePath,
                fileResult,
            };
        }
    };

    const formatAll = async (isInitialRun = false): Promise<void> => {
        runner.start(`(${write ? 'write' : 'check'})`, { isInitialRun });
        debugLog('Starting Prettier format all', { directories, extensions, ignore }, debug);

        try {
            const allFiles: string[] = [];

            for (const dir of directories) {
                const dirPath = path.resolve(process.cwd(), dir);
                if (!fs.existsSync(dirPath)) {
                    debugLog(`Directory does not exist: ${dirPath}`, undefined, debug);
                    continue;
                }

                debugLog(`Collecting files from: ${dirPath}`, undefined, debug);
                const files = await collectFilesRecursively(dirPath);
                debugLog(`Found ${files.length} files in ${dirPath}`, undefined, debug);
                allFiles.push(...files);
            }

            debugLog(`Total files collected: ${allFiles.length}`, undefined, debug);

            const targets: string[] = [];
            for (const file of allFiles) {
                try {
                    // Check extension
                    const fileExt = path.extname(file).slice(1);
                    if (!extensions.includes(fileExt)) {
                        debugLog(`Skipping file (extension): ${file}`, { fileExt, extensions }, debug);
                        continue;
                    }

                    // Check ignore patterns
                    const relativePath = path.relative(process.cwd(), file);
                    const isIgnored = ignore.some((pattern) => matchPattern(relativePath, pattern));
                    if (isIgnored) {
                        debugLog(`Skipping file (ignored): ${relativePath}`, undefined, debug);
                        continue;
                    }

                    // Check Prettier support
                    const info = await prettier.getFileInfo(file, { ignorePath });
                    if (!info.ignored && info.inferredParser) {
                        targets.push(file);
                        debugLog(`Added target file: ${relativePath}`, undefined, debug);
                    } else {
                        debugLog(`Skipping file (no parser/ignored): ${relativePath}`, undefined, debug);
                    }
                } catch (err) {
                    debugLog(`Error checking file: ${file}`, err, debug);
                }
            }

            debugLog(`Target files: ${targets.length}`, undefined, debug);

            if (targets.length === 0) {
                logInfo(chalk.yellow('⚠ Prettier: No target files found.'));
                runner.warning('No target files found.');
                return;
            }

            const fileResults: UnifiedFileResult[] = [];
            let formattedCount = 0;

            for (const filePath of targets) {
                const result = await formatSingle(filePath);

                if (result.success && result.formatted) {
                    formattedCount++;
                }

                if (result.fileResult) {
                    fileResults.push(result.fileResult);
                }
            }

            debugLog(
                'Prettier processing complete',
                {
                    totalFiles: targets.length,
                    formattedCount,
                    errorsFound: fileResults.length,
                },
                debug
            );

            if (write) {
                const message =
                    formattedCount === 0 ? 'all files already formatted' : `formatted ${formattedCount} file(s)`;
                runner.success(message);
                return;
            }

            const { totalErrors } = printUnifiedLintReport('Prettier', fileResults, { runner });

            if (failOnError && totalErrors > 0) {
                runner.error(`${totalErrors} unformatted file(s)`);
                recordLintFailure('Prettier', `${totalErrors} unformatted file(s)`);
                return;
            }
        } catch (err) {
            if (err instanceof Error && err.name === 'LintBuildError') {
                throw err;
            }

            logError('Error during Prettier processing', err);
            runner.error('processing failed');
            throw createBuildError('Prettier processing failed', err);
        }
    };

    const collectFilesRecursively = async (dir: string): Promise<string[]> => {
        const files: string[] = [];

        const traverse = (currentDir: string) => {
            try {
                const entries = fs.readdirSync(currentDir, { withFileTypes: true });

                for (const entry of entries) {
                    if (entry.name === '.git' || entry.name === '.idea' || entry.name === '.DS_Store') continue;

                    const fullPath = path.join(currentDir, entry.name);

                    if (entry.isDirectory()) {
                        traverse(fullPath);
                    } else if (entry.isFile()) {
                        files.push(fullPath);
                    }
                }
            } catch (err) {
                debugLog(`Error reading directory: ${currentDir}`, err, debug);
            }
        };

        traverse(dir);
        return files;
    };

    return {
        name: 'vite-plugin-prettier',
        enforce: 'pre',

        async buildStart() {
            debugLog('Prettier plugin configResolved', { write, validate, watchMode }, debug);

            if (!write && !validate) return;

            if (watchMode) {
                debugLog('Setting up Prettier file watcher', undefined, debug);

                getViteWatcher().addCallback((event) => {
                    (async () => {
                        debugLog('File change event', event, debug);

                        const fileExt = path.extname(event.file).slice(1);
                        if (!extensions.includes(fileExt)) {
                            debugLog(`Skipping file (extension): ${event.file}`, undefined, debug);
                            return;
                        }

                        const isIgnored = ignore.some((pattern) => matchPattern(event.file, pattern));
                        if (isIgnored) {
                            debugLog(`Skipping file (ignored): ${event.file}`, undefined, debug);
                            return;
                        }

                        const absolutePath = path.resolve(process.cwd(), event.file);
                        if (!fs.existsSync(absolutePath)) {
                            debugLog(`File does not exist: ${absolutePath}`, undefined, debug);
                            return;
                        }

                        const info = await prettier.getFileInfo(absolutePath, { ignorePath });
                        if (info.ignored || !info.inferredParser) {
                            debugLog(`File ignored or no parser: ${event.file}`, info, debug);
                            return;
                        }

                        if (!write && !validate) {
                            debugLog('Neither write nor validate enabled, skipping', undefined, debug);
                            return;
                        }

                        debugLog(`Processing file change: ${event.file}`, undefined, debug);
                        formatSingle(absolutePath);

                    })().catch((error) => {
                        logError('Prettier watcher handling error', error);
                    });
                });
            }

            runner.run(() => formatAll(true));
        },
    };
}
