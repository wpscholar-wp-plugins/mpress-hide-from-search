'use strict';

var gulp = require('gulp');
var shell = require('gulp-shell');
var plumber = require('gulp-plumber');
var clean = require('del');
var sound = require('mac-sounds');

var config = {
    svn: {
        src: [
            './**',
            '!**/svn',
            '!**/svn/**',
            '!**/package.json',
            '!**/node_modules',
            '!**/node_modules/**',
            '!**/bower.json',
            '!**/bower_components',
            '!**/bower_components/**',
            '!**/gulpfile.js',
            '!**/gulp',
            '!**/gulp/**'
        ],
        dest: './svn/trunk',
        clean: './svn/trunk/**/*'
    }
};

gulp.task('svn:checkout', shell.task('svn co http://plugins.svn.wordpress.org/mpress-hide-from-search/ svn'));

gulp.task('svn:clean', function () {
    return clean(config.svn.clean);
});

gulp.task('svn:build', ['svn:clean'], function () {
    return gulp.src(config.svn.src)
        .pipe(plumber({
            errorHandler: function (err) {
                sound('blow');
                console.log(err);
            }
        }))
        .pipe(gulp.dest(config.svn.dest));
});