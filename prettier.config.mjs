/**
 * @see https://prettier.io/docs/configuration
 * @see https://github.com/shufo/prettier-plugin-blade
 * @type {import("prettier").Config}
 */

export default {
    arrowParens: 'always',
    bracketSpacing: true,
    experimentalOperatorPosition: 'end',
    htmlWhitespaceSensitivity: 'css',
    insertPragma: false,
    jsxSingleQuote: true,
    objectWrap: 'preserve',
    printWidth: 120,
    proseWrap: 'never',
    quoteProps: 'as-needed',
    requirePragma: false,
    semi: true,
    singleQuote: true,
    tabWidth: 4,
    trailingComma: 'es5',
    useTabs: false,

    plugins: [
        '@shufo/prettier-plugin-blade',
    ],
    overrides: [{
        files: ['*.blade.php'],
        options: {
            parser: 'blade',
            componentPrefix: 'x-',
            extraLiners: 'x-code-example',
            indentInnerHtml: false,
            noPhpSyntaxCheck: false,
            objectWrap: 'preserve',
            phpVersion: '8.4',
            printWidth: 999,
            sortHtmlAttributes: 'idiomatic',
            sortTailwindcssClasses: true,
            tabWidth: 4,
            wrapAttributes: 'force-expand-multiline',
            wrapAttributesMinAttrs: 3,
        },
    }],
};
