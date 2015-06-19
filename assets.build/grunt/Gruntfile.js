var root = '../../';
var assets_root = root + 'assets.build/';
module.exports = function (grunt) {
    require('grunt-task-loader')(grunt, {
        mapping: {
            sass_globbing: 'grunt-sass-globbing'
        }
    })
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        watch: {
            scss: {
                files: [assets_root + '**/*.scss', assets_root + '!scss/includes/*.scss'],
                tasks: ['sass_globbing', 'sass'],
            },
            scripts: {
                files: [root + '.core/**/*.js', root + 'js/src/*.js'],
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
                    root + 'js/src/*.js'
                ],
                dest: root + 'js/script.js',
                nonull: true
            }
        },
        sass: {
            css: {
                options: {
                },
                files: {
                    root + 'css/styles.css': assets_root + 'scss/styles.scss',
                    root + 'css/ckeditor.css': assets_root + 'scss/ckeditor.scss',                   
                }
            }
        },
        sass_globbing: {
            css: {
                files: {
                    assets_root + 'scss/includes/partials.scss': assets_root + 'scss/partials/*.scss',
                    assets_root + 'scss/includes/core.scss': root + '.core/css/*.scss',
                    assets_root + 'scss/includes/modules.scss': root + '/inc/module/**/scss/*.scss',
                },
                options: {
                }
            }
        }
    });
    grunt.registerTask('default', ['watch']);
};
