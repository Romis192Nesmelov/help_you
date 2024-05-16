import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        vue(),
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
                'resources/css/jquery.fancybox.min.css',
                'resources/css/owl.carousel.min.css',
                'resources/css/loader.css',
                'resources/css/app.css',

                'resources/js/admin/jquery.min.js',
                'resources/js/admin/bootstrap.min.js',
                'resources/js/admin/select2.min.js',
                'resources/js/admin/styling/uniform.min.js',
                'resources/js/admin/styling/switchery.min.js',
                'resources/js/admin/styling/bootstrap-switch.js',
                'resources/js/admin/styling/bootstrap-toggle.min.js',
                'resources/js/admin/app.js',
                'resources/js/admin/jquery.maskedinput.js',
                'resources/js/admin/login.js',
                'resources/js/admin/admin.js',

                'resources/css/admin/bootstrap.css',
                'resources/css/admin/bootstrap-switch.css',
                'resources/css/admin/bootstrap-toggle.min.css',
                'resources/css/admin/colors.css',
                'resources/css/admin/core.css',
                'resources/css/admin/components.css',
                'resources/css/admin/admin.css',
            ],
            refresh: true,
        }),
    ],
});
