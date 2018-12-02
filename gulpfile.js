var gulp = require('gulp');
var minify = require('gulp-minify');
var concat = require('gulp-concat');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var sourcemaps = require('gulp-sourcemaps');

var path= {
    baseDir: '',
    cssDir: 'css/',
}


//minify js
gulp.task('minify_js', function() {
  gulp.src(path.baseDir+'src/js/*.js')
    .pipe(minify({
        ext:{
            src:'-debug.js',
            min:'.js'
        },
        exclude: ['tasks'],
        ignoreFiles: ['.combo.js', '-min.js']
    }))
    .pipe(gulp.dest(path.baseDir+'js/'));
});

//scss
gulp.task('sass', function () {
  return gulp.src(path.baseDir+'src/scss/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
    .pipe(autoprefixer(['last 4 versions', '> 1%', 'ie 8', 'ie 7'], {cascade: false}))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(path.cssDir));
});

//all task
gulp.task('all:watch', ['sass', 'minify_js'], function(){
    setTimeout(function(){
        gulp.watch(path.baseDir+'src/scss/*.scss', ['sass']);
        gulp.watch(path.baseDir+'src/js/*.js', ['minify_js']);
    }, 200)
});