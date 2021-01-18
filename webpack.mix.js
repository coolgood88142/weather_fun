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
   .js('resources/js/augurio-taiwan.js', 'public/js')
   .js('resources/js/augurio-taiwan.min.js', 'public/js')
   .js('resources/js/taiwan.js', 'public/js')
    // .sass('resources/sass/augurio-taiwan.scss', 'public/css')
    .sass('resources/sass/app.scss', 'public/css');