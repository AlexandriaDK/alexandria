var color = require('ansi-colors'),
    gulp = require('gulp'),
    autoprefixer = require('gulp-autoprefixer'),
    minify_css = require('gulp-clean-css'),
    concat = require('gulp-concat'),
    importfixer = require('gulp-cssimport'),
    jshint = require('gulp-jshint'),
    minify_js = require('gulp-minify'),
    plumber = require('gulp-plumber'),
    rename = require('gulp-rename'),
    sass = require('gulp-dart-sass'),
    sourcemaps = require('gulp-sourcemaps'),
    uglify = require('gulp-uglify'),
    imagemin = require('gulp-imagemin'),
    del = require('del');

function done(cb) {
  console.log("Finished");
  //cb();
}

gulp.task('copy-webfonts', function() {
return gulp.src([
  'src/vendors/Font-Awesome-Pro/webfonts/*-regular-*',
  'src/vendors/Font-Awesome-Pro/webfonts/*-solid-*',
  'src/vendors/Font-Awesome-Pro/webfonts/*-brands-*'
  ])
  .pipe(gulp.dest('./assets/webfonts/'));
});

gulp.task('optimise-images', function() {
	return gulp.src('src/images/**/*')
      .pipe(imagemin())
      .pipe(gulp.dest('www/assets/images'));
});

gulp.task('compile-frontend-css', function() {
return gulp.src('src/sass/bastardcafe.scss')
  .pipe(sourcemaps.init())
  .pipe(sass())
  .pipe(autoprefixer())
  .pipe(rename({suffix: '.min'}))
  .pipe(minify_css({compatibility: 'ie8'}))
  .pipe(sourcemaps.write())
  .pipe(gulp.dest('./assets/css/'));
});

gulp.task('compile-editor-css', function() {
return gulp.src('assets/src/sass/editor.scss')
  .pipe(sourcemaps.init())
  .pipe(sass())
  .pipe(autoprefixer())
  .pipe(rename({suffix: '.min'}))
  .pipe(minify_css({compatibility: 'ie8'}))
  .pipe(sourcemaps.write())
  .pipe(gulp.dest('www/assets/css/'));
});

gulp.task('compile-editor-css', function() {
return gulp.src('assets/src/sass/editor.scss')
  .pipe(sourcemaps.init())
  .pipe(sass())
  .pipe(autoprefixer())
  .pipe(rename({suffix: '.min'}))
  .pipe(minify_css({compatibility: 'ie8'}))
  .pipe(sourcemaps.write())
  .pipe(gulp.dest('./assets/css/'));
});

gulp.task('compile-admin-css', function() {
return gulp.src('src/sass/admin.scss')
  .pipe(sourcemaps.init())
  .pipe(sass())
  .pipe(autoprefixer())
  .pipe(rename({suffix: '.min'}))
  .pipe(minify_css({compatibility: 'ie8'}))
  .pipe(sourcemaps.write())
  .pipe(gulp.dest('www/assets/css/'));
});

gulp.task('compile-js', function() {
return gulp.src([
  'src/vendors/bootstrap/dist/js/bootstrap.bundle.min.js',
  // moving on...
  'assets/src/js/_*.js'])								// Gets all the user JS _*.js from assets/src/js/administration
  .on('error', function (error) {
    console.log(color.red( error.message ));
  })  // Handle JS errors
  .pipe(concat('scripts.js'))
  .pipe(rename({suffix: ''}))
  .pipe(sourcemaps.write())
  .pipe(minify_js({
    noSource: true,
    ext:{
      min:'.min.js'
    }
  }))
  .pipe(gulp.dest('assets/js/'));
});

// configure which files to watch and what tasks to use on file changes
gulp.task('watch', function() {
  gulp.watch('src/js/**/*.js', gulp.series('compile-js'));
  gulp.watch('src/sass/**/*.scss', gulp.series('compile-editor-css'));
  gulp.watch('src/sass/**/*.scss', gulp.series('compile-admin-css'));
  gulp.watch('src/sass/**/*.scss', gulp.series('compile-frontend-css'));
  gulp.watch('src/images/**/*', gulp.series('optimise-images'));
});

// define the default task and add the watch task to it
gulp.task('default',
  gulp.series( 'copy-webfonts', 'compile-frontend-css', 'compile-editor-css', 'compile-admin-css', 'compile-js', 'watch' )
);
gulp.task('compile',
  gulp.series( 'copy-webfonts', 'compile-frontend-css', 'compile-editor-css', 'compile-admin-css', 'compile-js' )
);
