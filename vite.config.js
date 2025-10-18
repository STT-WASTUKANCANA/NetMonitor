import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: [
                // Frontend assets
                'resources/css/**',
                'resources/js/**',
                'resources/sass/**',
                'resources/scss/**',
                
                // Blade templates
                'resources/views/**',
                
                // Laravel specific files that might affect views
                'app/Http/**',
                'routes/**', 
                'app/Models/**',
                'app/Services/**',
                'app/View/Components/**',
                'lang/**',
                
                // Config files that might affect the view
                'config/**',
            ],
        }),
    ],
    server: {
        host: true,
        hmr: {
            host: 'localhost',
        },
    }
});
