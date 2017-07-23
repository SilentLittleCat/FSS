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

// Custom styles
mix.sass('resources/assets/sass/styles.scss', 'public/css');

// Admin-lte
mix.copyDirectory('node_modules/admin-lte/dist', 'public/vendor/admin-lte/dist')

// Bootstrap
   .copyDirectory('node_modules/admin-lte/bootstrap', 'public/vendor/admin-lte/bootstrap')

// jquery
   .copy('node_modules/admin-lte/plugins/jQuery/jquery-2.2.3.min.js', 'public/vendor/admin-lte/plugins/jQuery/jquery-2.2.3.min.js')

// Bootstrap-wysihtml5
   .copy('node_modules/admin-lte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css', 'public/vendor/admin-lte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css')

// Select2
   .copy('node_modules/admin-lte/plugins/select2/select2.full.min.js', 'public/vendor/admin-lte/plugins/select2/select2.full.min.js')
   .copy('node_modules/admin-lte/plugins/select2/select2.min.css', 'public/vendor/admin-lte/plugins/select2/select2.min.css')

// Bootstrap-colorpicker
   .copy('node_modules/admin-lte/plugins/colorpicker/bootstrap-colorpicker.min.css', 'public/vendor/admin-lte/plugins/colorpicker/bootstrap-colorpicker.min.css')
   .copy('node_modules/admin-lte/plugins/colorpicker/bootstrap-colorpicker.min.js', 'public/vendor/admin-lte/plugins/colorpicker/bootstrap-colorpicker.min.js')
   .copyDirectory('node_modules/admin-lte/plugins/colorpicker/img', 'public/vendor/admin-lte/plugins/colorpicker/img')

// iCheck
   .copyDirectory('node_modules/admin-lte/plugins/iCheck', 'public/vendor/admin-lte/plugins/iCheck')

// Datatables
   .copyDirectory('node_modules/admin-lte/plugins/datatables/images', 'public/vendor/admin-lte/plugins/datatables/images')
   .copy('node_modules/admin-lte/plugins/datatables/dataTables.bootstrap.css', 'public/vendor/admin-lte/plugins/datatables/dataTables.bootstrap.css')
   .copy('node_modules/admin-lte/plugins/datatables/dataTables.bootstrap.js', 'public/vendor/admin-lte/plugins/datatables/dataTables.bootstrap.js')
   .copy('node_modules/admin-lte/plugins/datatables/jquery.dataTables.min.js', 'public/vendor/admin-lte/plugins/datatables/jquery.dataTables.min.js');

// jquery-ui
mix.copy('node_modules/jquery-ui-dist/jquery-ui.min.css', 'public/vendor/jquery-ui-dist/jquery-ui.min.css')
   .copy('node_modules/jquery-ui-dist/jquery-ui.js', 'public/vendor/jquery-ui-dist/jquery-ui.js')
   .copyDirectory('node_modules/jquery-ui-dist/images', 'public/vendor/jquery-ui-dist/images');

// fine-uploader
mix.copy('node_modules/fine-uploader/fine-uploader/fine-uploader-new.min.css', 'public/vendor/fine-uploader/fine-uploader-new.min.css')
   .copy('node_modules/fine-uploader/fine-uploader/fine-uploader.min.js', 'public/vendor/fine-uploader/fine-uploader.min.js')
   .copy('node_modules/fine-uploader/fine-uploader/templates/simple-thumbnails.html', 'public/vendor/fine-uploader/templates/simple-thumbnails.html')
   .copy('node_modules/fine-uploader/fine-uploader/continue.gif', 'public/vendor/fine-uploader/continue.gif')
   .copy('node_modules/fine-uploader/fine-uploader/edit.gif', 'public/vendor/fine-uploader/edit.gif')
   .copy('node_modules/fine-uploader/fine-uploader/loading.gif', 'public/vendor/fine-uploader/loading.gif')
   .copy('node_modules/fine-uploader/fine-uploader/pause.gif', 'public/vendor/fine-uploader/pause.gif')
   .copy('node_modules/fine-uploader/fine-uploader/processing.gif', 'public/vendor/fine-uploader/processing.gif')
   .copy('node_modules/fine-uploader/fine-uploader/retry.gif', 'public/vendor/fine-uploader/retry.gif')
   .copy('node_modules/fine-uploader/fine-uploader/trash.gif', 'public/vendor/fine-uploader/trash.gif')
   .copyDirectory('node_modules/fine-uploader/fine-uploader/placeholders', 'public/vendor/fine-uploader/placeholders');

// Font-awesome
mix.copy('resources/assets/bower/font-awesome/css/font-awesome.min.css', 'public/vendor/font-awesome/css/font-awesome.min.css')
   .copyDirectory('resources/assets/bower/font-awesome/fonts', 'public/vendor/font-awesome/fonts');

// handsontable
mix.copy('node_modules/handsontable/dist/handsontable.full.css', 'public/vendor/handsontable/dist/handsontable.full.css')
   .copy('node_modules/handsontable/dist/handsontable.full.js', 'public/vendor/handsontable/dist/handsontable.full.js');

// spectrum
mix.copy('node_modules/spectrum-colorpicker/spectrum.css', 'public/vendor/spectrum-colorpicker/spectrum.css')
   .copy('node_modules/spectrum-colorpicker/spectrum.js', 'public/vendor/spectrum-colorpicker/spectrum.js');

mix.browserSync({
    open: 'external',
    host: 'fss.app',
    proxy: 'fss.app',
    files: ['resources/views/**/*.php', 'resources/assets/**/*.js', 'resources/assets/**/*.scss', 'app/**/*.php', 'routes/**/*.php']
});