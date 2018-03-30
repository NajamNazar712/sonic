let mix = require('laravel-mix');

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

// mix.js('resources/assets/js/app.js', 'public/js')
//    .sass('resources/assets/sass/app.scss', 'public/css');
mix.js('resources/assets/js/app.js', 'public/js')
.combine([
	'resources/assets/app-assets/vendors/js/vendors.min.js',
	'resources/assets/app-assets/vendors/js/charts/chart.min.js',
	'resources/assets/app-assets/js/core/app-menu.js',
	'resources/assets/app-assets/js/core/app.js',
	'resources/assets/app-assets/js/scripts/customizer.js',
	'resources/assets/app-assets/js/scripts/pages/dashboard-crypto.js'
	], 'public/js/all-js.js');

mix.sass('resources/assets/sass/app.scss', 'public/css')
	.combine([
		'resources/assets/app-assets/css/vendors.css',
		'resources/assets/app-assets/css/app.css',
		'resources/assets/app-assets/css/core/menu/menu-types/vertical-overlay-menu.css',
		'resources/assets/app-assets/css/core/colors/palette-gradient.css',
		'resources/assets/app-assets/vendors/css/cryptocoins/cryptocoins.css',
		'resources/assets/assets/css/style.css'
		], 'public/css/all-css.css');
