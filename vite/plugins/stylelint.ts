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
import { getViteWatcher } from './vite-watcher';
import fs from 'node:fs';
import path from 'node:path';
import stylelint from 'stylelint';
import type { Plugin } from 'vite';

interface StyleLintOptions {
    include?: string[];
    ignorePattern?: string[];
    ignorePath?: string | undefined;
    fix?: boolean;
    cache?: boolean;
    cacheLocation?: string;
    clearCache?: boolean;
    failOnError?: boolean;
    failOnWarning?: boolean;
    configFile?: string;
    debug?: boolean;
    quietDeprecationWarnings?: boolean;
    lintAllOnSave?: boolean;
}

export function stylelintPlugin(options: StyleLintOptions = {}): Plugin {
    const {
        configFile,
        include = ['resources/assets/scss/**/*.{scss,css}'],
        ignorePattern = null,
        ignorePath = undefined,
        fix = false,
        cache = process.env.APP_ENV !== 'production' && watchMode,
        cacheLocation = '.stylelintcache',
        clearCache = true,
        failOnWarning = false,
        quietDeprecationWarnings = false,
        failOnError = process.env.VITE_BUILD_FAIL_ON_ERROR === 'true',
        debug = process.env.VITE_LINT_DEBUG === 'true' || options.debug === true,
        lintAllOnSave = options.lintAllOnSave === true,
    } = options;

    if (clearCache) {
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
                debugLog('Cleared stylelint cache', { cacheLocation: resolvedCachePath }, debug);
            } else {
                debugLog('No existing stylelint cache to clear', { cacheLocation: resolvedCachePath }, debug);
            }
        } catch (err) {
            logError('Failed clearing stylelint cache', err, debug);
        }
    }

    const runner = createPluginRunner('StyleLint');
    let isLinting = false;
    let isInitialRun = true;
    const failOnErrorLocal = watchMode ? false : failOnError;

    const lintFiles = async (
        changedFile?: string,
        context: { isWatchRun?: boolean } = {}
    ) => {
        if (isLinting) {
            debugLog('Lint already in progress, skipping', {}, debug);
            return;
        }

        isLinting = true;

        try {
            const isWatchRun = context.isWatchRun === true;
            const shouldLintAll = !changedFile || lintAllOnSave || !isWatchRun || fix;
            const lintTarget = shouldLintAll ? include : [changedFile as string];

            if (!changedFile || shouldLintAll) {
                runner.start(undefined, { isInitialRun });
            } else {
                const detail = path.relative(process.cwd(), changedFile);
                runner.start(`(${detail})`);
            }
            debugLog(
                'Starting lint process',
                {
                    target: lintTarget,
                    fix,
                    cache,
                    changedFile,
                },
                debug
            );

            const result = await stylelint.lint({
                files: lintTarget,
                ignorePath,
                ignorePattern,
                fix,
                cache,
                cacheLocation,
                configFile,
                quietDeprecationWarnings,
            });

            const fileResults: UnifiedFileResult[] = result.results
                .filter((r) => r.warnings.length > 0)
                .map((r) => ({
                    filePath: r.source || 'unknown file',
                    messages: r.warnings.map((w) => {
                        let msg = w.text || 'Unknown error';
                        // Remove (ruleName) at end or anywhere in string
                        msg = msg.replace(/\s*\([^)]+\)\s*$/, '').replace(/\s*\([^)]+\)\s*/g, '');
                        return {
                            line: w.line ?? null,
                            column: w.column ?? null,
                            severity: w.severity as 'error' | 'warning',
                            message: msg,
                            ruleId: w.rule,
                        } as UnifiedLintMessage;
                    }),
                }));

            const { totalErrors, totalWarnings } = printUnifiedLintReport('StyleLint', fileResults, { runner });

            debugLog(
                'Lint results summary',
                {
                    totalErrors,
                    totalWarnings,
                    fileCount: fileResults.length,
                    changedFile,
                },
                debug
            );

            if (totalErrors > 0 && failOnErrorLocal) {
                const warningPart = totalWarnings ? ` and ${totalWarnings} warning(s)` : '';
                recordLintFailure('StyleLint', `Found ${totalErrors} error(s)${warningPart}`);
                return result;
            }

            if (totalErrors === 0 && totalWarnings > 0 && failOnWarning) {
                recordLintFailure('StyleLint', `Found ${totalWarnings} warning(s)`);
                return result;
            }

            return result;
        } catch (error) {
            if (error instanceof Error && error.name === 'LintBuildError') {
                throw error;
            }

            logError('StyleLint error:', error, debug);
            runner.error('configuration error');

            if (failOnErrorLocal) {
                throw createBuildError('StyleLint configuration error', error);
            }
        } finally {
            isLinting = false;
            isInitialRun = false;
        }
    };

    return {
        name: 'vite-plugin-stylelint',

        configResolved(config) {
            if (debug && config) {
                getViteWatcher().setDebug(true);
            }
        },

        async buildStart() {
            if (watchMode) {
                getViteWatcher().addCallback((event) => {
                    if (event.file.match(/\.(scss|css|sass)$/) && event.type === 'change') {
                        if (event.action === 'deleted') {
                            return;
                        }
                        if (!lintAllOnSave && !fs.existsSync(event.path)) {
                            debugLog('Style file change detected but file is missing, skipping', { path: event.path }, debug);
                            return;
                        }
                        debugLog('Style file changed', { type: event.type, file: event.file, path: event.path }, debug);

                        runner
                            .run(
                                () => lintFiles(lintAllOnSave ? undefined : event.path, { isWatchRun: true }),
                                { skipInitialDelay: true }
                            )
                            .catch((error) => logError('StyleLint watch error', error, debug));
                    }
                });
            }

            if (isInitialRun && !watchMode) {
                isInitialRun = false;
            }
            
            await runner.run(() =>  lintFiles());

        },

        buildEnd() {
            isInitialRun = true;
        },
    };
}
