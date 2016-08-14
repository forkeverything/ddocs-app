var elixir = require('laravel-elixir');

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

elixir(function(mix) {

    // Landing
        // Styles
            // Packages
            mix.styles([
                // Packages
                'font-awesome/css/font-awesome.min.css',
                'jquery-ui/themes/base/datepicker.css',
            ], 'resources/assets/css/landing/vendor.css', 'resources/assets/bower');
            // Main
            mix.sass('landing.scss', 'resources/assets/css/landing/main.css');
            // Combined
            mix.stylesIn('resources/assets/css/landing', 'public/css/landing.css');
        // Scripts
            // Packages
            mix.scripts([
                'jquery/dist/jquery.js',
                'bootstrap-sass/assets/javascripts/bootstrap.min.js',
                'vue/dist/vue.js',
                'lodash/lodash.js',
                'moment/min/moment-with-locales.min.js',
                'jquery-ui/ui/widgets/datepicker.js',
                'autosize/dist/autosize.min.js'
                // 'js-cookie/src/js.cookie.js',
                // 'toastr/toastr.js',
                // 'accounting.js/accounting.min.js'
            ], 'public/js/landing/vendor.js', 'resources/assets/bower');
            // Dependencies
            mix.scripts([
                'ajax.js',
                'autosize.js',
                'helpers.js',
                'tooltip.js',
                'vue/directives/**/*.js',
                'vue/filters/**/*.js',
                'vue/components/form-errors.js',
                'ga-tracker.js'
            ], 'public/js/landing/dependencies.js', 'resources/assets/js/dependencies');
            // Main
            mix.scriptsIn('resources/assets/js/landing', 'public/js/landing/main.js');

    // App
        // Styles
            // Main
                mix.sass('app.scss');
            // Packages
            mix.styles([
                // App
                '../../../public/css/app.css',
                // Packages
                'font-awesome/css/font-awesome.min.css',
                'jquery-ui/themes/base/datepicker.css',
                // Resource folder
                '../../../resources/assets/css/*.css'
            ], 'public/css/all.css', 'resources/assets/bower');
        // Scripts
            // Packages
            mix.scripts([
                'jquery/dist/jquery.js',
                'bootstrap-sass/assets/javascripts/bootstrap.min.js',
                'vue/dist/vue.js',
                'lodash/lodash.js',
                'moment/min/moment-with-locales.min.js',
                'jquery-ui/ui/widgets/datepicker.js',
                'autosize/dist/autosize.min.js',
                // 'js-cookie/src/js.cookie.js',
                // 'toastr/toastr.js',
                // 'accounting.js/accounting.min.js'
            ], 'public/js/app/vendor.js', 'resources/assets/bower');
            // Dependencies
            mix.scriptsIn('resources/assets/js/dependencies', 'public/js/app/dependencies.js');
            // Pages
            mix.scriptsIn('resources/assets/js/app/pages', 'public/js/app/pages.js');
            // root
            mix.copy('resources/assets/js/app/vue-root.js', 'public/js/app/root.js');

    // All
    mix.copy('resources/assets/bower/font-awesome/fonts', 'public/fonts');

    // BrowserSync - Gulp watch
    mix.browserSync({proxy: 'filescollector.app'});

    // mix.phpUnit(null, {group: 'driven'});

});
