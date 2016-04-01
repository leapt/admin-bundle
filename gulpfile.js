// Common modules
var _ = require('underscore');
var gulp = require('gulp');
var path = require('path');
var yaml = require('js-yaml')
var fs = require('fs');
var merge = require('merge-stream');
var mainBowerFiles = require('main-bower-files');
var runSequence = require('run-sequence');
var del = require('del');
var cssSprite = require('css-sprite').stream;
var shortId = require('shortid');
var $ = require('gulp-load-plugins')();
// Initialize the config
var config = {};
var server = $.util.noop;
var reload = $.util.noop;
var onlyNewer = false;

var loadConfig = function () {
    var config = yaml.safeLoad(fs.readFileSync('gulpconfig.yml', 'utf8'));
    var configTypes = ['assets', 'copies', 'sprites'];
    var pathTypes = ['files', 'watch', 'lint'];

    // Loop on each config type that could have a src path to join to the file
    _.each(configTypes, function (type) {
        // Check if a config exists for that type
        if (!_.isUndefined(config[type]) && _.isArray(config[type])) {
            // Loop on each config of that type
            _.each(config[type], function (part, partIndex) {
                // Loop on each paths type that could have a src path to join on
                _.each(pathTypes, function (pathType) {
                    // Check if a path exists for that type
                    if (!_.isUndefined(part[pathType]) && _.isArray(part[pathType])) {
                        // Store the files as default filters to apply on each src when needed
                        if (pathType === 'files') {
                            config[type][partIndex]['filters'] = [];
                            _.each(part[pathType], function (glob) {
                                config[type][partIndex]['filters'].push(path.basename(glob));
                            });
                        }
                        // Loop on each config path type
                        _.each(part[pathType], function (file, fileIndex) {
                            // Join the src to the file, so we don't have to full path on each file
                            config[type][partIndex][pathType][fileIndex] = path.join(part.src, file);
                        });
                    }

                });
            });
        }

    });
    // Cache bower files and directory
    config.bower = {};
    config.bower.src = getBowerDirectory();
    config.bower.files = mainBowerFiles();

    // Cache server and server reload methods
    if (!_.isUndefined(config.server)) {
        if (!_.isUndefined(config.server.plugin)) {
            switch (config.server.plugin) {
                case 'livereload':
                    server = $.livereload;
                    reload = server;
                    break;
                case 'browser-sync':
                    server = require('browser-sync');
                    reload = server.reload;
                    break;
            }
        }
    }

    $.util.log('Configuration file loaded', $.util.colors.magenta('gulpconfig.yml'));

    return config;
};

var getBowerDirectory = function () {
    var bowerrc = path.join(process.cwd(), '.bowerrc'),
        directory = './bower_components',
        bower_config
        ;

    try {
        bower_config = JSON.parse(fs.readFileSync(bowerrc));
        directory = bower_config.directory;
    } catch (ignore) {
        // do nothing, just use default directory instead
    }

    fs.mkdir(path.join(process.cwd(), directory), function () {
        // do nothing, the directory is already there
    });

    $.util.log('Getting bower directory', $.util.colors.magenta(directory));

    return directory;
};

try {
    config = loadConfig();
} catch (error) {
    $.util.log($.util.colors.red('The config file is not a valid yaml file'), error);
    return;
}

/**
 * Listener callback when a error is triggered
 *
 * @param error
 */
var onError = function (error) {
    $.util.beep();
    $.util.log($.util.colors.red(error));
};

// Install bower package if any
gulp.task("bower-install", function () {
    return $.bower().pipe($.size({title: 'bower-install'}));
});

// Copy all bower files to the vendor directory
gulp.task("bower-main", ["bower-install"], function () {
    // Loop on all bower.json main files with the %config.bower% (should match your .bowerrc file) to
    // avoid flatten directory structure in the destination
    return gulp.src(config.bower.files, {base: config.bower.src})
        .pipe($.if(onlyNewer, $.newer(path.join(config.dest, 'vendor'))))
        // Copy all files declared in the "main" settings of each bower.json dependency
        .pipe(gulp.dest(path.join(config.dest, 'vendor')))
        // Minify all css for production files
        .pipe($.if('*.css', $.csso()))
        // Uglify all js for production files
        .pipe($.if('*.js', $.uglify()))
        // Trigger a server reload
        .pipe(config.server.plugin === 'browser-sync' ? reload({stream: true}) : reload())
        // Append the production suffix on the filename
        .pipe($.rename({suffix: config.productionSuffix}))
        // Save the optimized files in %config.dest%/vendor
        .pipe(gulp.dest(path.join(config.dest, 'vendor')))
        // Display the size of the generated bower packages
        .pipe($.size({title: 'bower-main'}))
        ;
});

// Copy all images and optimize them
gulp.task('images', function () {
    var streams = [];
    // Loop on each asset where %asset.type% is "image"
    _.each(_.where(config.assets, {type: 'images'}), function (asset) {
        // Get all %asset.files% and append them the %asset.src% so
        // we don't need to specify the full path for each glob
        streams.push(
            gulp.src(asset.files)
                // Cache all files content to add only files who changed to the stream
                .pipe($.if(onlyNewer, $.newer(path.join(config.dest, 'images'))))
                // Run imagemin to optimize the images which are not cached or are deprecated
                .pipe($.imagemin(asset.options || {}))
                // TODO : uncomment the line below if you want to flatten the images directory structure
                //.pipe($.flatten())
                // Save the optimized files in %config.dest%/images
                .pipe(gulp.dest(path.join(config.dest, 'images')))
                // Display the size of generated images
                .pipe($.size({title: 'images'}))
                .pipe(config.server.plugin === 'browser-sync' ? reload({stream: true}) : reload())
        );
    });

    if (streams.length > 0) {
        return merge(streams);
    }
});

// Generate sprites
gulp.task('sprites', function () {
    var streams = [];
    if (!_.isUndefined(config.sprites) && _.isArray(config.sprites)) {
        _.each(config.sprites, function (sprite) {
            streams.push(
                gulp.src(sprite.files)
                    .pipe($.if(onlyNewer, $.newer(sprite.spriteDest)))
                    // Call the sprite method
                    .pipe(cssSprite(sprite.options || {}))
                    // Save the sprite and the style file in the destination of your choice
                    .pipe($.if('*.png', gulp.dest(sprite.spriteDest), gulp.dest(sprite.styleDest)))
                    // Display the size of the copied fonts
                    .pipe($.size({title: 'sprites'}))
            );
        });

    }

    if (streams.length > 0) {
        return merge(streams);
    }
});

// Copy all Web Fonts
gulp.task('fonts', function () {
    var streams = [];
    // Loop on each asset where %asset.type% is "font"
    _.each(_.where(config.assets, {type: 'fonts'}), function (asset) {
        // Get all %asset.files% and append them the %asset.src% so
        // we don't need to specify the full path for each glob
        streams.push(
            gulp.src(asset.files)
                // Flatten the directory structure because we don't need the same font multiple times
                // so we are not afraid of name conflicts
                .pipe($.flatten())
                // Process only files who changed
                .pipe($.if(onlyNewer, $.newer(path.join(config.dest, 'fonts'))))
                // Save all fonts in %config.dest%/fonts
                .pipe(gulp.dest(path.join(config.dest, 'fonts')))
                // Display the size of the copied fonts
                .pipe($.size({title: 'fonts'}))
                .pipe(config.server.plugin === 'browser-sync' ? reload({stream: true}) : reload())
        );
    });

    if (streams.length > 0) {
        return merge(streams);
    }
});

// Build all styles (css, less, sass)
gulp.task('styles', function () {
    var streams = [];
    // Loop on each asset where %asset.type% is "styles"
    _.each(_.where(config.assets, {type: 'styles'}), function (asset) {
        var filter,
            files
            ;
        // If we have an array of files to watch, then use them as
        // the files to read and add a filter on the real asset files so
        // we can check if a file to watch has changed but only compile the real asset files
        if (_.isUndefined(asset.watch) || _.isBoolean(asset.watch)) {
            files = asset.files;
        } else {
            files = asset.watch;
        }

        filter = $.filter(asset.filters);
        // Get all %asset.files% and append them the %asset.src% so
        // we don't need to specify the full path for each glob
        streams.push(
            gulp.src(files)
                // Prevent a pipe to break the task when an error is triggered
                .pipe($.plumber({errorHandler: onError}))
                // Only build if a file has changed
                .pipe($.if(onlyNewer, $.newer(path.join(config.dest, 'styles', asset.output))))
                // Filter on the files that really need to be processed
                .pipe(filter)
                // Compile all less files
                .pipe($.if('*.less', $.less()))
                // Compile all sass files
                .pipe($.if('*.scss', $.sass()))
                // Concatenate all files in one
                .pipe($.concat(asset.output))
                // Add browser prefixes to all styles that matches the list of supported browsers
                .pipe($.autoprefixer(!_.isUndefined(config.autoprefixer) ? config.autoprefixer.options || {} : {}))
                // Rewrite the url of all fonts in the css file to point to the new fonts directory
                .pipe($.replace(/([\/\w\._-]+\/)*([\w\._-]+\.(ttf|eot|woff|svg))/g, '../fonts/$2'))
                // TODO : uncomment the line below if you want the images directory structure to be flatten
                //.pipe($.replace(/([\/\w\._-]+\/)*([\w\._-]+\.(png|jpg|gif))/g, '../images/$2'))
                // Save the concatenated styles in %config.dest%/styles
                .pipe(gulp.dest(path.join(config.dest, 'styles')))
                // Display the size of the concatenated styles
                .pipe($.size({title: 'styles'}))
                // Display a desktop notification when the developments files are built
                .pipe($.notify({message: 'Styles file built : <%= file.relative %>'}))
                // Trigger a server reload
                .pipe(config.server.plugin === 'browser-sync' ? reload({stream: true}) : reload())
                // Append the production suffix on the filename
                .pipe($.rename({suffix: config.productionSuffix}))
                // Minify all css for production files
                .pipe($.csso())
                // Save the optimized styles in %config.dest%/css
                .pipe(gulp.dest(path.join(config.dest, 'styles')))
                // Display the size of the optimized styles
                .pipe($.size({title: 'styles[optimized]'}))
        );
    });

    if (streams.length > 0) {
        return merge(streams);
    }
});

// Build all javascripts
gulp.task('scripts', function () {
    var streams = [];
    // Loop on each asset where %asset.type% is "scripts"
    _.each(_.where(config.assets, {type: 'scripts'}), function (asset) {
        var files;
        // If we have an array of files to watch, then use them as the files to read
        if (_.isUndefined(asset.watch) || _.isBoolean(asset.watch)) {
            files = asset.files;
        } else {
            files = asset.watch;
        }

        // Get all %asset.files% and append them the %asset.src% so
        // we don't need to specify the full path for each glob
        streams.push(
            gulp.src(files)
                // Prevent a pipe to break the task when an error is triggered
                .pipe($.plumber({errorHandler: onError}))
                // Only build if a file has changed
                .pipe($.if(onlyNewer, $.newer(path.join(config.dest, 'scripts', asset.output))))
                // Concatenate all javascripts in one file
                .pipe($.concat(asset.output))
                // Save the concatenated javascripts in %config.dest%/scripts
                .pipe(gulp.dest(path.join(config.dest, 'scripts')))
                // Display the size of the concatenated javascripts
                .pipe($.size({title: 'scripts'}))
                // Display a desktop notification when the developments files are built
                .pipe($.notify({message: 'Javascript file built <%= file.relative %>'}))
                // Trigger a server reload
                .pipe(config.server.plugin === 'browser-sync' ? reload({stream: true}) : reload())
                // Append the production suffix on the filename
                .pipe($.rename({suffix: config.productionSuffix}))
                // Minify all javascripts for production files
                .pipe($.uglify())
                // Save the optimized javascripts in %config.dest%/scripts
                .pipe(gulp.dest(path.join(config.dest, 'scripts')))
                // Display the size of the optimized javascripts
                .pipe($.size({title: 'scripts[optimized]'}))
        );
    });

    if (streams.length > 0) {
        return merge(streams);
    }
});

// Lint all javascripts
gulp.task('lint', function () {
    var files = [],
        streams = [];
    // Loop on each assets where %asset.type% is "scripts" and where %asset.lint% is true or an array of files
    _.each(_.where(config.assets, {type: 'scripts'}), function (asset) {
        // Don't run the task for asset where %asset.lint% is false or undefined
        if (!_.isUndefined(asset.lint) && asset.lint) {
            // If %asset.lint% is an array, replace the %asset.files% by this one
            if (_.isArray(asset.lint)) {
                files = asset.lint;
            } else { // otherwise, simply use the %asset.files%
                files = asset.files;
            }
            // Loop on all %files% and append them the %asset.src% so we
            // don't need to specify the full path for each glob
            streams.push(
                gulp.src(files)
                    // Start jshint
                    .pipe($.jshint())
                    // Display the report
                    .pipe($.jshint.reporter('default'))
            );
        }
    });

    if (streams.length > 0) {
        return merge(streams);
    }
});

// Task that copies an array of files to another destination
gulp.task('copies', function () {
    var streams = [];
    if (!_.isUndefined(config.copies)) {
        _.each(config.copies, function (copy) {
            streams.push(
                gulp.src(copy.files)
                    // Only copy if a file has changed
                    .pipe($.if(onlyNewer, $.newer(copy.dest || config.dest)))
                    // Copy the files to the new destination
                    .pipe(gulp.dest(copy.dest || config.dest))
                    .pipe($.size({title: 'copy'}))
            );
        });

    }

    if (streams.length > 0) {
        return merge(streams);
    }
});

// Build every assets sequentially
gulp.task('build', function () {
    runSequence('bower-install', ['copies', 'sprites'], ['fonts', 'images', 'scripts', 'styles'], function () {
        onlyNewer = true;
        $.util.log($.util.colors.green('Build completed'));
    });
});

// Clean destination directories
gulp.task('clean', function () {
    // Get all assets directories
    var assetDirectories = [
        path.join(config.dest, 'images'),
        path.join(config.dest, 'fonts'),
        path.join(config.dest, 'scripts'),
        path.join(config.dest, 'styles'),
        path.join(config.dest, 'vendor')
    ];
    // Delete all assets directories
    del(assetDirectories, function (err, deletedFiles) {
        $.util.log('Cleaning', $.util.colors.magenta(deletedFiles));
    });
});


// Watch files for changes and trigger the right task
gulp.task('watch', ['build'], function () {
    // Check if we have assets that need to be watched
    if (!_.isUndefined(config.assets) && _.isArray(config.assets)) {
        // Loop on each assets where %asset.watch% is true or is an array of files to watch
        _.each(config.assets, function (asset) {
            // Don't run the task for asset where %asset.watch% is false or undefined
            var files = [];
            if (!_.isUndefined(asset.watch) && asset.watch) {
                // If %asset.watch% is true, use the %asset.files%
                if (_.isBoolean(asset.watch)) {
                    files = asset.files;
                } else { // otherwise, use the %asset.watch%
                    files = asset.watch;
                }
            }
            gulp.watch(files, [asset.type]);
        });
    }

    // Check if we have sprites that need to be watched
    if (!_.isUndefined(config.sprites) && _.isArray(config.sprites)) {
        // Loop on each sprites where %sprite.watch% is true
        _.each(config.sprites, function (sprite) {
            if (!_.isUndefined(sprite.watch) && sprite.watch) {
                gulp.watch(sprite.files, ['sprites']);
            }
        });
    }
});

// Start a server to refresh the browser on changes
gulp.task("serve", ["watch"], function (cb) {
    if (!_.isUndefined(config.server)) {
        if (!_.isUndefined(config.server.plugin)) {
            switch (config.server.plugin) {
                case 'livereload':
                    $.util.log('Starting', $.util.colors.cyan('livereload'));
                    server.listen(config.server.options);
                    break;
                case 'browser-sync':
                    $.util.log('Starting', $.util.colors.cyan('browser-sync'));
                    server(config.server.options);
                    break;
                default:
                    $.util.log('No plugin available with name', $.util.colors.red(config.server.plugin));
                    return cb;
                    break;
            }
        } else {
            $.util.log($.util.colors.red('Missing server.plugin'));
            return cb;
        }
    }
    if (!_.isUndefined(config.server.watch)) {
        gulp.watch(config.server.watch).on('change', function (event) {
            gulp.src(event.path).pipe(config.server.plugin === 'browser-sync' ? reload({stream: true}) : reload());
        });
    }
});


// Actually create the default task
gulp.task('default', ['serve']);