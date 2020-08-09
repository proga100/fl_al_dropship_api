var gulp = require('gulp');
var lessToScss = require('gulp-less-to-scss');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
const browserSync = require('browser-sync').create();
const watch = require('gulp-watch');
const cssmin = require('gulp-cssmin');
var sourcemaps = require('gulp-sourcemaps');
var livereload = require('gulp-livereload');

gulp.task('serve', function () {
    "use strict";
    browserSync.init({
        proxy: "alie-calls",
        host: "192.168.0.110",
        port: 3000,
        notify: true,
        ui: {
            port: 3001
        },
        open: true
    });
});

gulp.task('watch', function () {

    livereload.listen();

    watch('./assets/scss/*.scss').on('change', (e) => {
        livereload.listen();
        gulp.src('./assets/scss/*.scss')
            .pipe(sass().on('error', sass.logError))
            .pipe(autoprefixer())
            .pipe(cssmin())
            .pipe(gulp.dest('./assets/css'))
            .pipe(browserSync.stream())
            .pipe(livereload());
        browserSync.reload();
    });

});

gulp.task('default', gulp.series(gulp.parallel('watch', 'serve')));