import chalk from 'chalk';
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/filament/admin/theme.css'],
        }),
        {
            name: 'Development',
            configResolved() {
                console.clear();
                console.log(chalk.gray(`ADMIN`, process.env.NODE_ENV.toUpperCase()));
            },
            async closeBundle() {
                console.log('\n');
            },
        },
    ],
});
