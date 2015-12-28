'use strict';

const path    = require('path');
const gulp    = require('gulp');
const filter  = require('gulp-filter');
const replace = require('gulp-replace');
const semver  = require('semver');
const pkg     = require('./package.json');

/**
 * Creates bump task.
 *
 * @param {string} type a semver type.
 *
 * @return {Function} A bump task.
 */
function bumpTaskFactory(type) {
    return () => {
        const packages       = ['composer.json', 'package.json'];
        const version        = pkg.version;
        const newVersion     = semver.inc(version, type);
        const versionPattern = (version + '').replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
        const packageRegExp  = new RegExp('^(\\s*"version"\\s*:)\\s*"' + versionPattern + '"\\s*(,?)\\s*$', 'mg');

        gulp.src(packages, {base: './'})
            .pipe(replace(packageRegExp, '$1 "' + newVersion + '"$2'))
            .pipe(gulp.dest('./'));
    };
}

gulp.task('bump', ['bump:patch']);

gulp.task('bump:patch', bumpTaskFactory('patch'));

gulp.task('bump:minor', bumpTaskFactory('minor'));

gulp.task('bump:major', bumpTaskFactory('major'));

gulp.task('bump:prerelease', bumpTaskFactory('prerelease'));
