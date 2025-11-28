import { createPluginRunner, debugLog, logError, logInfo, matchPattern, watchMode } from './plugin-utils';
import { optimize } from 'svgo';
import { promises as fs } from 'node:fs';
import { getViteWatcher } from './vite-watcher';
import { normalizePath, type Plugin } from 'vite';
import chalk from 'chalk';
import fg from 'fast-glob';
import path from 'node:path';
import type { PluginRunner } from './plugin-utils';

interface SvgSpriteOptions {
    svgSource?: string;
    inputDir?: string;
    outputFile?: string;
    dontStripColor?: string[];
    icons?: Record<string, string>;
    debug?: boolean;
    hotReloadTimeout?: number;
}

interface SvgAttributes {
    viewBox?: string;
    width?: string;
    height?: string;
}

interface ProcessedSymbol {
    id: string;
    content: string;
}

export class SvgSprite {
    private readonly config: Required<Omit<SvgSpriteOptions, 'debug'>> & { debug: boolean };
    private readonly srcDir: string;
    private readonly debug: boolean;
    private readonly runner: PluginRunner;
    public outFile: string;
    public iconCount: number;
    public recompiled: boolean;
    private isInitialRun = true;

    constructor(options: SvgSpriteOptions, runner: PluginRunner) {
        this.config = {
            svgSource: 'resources/assets/svg',
            inputDir: 'sprite-icons',
            outputFile: 'sprite.svg',
            dontStripColor: [],
            icons: {},
            debug: options.debug ?? false,
            hotReloadTimeout: 1000,
            ...options,
        };

        this.srcDir = normalizePath(path.join(this.config.svgSource, this.config.inputDir));
        this.outFile = normalizePath(path.join(this.config.svgSource, this.config.outputFile));
        this.debug = this.config.debug;
        this.runner = runner;
        this.iconCount = 0;
        this.recompiled = false;
    }

    private shoulddontStripColor(relativePath: string): boolean {
        return this.config.dontStripColor.some((pattern) => matchPattern(relativePath, pattern));
    }

    private extractSvgAttributes(svgContent: string): SvgAttributes {
        const svgMatch = svgContent.match(/<svg\b([^>]*)>/i);
        if (!svgMatch) return {};

        const attributes = svgMatch[1] || '';

        const viewBox = attributes.match(/\bviewBox\s*=\s*["']([^"']+)["']/i)?.[1];
        const width = attributes.match(/\bwidth\s*=\s*["']?(\d+(?:\.\d+)?)["']?/i)?.[1];
        const height = attributes.match(/\bheight\s*=\s*["']?(\d+(?:\.\d+)?)["']?/i)?.[1];

        return { viewBox, width, height };
    }

    private generateViewBox(attributes: SvgAttributes): string | undefined {
        if (attributes.viewBox) return attributes.viewBox;
        if (attributes.width && attributes.height) {
            return `0 0 ${attributes.width} ${attributes.height}`;
        }
        return undefined;
    }

    private createSymbolId(relativePath: string): string {
        return relativePath
            .replace(/\.svg$/i, '')
            .replace(/[\\/]+/g, '-')
            .replace(/\s+/g, '_')
            .toLowerCase();
    }

    private forceCurrentColorRaw(svgContent: string): string {
        // Replace fill and stroke attributes with currentColor using regex
        // This handles: fill="#color", fill='#color', stroke="rgb(...)", etc.

        return (
            svgContent
                // Replace fill attributes (but preserve none, currentColor, url(), var())
                .replace(/\bfill\s*=\s*["']([^"']+)["']/gi, (match, value) => {
                    const v = value.trim().toLowerCase();
                    if (v === 'none' || v === 'currentcolor' || v.startsWith('url(') || v.startsWith('var(')) {
                        return match; // Keep as-is
                    }
                    // Check if it's a color value (hex, rgb, hsl, named color)
                    if (/^#([0-9a-f]{3,8})$/i.test(v) || /^(rgb|hsl)a?\(/i.test(v) || /^[a-z]+$/i.test(v)) {
                        return match.replace(value, 'currentColor');
                    }
                    return match;
                })
                // Replace stroke attributes (same logic as fill)
                .replace(/\bstroke\s*=\s*["']([^"']+)["']/gi, (match, value) => {
                    const v = value.trim().toLowerCase();
                    if (v === 'none' || v === 'currentcolor' || v.startsWith('url(') || v.startsWith('var(')) {
                        return match; // Keep as-is
                    }
                    // Check if it's a color value (hex, rgb, hsl, named color)
                    if (/^#([0-9a-f]{3,8})$/i.test(v) || /^(rgb|hsl)a?\(/i.test(v) || /^[a-z]+$/i.test(v)) {
                        return match.replace(value, 'currentColor');
                    }
                    return match;
                })
        );
    }

    private async processSvgFile(filePath: string, relativePath: string): Promise<ProcessedSymbol | null> {
        try {
            let raw = await fs.readFile(filePath, 'utf8');
            const symbolId = this.createSymbolId(relativePath);

            // Apply color conversion with raw string replacement if not skipped
            if (!this.shoulddontStripColor(relativePath)) {
                raw = this.forceCurrentColorRaw(raw);
            }

            const { data: optimizedSvg } = optimize(raw, {
                multipass: true,
                floatPrecision: 3,
                plugins: [
                    { name: 'preset-default' },
                    { name: 'prefixIds', params: { prefix: `${symbolId}-` } },
                ]
            });

            const attributes = this.extractSvgAttributes(optimizedSvg);
            const viewBox = this.generateViewBox(attributes);

            const innerContent = optimizedSvg.replace(/^[\s\S]*?<svg\b[^>]*>/i, '').replace(/<\/svg>\s*$/i, '');

            const content = viewBox
                ? `<symbol id="${symbolId}" viewBox="${viewBox}">${innerContent}</symbol>`
                : `<symbol id="${symbolId}">${innerContent}</symbol>`;

            return { id: symbolId, content };
        } catch (error) {
            logError(`SVG sprite: error processing ${relativePath}:`, error, this.debug);
            return null;
        }
    }

    private async validateSourceDirectory(): Promise<boolean> {
        try {
            await fs.access(this.srcDir);
            return true;
        } catch {
            logInfo(chalk.yellow(`SVG sprite source directory not found: ${this.srcDir}`));
            return false;
        }
    }

    private getSvgFiles(): string[] {
        try {
            const svgFiles = fg.sync(['**/*.svg'], { cwd: this.srcDir, absolute: true });
            debugLog('SVG sprite: scanned svg files', { count: svgFiles.length }, this.debug);
            return svgFiles.filter((file) => !file.includes(this.config.outputFile));
        } catch (e) {
            logError('SVG sprite: failed scanning svg files', e, this.debug);
            return [];
        }
    }

    private async processAllSvgFiles(svgFiles: string[]): Promise<ProcessedSymbol[]> {
        const symbolPromises = svgFiles.map(async (absolutePath) => {
            const relativePath = normalizePath(path.relative(this.srcDir, absolutePath));

            if (!relativePath || relativePath.includes('sprite')) {
                return null;
            }

            return this.processSvgFile(absolutePath, relativePath);
        });

        const symbols = await Promise.all(symbolPromises);
        return symbols.filter((symbol): symbol is ProcessedSymbol => symbol !== null);
    }

    private generateSpriteContent(symbols: ProcessedSymbol[]): string {
        return [
            '<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="position:absolute;width:0;height:0;overflow:hidden">',
            ...symbols.map((symbol) => symbol.content),
            '</svg>',
        ].join('');
    }

    private async writeSpriteFile(content: string, symbolCount: number): Promise<void> {
        this.recompiled = true;
        this.iconCount = symbolCount;
        await fs.writeFile(this.outFile, content, 'utf8');

        if (watchMode) {
            const publicFile = normalizePath(path.join(`public/front/svg/`, this.config.outputFile));
            await fs.writeFile(publicFile, content, 'utf8');
            debugLog('Public Sprite written:', publicFile, this.debug);
        }
    }

    public showCompileMessage(): void {
        const message = `compiled ${this.iconCount} symbols ${chalk.gray(`./${this.outFile}`)}`;
        this.runner.success(message);
    }

    public getSpriteFilePath(): string {
        return this.outFile;
    }

    public async generateSprite(): Promise<void> {
        try {
            this.runner.start(undefined, { isInitialRun: this.isInitialRun });
            // Validate preconditions
            if (!(await this.validateSourceDirectory())) return;

            // Get and validate SVG files by scanning directory
            const svgFiles = this.getSvgFiles();

            if (svgFiles.length === 0) {
                logInfo(chalk.yellow(`No SVG sprite files found in ${this.srcDir}`));
                return;
            }

            // Process all SVG files
            const symbols = await this.processAllSvgFiles(svgFiles);
            if (symbols.length === 0) {
                logInfo(chalk.yellow('No valid SVG symbols were generated'));
                return;
            }

            // Generate and write sprite
            const spriteContent = this.generateSpriteContent(symbols);
            await this.writeSpriteFile(spriteContent, symbols.length);

            if (this.recompiled) {
                this.showCompileMessage();
                this.recompiled = false;
            }
        } catch (error) {
            logError('SVG sprite generation failed:', error, this.debug);
            this.runner.error('generation failed');
        } finally {
            this.isInitialRun = false;
        }
    }
}

export function svgSpritePlugin(options: SvgSpriteOptions = {}): Plugin {
    const runner = createPluginRunner('SVG', 'SVG Sprite');
    const generator = new SvgSprite(options, runner);
    let serverTimeout: NodeJS.Timeout | null = null;
    const inputDir = options.inputDir ?? 'sprite-icons';
    const svgSourceRoot = normalizePath(path.join(options.svgSource ?? 'resources/assets/svg', inputDir));
    const spriteOutputPath = normalizePath(generator.getSpriteFilePath());

    return {
        name: 'svg-sprite',
        enforce: 'pre',

        async configResolved() {
            new SvgSprite(options, runner);
        },

        async buildStart() {
            if (watchMode) {
                getViteWatcher().addCallback((event) => {
                    if (event.file.match(/\.(svg)$/)) {
                        const eventPath = normalizePath(event.file);
                        if (eventPath === spriteOutputPath) {
                            return;
                        }

                        if (eventPath.startsWith(svgSourceRoot)) {
                            runner
                                .run(() => generator.generateSprite(), { skipInitialDelay: true })
                                .catch((error) => logError('SVG sprite watch error', error, options.debug ?? false));
                        }
                    }
                });
            }

            runner.run(() => generator.generateSprite());
        },

        async handleHotUpdate({ file, server }) {
            if (file.endsWith(generator.outFile)) {
                clearTimeout(serverTimeout);

                serverTimeout = setTimeout(async () => {
                    server.ws.send({
                        type: 'full-reload',
                        path: '*',
                    });
                }, options.hotReloadTimeout);
            }
        },
    };
}
