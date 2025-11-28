import {
    createBuildError,
    createPluginRunner,
    debugLog,
    logError,
    printUnifiedLintReport,
    recordLintFailure,
    UnifiedFileResult,
    UnifiedLintMessage,
    watchMode,
} from './plugin-utils';
import { ESLint } from 'eslint';
import { getViteWatcher } from './vite-watcher';
import fs from 'node:fs';
import path from 'node:path';
import type { Plugin } from 'vite';

interface ESLintOptions {
    include?: string[];
    exclude?: string[] | null;
    fix?: boolean;
    fixTypes?: ('problem' | 'suggestion' | 'layout')[];
    cache?: boolean;
    cacheLocation?: string;
    clearCache?: boolean;
    failOnError?: boolean;
    failOnWarning?: boolean;
    debug?: boolean;
    lintAllOnSave?: boolean;
}

export function eslintPlugin(options: ESLintOptions = {}): Plugin {
    const {
        include = ['resources/assets/js/**/*.{js,ts}'],
        exclude = ['**/node_modules/**', '**/dist/**', '**/build/**'],
        fix = false,
        fixTypes = ['problem', 'suggestion', 'layout'],
        cache = process.env.APP_ENV !== 'production' && watchMode,
        cacheLocation = '.eslintcache',
        failOnWarning = false,
        failOnError = process.env.VITE_BUILD_FAIL_ON_ERROR === 'true',
        debug = process.env.VITE_LINT_DEBUG === 'true' || options.debug === true,
        lintAllOnSave = options.lintAllOnSave === true,
    } = options;

    const runner = createPluginRunner('ESLint');
    let eslint: ESLint;
    const failOnErrorLocal = watchMode ? false : failOnError;

    if (options.clearCache) {
        try {
            const resolvedCachePath = path.isAbsolute(cacheLocation)
                ? cacheLocation
                : path.join(process.cwd(), cacheLocation);
            if (fs.existsSync(resolvedCachePath)) {
                const stat = fs.lstatSync(resolvedCachePath);
                if (stat.isDirectory()) {
                    fs.rmSync(resolvedCachePath, { recursive: true, force: true });
                } else {
                    fs.unlinkSync(resolvedCachePath);
                }
                debugLog('Cleared ESLint cache', { cacheLocation: resolvedCachePath }, debug);
            } else {
                debugLog('No existing ESLint cache to clear', { cacheLocation: resolvedCachePath }, debug);
            }
        } catch (err) {
            logError('Failed clearing ESLint cache', err, debug);
        }
    }

    /**
     * Lint all files matching the include patterns
     */
    let isLinting = false;

    const lintFiles = async (
        changedFile?: string,
        context: { isWatchRun?: boolean } = {}
    ) => {
        if (isLinting) {
            debugLog('ESLint run already in progress, skipping', {}, debug);
            return;
        }

        isLinting = true;
        try {
            const isWatchRun = context.isWatchRun === true;
            const lintAll = !isWatchRun || lintAllOnSave || !changedFile;
            const lintTarget = lintAll ? include : [changedFile as string];

            const detail = lintAll || !changedFile ? undefined : path.relative(process.cwd(), changedFile);

            runner.start(detail ? `(${detail})` : undefined, { isInitialRun: lintAll && isInitialRun });
            debugLog(
                'Starting ESLint process',
                { include, fix, cache, lintAll, lintTarget },
                debug
            );

            const results = await eslint.lintFiles(lintTarget);

            if (fix) {
                await ESLint.outputFixes(results);
            }

            const fileResults: UnifiedFileResult[] = results
                .filter((r) => r.messages.length > 0)
                .map((r) => ({
                    filePath: r.filePath,
                    messages: r.messages.map(
                        (msg) =>
                            ({
                                line: msg.line ?? null,
                                column: msg.column ?? null,
                                severity: msg.severity === 2 ? 'error' : 'warning',
                                message: msg.message,
                                ruleId: msg.ruleId || undefined,
                            }) as UnifiedLintMessage
                    ),
                }));

            const { totalErrors, totalWarnings } = printUnifiedLintReport('ESLint', fileResults, { runner });

            debugLog('ESLint results summary', { totalErrors, totalWarnings, fileCount: fileResults.length }, debug);

            if (totalErrors > 0 && failOnErrorLocal) {
                const warningPart = totalWarnings ? ` and ${totalWarnings} warning(s)` : '';
                recordLintFailure('ESLint', `Found ${totalErrors} error(s)${warningPart}`);
                return;
            }

            if (totalWarnings > 0 && failOnWarning) {
                recordLintFailure('ESLint', `Found ${totalWarnings} warning(s)`);
                return;
            }
        } catch (error) {
            if (error instanceof Error && error.name === 'LintBuildError') {
                throw error;
            }

            logError('ESLint error:', error, debug);
            runner.error('configuration error');

            if (failOnErrorLocal) {
                throw createBuildError('ESLint configuration error', error);
            }
        } finally {
            isLinting = false;
            isInitialRun = false;
        }
    };
    let isInitialRun = true;

    return {
        name: 'vite-plugin-eslint',
        enforce: 'pre',

        configResolved() {
            eslint = new ESLint({
                cache,
                fix,
                fixTypes,
                ignorePatterns: exclude || null,
            });

            if (debug) {
                getViteWatcher().setDebug(true);
            }
        },

        async buildStart() {
            if (watchMode) {
                getViteWatcher().addCallback((event) => {
                    if (event.file.match(/\.(js|ts|jsx|tsx)$/) && event.type === 'change') {
                        if (event.action === 'deleted') {
                            return;
                        }

                        if (!lintAllOnSave && !fs.existsSync(event.path)) {
                            debugLog('Changed file no longer exists, skipping ESLint watch run', { path: event.path }, debug);
                            return;
                        }

                        debugLog(
                            'Script file changed',
                            { type: event.type, file: event.file, path: event.path },
                            debug
                        );

                        runner
                            .run(
                                () => lintFiles(lintAllOnSave ? undefined : event.path, { isWatchRun: true }),
                                { skipInitialDelay: true }
                            )
                            .catch((error) => logError('ESLint watch error', error, debug));
                    }
                });

                debugLog('ESLint file watching enabled', {}, debug);
            } 

            if (isInitialRun && !watchMode) {
                isInitialRun = false;
            }

            await runner.run(() => lintFiles());
        },

        buildEnd() {
            isInitialRun = true;
        },
    };
}
