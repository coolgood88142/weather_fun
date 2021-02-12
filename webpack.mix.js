const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
   .js('resources/js/taiwan.js', 'public/js')
   .js('resources/js/stock.js', 'public/js')
   .js('resources/js/d3.v3.min.js', 'public/js')
   .js('resources/js/bootstrap-datetimepicker.min.js', 'public/js')
    .sass('resources/sass/stock.scss', 'public/css')
    .sass('resources/sass/datepicker.scss', 'public/css')
    .sass('resources/sass/app.scss', 'public/css');
