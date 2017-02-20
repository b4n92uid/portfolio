
const gulp = require('gulp');

const $ = require('gulp-load-plugins')();

const merge = require('merge-stream');
const argv = require('yargs').argv;
const fs = require('fs');

const WP_HEADER = [
  '/**',
  ' * Theme Name: b4n92uid Portfolio',
  ' * Description: Beldjouhri Abdelghani Portfolio Theme',
  ' * Author: Beldjouhri Abdelghani <b4n92uid@gmail.com>',
  '*/',

].join('\n');

var config = {
  jsapp: [
    'src/script.js'
  ],

  cssapp: [
    'src/style.scss'
  ],

  build: {
    css   : '.',
    js    : '.',
  },
}

gulp.task('deploy:js', function() {

  var filterJS = $.filter('**/*.js', { restore: true });

  var vendor = gulp.src('./bower.json')
    .pipe($.mainBowerFiles())
    .pipe(filterJS)

  var app = gulp.src(config.jsapp);

  return merge(app, vendor)
    .pipe($.order([
      'bower_components/jquery/dist/jquery.js',
      'bower_components/**/*.*',
      'js/main.js',
    ], { base: './' }))
    .pipe($.debug())
    .pipe($.sourcemaps.init({identityMap: true, debug: true}))
      .pipe($.uglify())
      .pipe($.concat('script.js'))
    .pipe($.sourcemaps.write('.'))
    .pipe(gulp.dest(config.build.js))
    .pipe($.livereload())
    .pipe($.notify({message:'JS Deployed !', onLast: true}))
});

gulp.task('modernizr', function() {
  gulp.src(config.build.js + '/script.min.js')
    .pipe($.modernizr({
      "options": ['mq']
    }))
    .pipe(gulp.dest(config.build.js))
});

gulp.task('deploy:css', function() {

  var filterCSS = $.filter(['**/*.css'], { restore: true });

  var vendor = gulp.src('./bower.json')
    .pipe($.mainBowerFiles())
    .pipe(filterCSS);

  var app = gulp.src(config.cssapp)
    .pipe($.plumber(function(error) {
        $.notify().write(error);
        this.emit('end');
    }))
    .pipe($.sass({outputStyle: 'compressed'}))
    .pipe($.autoprefixer({
      browsers: ['last 2 versions'],
      cascade: false
    }))

  return merge(app, vendor)
    .pipe($.order([
      'bower_components/**/*.*',
      'scss/*',
    ], { base: './' }))
    .pipe($.debug())
    .pipe($.uglifycss())
    .pipe($.concat('style.css'))
    .pipe($.header(WP_HEADER))
    .pipe(gulp.dest(config.build.css))
    .pipe($.livereload())
    .pipe($.notify({message:'CSS Deployed !', onLast: true}))
});


gulp.task('build', ['deploy:js', 'deploy:css']);

gulp.task('default', ['build'], function() {
  $.livereload.listen();

  $.watch(['bower.json'], function() {
    gulp.start('build');
  })

  $.watch(['src/*.scss'], function() {
    gulp.start('deploy:css');
  })

  $.watch(['src/*.js'], function() {
    gulp.start('deploy:js');
  })
});

