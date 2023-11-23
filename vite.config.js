import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/interactions.min.js',
                'resources/js/touch.min.js',
                'resources/js/widgets.min.js',
                'resources/js/jquery.fancybox.min.js',
                'resources/js/owl.carousel.min.js',
                'resources/js/app.js',
                'resources/css/icons/fontawesome/styles.min.css',
                'resources/css/icons/icomoon/styles.css',
                'resources/css/datatables.css',
                'resources/css/jquery.fancybox.min.css',
                'resources/css/owl.carousel.min.css',
                'resources/css/loader.css',
                'resources/css/app.css',
            ],
            refresh: true,
        }),
    ],
});
