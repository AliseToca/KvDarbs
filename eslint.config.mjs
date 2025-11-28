import js from '@eslint/js';
import tseslint from 'typescript-eslint';
import prettier from 'prettier';
import globals from "globals";

export default [
    {
        ignores: [
            '*.min.js',
            'app/**',
            'bootstrap/**',
            'build/**',
            'dist/**',
            'lang/**',
            'node_modules/**',
            'packages/**',
            'public/**',
            'routes/**',
            'storage/**',
            'tests/**',
            'vendor/**',
            'vite.config.ts',
        ],
    },
    js.configs.recommended,
    ...tseslint.configs.recommended,
    ...tseslint.configs.stylistic,
    {
        files: ['resources/assets/js/**/*.{ts,js}'],
        languageOptions: {
            parser: tseslint.parser,
            parserOptions: {
                ecmaVersion: 'latest',
                sourceType: 'module',
                projectService: true,
                tsconfigRootDir: import.meta.dirname,
                globals: {
                    ...globals.browser,
                    ...globals.node
                },
            },
        },
        plugins: {
            '@typescript-eslint': tseslint.plugin,
            'prettier': prettier,
        },
        rules: {
            '@typescript-eslint/no-unused-vars': ['warn', { argsIgnorePattern: '^_', varsIgnorePattern: '^_' }],
        },
    },
];
