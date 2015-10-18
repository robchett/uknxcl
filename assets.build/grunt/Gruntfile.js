var root = '../../';
var assets_root = root + 'assets.build/';
module.exports = function (grunt) {
    require('grunt-task-loader')(grunt, {
        mapping: {
            sass_globbing: 'grunt-sass-globbing'
        }
    })

    var sass_globbing_files = {};
    sass_globbing_files[assets_root + 'scss/includes/partials.scss'] = assets_root + 'scss/partials/*.scss';
    sass_globbing_files[assets_root + 'scss/includes/core.scss'] = root + '.core/css/*.scss';
    sass_globbing_files[assets_root + 'scss/includes/modules.scss'] = root + '/inc/module/**/scss/*.scss';

    var sass_files = {};
    sass_files[root + 'css/styles.css'] = assets_root + 'scss/styles.scss';
    sass_files[root + 'css/ckeditor.css'] = assets_root + 'scss/ckeditor.scss';                 

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        watch: {
            scss: {
                files: [assets_root + '**/*.scss', assets_root + '!scss/includes/*.scss'],
                tasks: ['sass_globbing', 'sass'],
            },
            scripts: {
                files: [root + '.core/**/*.js', assets_root + 'js/*.js'],
                tasks: ['concat', 'uglify'],
                spawn: false,
                interrupt: true
            }
        },
        uglify: {
            build: {
                src: root + 'js/script.js',
                dest: root + 'js/script.min.js'
            }
        },
        concat: {
            options: {
                separator: ';'
            },
            js: {
                src: [
                    root + '.core/js/jquery.js',
                    root + '.core/js/_ajax.js',
                    root + '.core/js/_default.js',
                    root + '.core/js/jscrollpane.min.js',
                    root + '.core/js/page_handler.js',
                    root + '.core/js/table-sorter.js',
                    root + '.core/js/colorbox.js',
                    assets_root + 'js/*.js'
                ],
                dest: root + 'js/script.js',
                nonull: true
            }
        },
        sass: {
            css: {
                options: {},
                files: sass_files
            }
        },
        sass_globbing: {
            css: {
                files: sass_globbing_files,
                options: {}
            }
        }
    });
    grunt.registerTask('default', ['watch']);
};
