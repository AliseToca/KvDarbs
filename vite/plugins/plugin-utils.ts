/**
 * Utility functions for Vite plugins
 */

import chalk from 'chalk';
import path from 'node:path';
import fs from 'node:fs';

export const watchMode = process.env.NODE_ENV == 'development';

// const INITIAL_TASK_DELAY_MS = watchMode ? 1000 : 0;
process.stdout.write('\x1b[H\x1b[2J');
class LintReportBuffer {
    private readonly reports = new Map<string, string[]>();
    private order: string[] = [];

    set(toolName: string, lines: string[]): void {
        if (!this.order.includes(toolName)) {
            this.order.push(toolName);
        }
        this.reports.set(toolName, [...lines]);
    }

    clear(toolName: string): void {
        if (!this.reports.has(toolName)) return;
        this.reports.delete(toolName);
        this.order = this.order.filter((name) => name !== toolName);
    }

    flush(): void {
        if (this.reports.size === 0) return;
        
        statusLine.finish('\n');
        for (const toolName of this.order) {
            const lines = this.reports.get(toolName);
            if (!lines || lines.length === 0) continue;
            for (const line of lines) {
                if (line.length === 0) {
                    statusLine.finish('');
                } else {
                    console.log(line);
                }
            }
        }
        statusLine.finish('');

        this.reports.clear();
        this.order = [];
    }
}

const lintReportBuffer = new LintReportBuffer();

class LintFailureCollector {
    private failures = new Map<string, string>();

    record(toolName: string, message: string): void {
        this.failures.set(toolName, message);
    }

    clear(toolName: string): void {
        this.failures.delete(toolName);
    }

    consume(): { toolName: string; message: string }[] {
        const entries = Array.from(this.failures.entries()).map(([toolName, message]) => ({
            toolName,
            message,
        }));
        this.failures.clear();
        return entries;
    }
}

const lintFailureCollector = new LintFailureCollector();

function queueLintReport(toolName: string, lines: string[]): void {
    if (lines.length === 0) {
        lintReportBuffer.clear(toolName);
        clearLintFailure(toolName);
        return;
    }

    lintReportBuffer.set(toolName, lines);
}

function flushPendingLintReports(): void {
    lintReportBuffer.flush();
}

export function recordLintFailure(toolName: string, message: string): void {
    lintFailureCollector.record(toolName, message);
}

export function clearLintFailure(toolName: string): void {
    lintFailureCollector.clear(toolName);
}

function consumeLintFailures(): { toolName: string; message: string }[] {
    return lintFailureCollector.consume();
}

function createAggregatedLintError(entries: { toolName: string; message: string }[]): Error {
    const combined = entries.map((entry) => `${entry.toolName}: ${entry.message}`).join('\n');
    const error = new Error(combined);
    error.name = 'LintBuildError';
    error.stack = '';
    return error;
}

class PluginTaskScheduler {
    private sequence: Promise<void> = Promise.resolve();
    // private initialDelayPending = INITIAL_TASK_DELAY_MS > 0;
    private pendingTaskCount = 0;
    // private initialDelayPending = INITIAL_TASK_DELAY_MS > 0;
    enqueue<T>(task: () => Promise<T>, options: PluginRunOptions = {}): Promise<T> {
        this.pendingTaskCount++;

        const finalize = (): Error | null => {
            this.pendingTaskCount = Math.max(this.pendingTaskCount - 1, 0);
            if (this.pendingTaskCount === 0) {


                flushPendingLintReports();

                
                const failures = consumeLintFailures();
                if (failures.length > 0) {
                    return createAggregatedLintError(failures);
                }
            }
            return null;
        };

        const runTaskWithFinalize = async (): Promise<T> => {
            let taskError: unknown = null;
            let taskResult: T | undefined;

            try {
                taskResult = await task();
            } catch (error) {
                taskError = error;
            }

            const failure = finalize();
            if (failure) {
                throw failure;
            }

            if (taskError !== null) {
                throw taskError;
            }

            return taskResult as T;
        };

        if (options.parallel) {
            return Promise.resolve().then(runTaskWithFinalize);
        }

    const result = this.sequence.then(runTaskWithFinalize);
        this.sequence = result.then(
            () => undefined,
            () => undefined
        );
        return result;
    }
}

const pluginTaskScheduler = new PluginTaskScheduler();

export interface PluginRunOptions {
    skipInitialDelay?: boolean;
    parallel?: boolean;
}

interface RunnerStartOptions {
    isInitialRun?: boolean;
}

export interface RunnerFinishOptions {
    persist?: boolean;
}

/**
 * Helper for standardising status output across plugins.
 * Coordinates with the shared StatusLine instance so all tools
 * announce start/success/failure in the same format.
 */
export class PluginRunner {
    private readonly label: string;

    constructor(private readonly toolKey: string, label?: string) {
        this.label = label ?? toolKey;
    }

    start(detail?: string, options: RunnerStartOptions = {}): void {
        const suffix = detail ? ` ${detail}` : '';
        const message = chalk.cyan(`→ Running ${this.label}${suffix}...`);
        statusLine.show(message, options.isInitialRun ?? false);
    }

    success(message: string, options?: RunnerFinishOptions): void {
        this.emit(chalk.green('✓ ') + `${this.label}: ${message}`, options);
    }

    warning(message: string, options?: RunnerFinishOptions): void {
        this.emit(chalk.yellow('⚠ ') + `${this.label}: ${message}`, options);
    }

    error(message: string, options?: RunnerFinishOptions): void {
        this.emit(chalk.red('✗ ') + `${this.label}: ${message}`, options);
    }

    log(message: string, options?: RunnerFinishOptions): void {
        this.emit(message, { ...(options || {}), persist: options?.persist ?? false });
    }

    run<T>(task: () => Promise<T>, options: PluginRunOptions = {}): Promise<T> {
        return pluginTaskScheduler.enqueue(task, options);
    }

    private emit(formatted: string, options?: RunnerFinishOptions): void {
        const shouldPersist = options?.persist ?? true;

        if (shouldPersist) {
            statusLine.replaceToolLine(this.toolKey, formatted);
        } else {
            statusLine.finish(formatted);
        }
    }
}

export function createPluginRunner(toolKey: string, label?: string): PluginRunner {
    return new PluginRunner(toolKey, label);
}

/**
 * Status line manager for showing "Running..." messages that can be cleared
 */
class StatusLine {
    private isShowing = false;
    private lastRunningMessage = '';
    private initialRunComplete = new Set<string>();
    private toolStatus = new Map<string, string>(); // Track status for each tool
    private toolOrder: string[] = []; // Track order tools were started
    private isInitialBuild = true; // Track if this is the initial build

    show(message: string, isInitialRun = false): void {
        // Skip if this is a duplicate running message
        if (message.includes('Running') && this.lastRunningMessage === message) {
            return;
        }

        if (message.includes('Running')) {
            this.lastRunningMessage = message;
            this.isShowing = true;

            // Extract tool name from message (e.g., "StyleLint", "ESLint", "Prettier")
            const toolMatch = message.match(/Running\s+(\w+)/);
            const toolName = toolMatch ? toolMatch[1] : '';

            if (toolName) {
                this.toolStatus.set(toolName, message);
                if (!this.toolOrder.includes(toolName)) {
                    this.toolOrder.push(toolName);
                }
            }

            if (isInitialRun || !this.initialRunComplete.has(toolName)) {
                if (toolName) this.initialRunComplete.add(toolName);
                console.log(message);
            }
            return;
        }

        console.log(message);
    }

    update(message: string): void {
        this.show(message);
    }

    clear(): void {
        this.isShowing = false;
        this.lastRunningMessage = '';
        this.toolStatus.clear();
        this.toolOrder.length = 0;
    }

    markInitialBuildComplete(): void {
        this.isInitialBuild = false;
        void import('./vite-watcher')
            .then(({ getViteWatcher }) => {
                getViteWatcher().startSpinner();
            })
            .catch((error) => {
                // Ignore errors if vite-watcher is not available
                console.error('Error starting Vite watcher spinner:', error);
            });
    }

    finish(message: string): void {
        console.log(message);
    }

    // For spinner to check if status line is active
    get isActive(): boolean {
        return this.isShowing;
    }

    get message(): string {
        return this.lastRunningMessage;
    }

    get isInitialBuildActive(): boolean {
        return this.isInitialBuild;
    }

    // Update a tool's status and redraw all tool lines
    replaceToolLine(toolName: string, message: string): void {
        // Use redraw approach during initial build (build mode or dev initial startup)
        // Always update cache so last status is available
        this.toolStatus.set(toolName, message);

        if (!this.toolOrder.includes(toolName)) {
            this.toolOrder.push(toolName);
        }

        if (!watchMode || this.isInitialBuild) {
            // Use redraw approach during initial build (build mode or first watch run)
            const numTools = this.toolOrder.length;
            process.stdout.write(`\x1b[${numTools}A`);

            for (const tool of this.toolOrder) {
                process.stdout.write('\x1b[2K');
                console.log(this.toolStatus.get(tool) || '' + '\n');
            }

            setTimeout(() => { this.allToolsComplete() }, 50);
            // console.log(statusLine)

            if (this.isInitialBuild) {
                this.allToolsComplete();
            }
        } else {
            // After initial build just emit the updated tool message once
            // so prior console output (e.g. file change logs) stays intact.
            console.log(message);
        }
    }

    private allToolsComplete() {
            // console.log(this.toolStatus)
            if (!this.isInitialBuild) return;
            if (this.toolOrder.length === 0) return;

            setTimeout(() => {

            if ( this.toolOrder.every((tool) => {
                const status = this.toolStatus.get(tool) || '';
                return status.includes('✓') || status.includes('✗') || status.includes('⚠');
            })) {
                this.markInitialBuildComplete();
            }

            }, 5);

    }
}

export const statusLine = new StatusLine();

/**
 * Safely formats any value for console output, preventing [object Object] issues
 */
export function formatForConsole(value: unknown): string {
    if (value === null) return 'null';
    if (value === undefined) return 'undefined';
    if (typeof value === 'string') return value;
    if (typeof value === 'number' || typeof value === 'boolean') return String(value);
    if (value instanceof Error) {
        return value.message;
    }

    try {
        return JSON.stringify(value, null, 2);
    } catch {
        return String(value);
    }
}

/**
 * Safely logs an error with proper formatting
 */
export function logError(prefix: string, error: unknown, showStack = false): void {
    const errorMessage = error instanceof Error ? error.message : formatForConsole(error);
    console.error(chalk.red(prefix), errorMessage);

    if (showStack && error instanceof Error && error.stack && error.stack !== errorMessage) {
        console.error(chalk.gray('Stack trace:'), error.stack);
    }
}

/**
 * Safely logs information with proper formatting
 */
export function logInfo(message: string, data?: unknown): void {
    if (data !== undefined) {
        console.log(message, formatForConsole(data));
    } else {
        console.log(message);
    }
}

/**
 * Creates a formatted error message for build failures
 */
export function createBuildError(context: string, originalError: unknown): Error {
    const errorMessage = originalError instanceof Error ? originalError.message : formatForConsole(originalError);
    consumeLintFailures();
    flushPendingLintReports();

    const message = `${context}: ${errorMessage}`;
    const error = new Error(message);
    error.name = 'LintBuildError';
    error.stack = '';
    return error;
}

/**
 * Debug logging that can be enabled/disabled
 */
export function debugLog(message: string, data?: unknown, enabled = false): void {
    if (!enabled) return;

    const timestamp = new Date().toISOString();
    const prefix = chalk.gray(`[DEBUG ${timestamp}] `);

    if (data !== undefined) {
        statusLine.finish(prefix + message + formatForConsole(data));
    } else {
        statusLine.finish(prefix + message);
    }
}

/**
 * Simple glob pattern matching with brace expansion support
 * Supports: **, *, {ext1,ext2}, and basic path patterns
 */
export function matchPattern(filePath: string, pattern: string): boolean {
    const regex = pattern
        .replace(/\*\*/g, '.*') // ** matches any path
        .replace(/\*/g, '[^/]*') // * matches anything except path separator
        .replace(/\./g, '\\.') // Escape dots
        .replace(/\{([^}]+)\}/g, (_, group) => {
            // {js,ts} becomes (js|ts)
            return `(${group.split(',').join('|')})`;
        });

    return new RegExp(`^${regex}$`).test(filePath);
}

/**
 * Check if a file path should be processed based on include/exclude patterns
 */
export function shouldProcessFile(filePath: string, include: string[] = ['**/*'], exclude: string[] = []): boolean {
    const relativePath = path.relative(process.cwd(), filePath);

    // Check if file matches include patterns
    const isIncluded = include.some((pattern) => matchPattern(relativePath, pattern));
    if (!isIncluded) return false;

    // Check if file matches exclude patterns
    const isExcluded = exclude.some((pattern) => matchPattern(relativePath, pattern));
    return !isExcluded;
}

// ---------------------------------------------------------------------------
// Unified lint report formatting (shared by ESLint & Stylelint plugins)
// ---------------------------------------------------------------------------

export interface UnifiedLintMessage {
    line: number | null;
    column: number | null;
    severity: 'error' | 'warning';
    message: string;
    ruleId?: string | null;
}

export interface UnifiedFileResult {
    filePath: string; // absolute path
    messages: UnifiedLintMessage[];
}

export interface UnifiedReportSummary {
    totalErrors: number;
    totalWarnings: number;
    fileCount: number; // files that had issues (messages length > 0)
}

export interface UnifiedLintReportOptions {
    runner?: PluginRunner;
    persist?: boolean;
}

/**
 * Print a unified, consistent lint report for any tool.
 * Layout:
 * <relative/path/file.ext>
 *   ✖ line:col  Message text  ruleId
 * Summary lines mimic: ✗ TOOL found X error(s) (and Y warning(s)) in Z file(s)
 */
export function printUnifiedLintReport(
    toolName: string,
    fileResults: UnifiedFileResult[],
    options: UnifiedLintReportOptions = {}
): UnifiedReportSummary {
    const filesWithIssues = fileResults.filter((fr) => fr.messages.length > 0);
    let totalErrors = 0;
    let totalWarnings = 0;
    const runner = options.runner;
    const runnerFinishOptions = runner ? { persist: options.persist } : undefined;

    // Count errors and warnings first
    for (const fr of filesWithIssues) {
        for (const m of fr.messages) {
            if (m.severity === 'error') totalErrors++;
            else totalWarnings++;
        }
    }

    // Use replaceToolLine during initial build, statusLine.finish during file watching
    if (filesWithIssues.length > 0) {
        if (totalErrors > 0) {
            const warningPart = totalWarnings ? ` and ${totalWarnings} warning(s)` : '';
            const message = `found ${totalErrors} error(s)${warningPart} in ${filesWithIssues.length} file(s)`;

            if (runner) {
                runner.error(message, runnerFinishOptions);
            } else {
                const formatted = chalk.red(`✗ ${toolName}: ${message}`);
                if (!watchMode || statusLine.isInitialBuildActive) {
                    statusLine.replaceToolLine(toolName, formatted);
                } else {
                    statusLine.finish(formatted);
                }
            }
        } else if (totalWarnings > 0) {
            const message = `found ${totalWarnings} warning(s) in ${filesWithIssues.length} file(s)`;

            if (runner) {
                runner.warning(message, runnerFinishOptions);
            } else {
                const formatted = chalk.yellow(`⚠ ${toolName} ${message}`);
                if (!watchMode || statusLine.isInitialBuildActive) {
                    statusLine.replaceToolLine(toolName, formatted);
                } else {
                    statusLine.finish(formatted);
                }
            }
        }
    } else {
        if (runner) {
            runner.success('passed', runnerFinishOptions);
        } else {
            const message = chalk.green('✓ ') + `${toolName}: passed`;

            if (!watchMode || statusLine.isInitialBuildActive) {
                statusLine.replaceToolLine(toolName, message);
            } else {
                statusLine.finish(message);
            }
        }

        clearLintFailure(toolName);
        queueLintReport(toolName, []);
        if (!runner) {
            flushPendingLintReports();
        }

        return { totalErrors: 0, totalWarnings: 0, fileCount: 0 };
    }

    // Reset counters for detailed output
    totalErrors = 0;
    totalWarnings = 0;

    const outputLines: string[] = [];
    
    outputLines.push(chalk.cyanBright(`${toolName} report:`));

    for (const fr of filesWithIssues) {
        const relPath = path.relative(process.cwd(), fr.filePath);
        const displayPath = relPath.startsWith('.') ? relPath : `./${relPath}`;

        outputLines.push(chalk.gray(displayPath));

        if (fr.messages.length === 0) continue;

        const lineWidth = Math.max(...fr.messages.map((m) => (m.line ?? '').toString().length), 2);
        const colWidth = Math.max(...fr.messages.map((m) => (m.column ?? '').toString().length), 2);

        for (const m of fr.messages) {
            if (m.severity === 'error') totalErrors++;
            else totalWarnings++;

            const symbol = m.severity === 'error' ? chalk.red('✖') : chalk.yellow('⚠');

            let location = '';
            if (m.line != null) {
                const line = String(m.line).padStart(lineWidth, ' ');
                location = line;
                if (m.column != null) {
                    const column = String(m.column).padEnd(colWidth, ' ');
                    location += `:${column}`;
                }
            }

            const rule = m.ruleId ? chalk.gray(m.ruleId) : '';
            const message = m.message.replace(/\s+/g, ' ').trim();

            let output = `  `;
            if (location.trim().length > 0) {
                output += `${chalk.gray(location)}  ${symbol}`;
            }
            output += `  ${chalk.dim(message)}`;
            if (rule) {
                output += `  ${rule}`;
            }

            outputLines.push(output.trimEnd());
        }

        outputLines.push('');
    }

    queueLintReport(toolName, outputLines);

    if (!runner) {
        flushPendingLintReports();
    }

    return { totalErrors, totalWarnings, fileCount: filesWithIssues.length };
}

// Load JSON file contents safely
const loadJson = (filePath: string) => JSON.parse(fs.readFileSync(filePath, 'utf8')) as Record<string, unknown>;

// ---------------------------------------------------------------------------
// Package version resolution
// ---------------------------------------------------------------------------

export function resolvePackageVersion(packageName: string, fallbackVersion = 'latest'): string {
    try {
        // Check for direct dependency first
        const pkgJsonPath = require.resolve(`${packageName}/package.json`);
        const pkgJson = loadJson(pkgJsonPath);

        if (pkgJson && typeof pkgJson.version === 'string') {
            return pkgJson.version;
        }
    } catch {
        // Ignore errors, fallback to searching in node_modules
    }

    try {
        // Fallback: search in node_modules for the package
        const nodeModulesPath = path.join(process.cwd(), 'node_modules', packageName, 'package.json');
        const pkgJson = loadJson(nodeModulesPath);

        if (pkgJson && typeof pkgJson.version === 'string') {
            return pkgJson.version;
        }
    } catch {
        // Ignore errors, return fallback version
    }

    return fallbackVersion;
}
