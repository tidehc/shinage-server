var gulp = require('gulp');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var clean_css = require('gulp-clean-css');
var environments = require('gulp-environments');
var sourcemaps = require('gulp-sourcemaps');
var del = require('del');

var development = environments.development;
//var production = environments.production;

var distPath = 'web/assets/';
var elfinderDistPath = 'web/vendor/studio-42/elfinder';
var semanticDistPath = 'web/semantic-ui-css';
var jqueryUiDistPath = 'web/jquery-ui-dist';
var paths = {
    js: [
        'node_modules/jquery/dist/jquery.js',
        'node_modules/jquery-ui-dist/jquery-ui.js',
        'node_modules/semantic-ui-css/semantic.js',
        'node_modules/moment/min/moment-with-locales.js',
        'node_modules/fullcalendar/dist/fullcalendar.js',
        'node_modules/fullcalendar/dist/locale-all.js',
        'vendor/studio-42/elfinder/js/elfinder.full.js',
    ],
    css: [
        'node_modules/reset-css/reset.css',
        'node_modules/semantic-ui-css/semantic.css',
        'node_modules/jquery-ui-dist/jquery-ui.css',
        'node_modules/jquery-ui-dist/jquery-ui.theme.css',
        'node_modules/fullcalendar/dist/fullcalendar.css',
        'vendor/studio-42/elfinder/css/elfinder.full.css',
        'vendor/studio-42/elfinder/css/theme.css',
        'src/AppBundle/Resources/private/css/**',
    ],
    less: [
    ],
};

gulp.task('clean', function() {
    return del([distPath]);
});

gulp.task('copy', function() {
    gulp.src('node_modules/semantic-ui-css/themes/**')
        .pipe(gulp.dest(semanticDistPath + '/themes/'));
    gulp.src('vendor/studio-42/elfinder/img/**')
        .pipe(gulp.dest(elfinderDistPath + '/img/'));
    gulp.src('node_modules/jquery-ui-dist/images/**')
        .pipe(gulp.dest(jqueryUiDistPath + '/images/'));
});

gulp.task('css', ['clean'], function() {
    return gulp.src(paths.css)
        .pipe(development(sourcemaps.init()))
        .pipe(clean_css())
        .pipe(concat('all.min.css'))
        .pipe(development(sourcemaps.write()))
        .pipe(gulp.dest(distPath));
});

gulp.task('js', ['clean'], function() {
    return gulp.src(paths.js)
        .pipe(development(sourcemaps.init()))
        .pipe(uglify())
        .pipe(concat('all.min.js'))
        .pipe(development(sourcemaps.write()))
        .pipe(gulp.dest(distPath));
});

gulp.task('default', ['copy', 'css', 'js']);


/*
// Rerun the task when a file changes
gulp.task('watch', function() {
  gulp.watch(paths.scripts, ['scripts']);
  gulp.watch(paths.images, ['images']);
});
*/