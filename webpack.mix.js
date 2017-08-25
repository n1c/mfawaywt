const { mix } = require('laravel-mix');

mix.js('resources/assets/js/app.js', 'public/js').version();
mix.sass('resources/assets/sass/app.scss', 'public/css').version();

mix.disableNotifications();
