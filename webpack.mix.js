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
    .react()
    .sass('resources/sass/app.scss', 'public/css');



const WebpackShellPluginNext = require('webpack-shell-plugin-next');

// Add shell command plugin configured to create JavaScript language file
mix.webpackConfig({
    plugins:
        [
            new WebpackShellPluginNext({
                onBuildStart: {
                    scripts: ['php artisan lang:js resources/js/translations.js --no-lib --quiet -c']
                },
                onBuildEnd: {
                    scripts: []
                }
            }),
        ]
});
