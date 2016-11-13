const elixir = require('laravel-elixir');

require('laravel-elixir-vue');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(mix => {

    // App
    mix.sass('app.scss');
    mix.webpack('app.js');

    // Landing
    mix.sass('landing.scss');
    mix.webpack('landing.js');

    // Node Package Styles
    mix.styles([
        'jquery-ui/themes/base/datepicker.css',
        'animate.css/animate.min.css',
        'dragula/dist/dragula.css'
    ], 'public/css/vendor.css', 'node_modules');


    mix.copy('node_modules/font-awesome/fonts', 'public/fonts');

    mix.browserSync({proxy: 'app.ddocs.dev'});
});
