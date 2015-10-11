module.exports = function(grunt) {
    require('grunt-task-loader')(grunt, {
        mapping: {
            sass_globbing: 'grunt-sass-globbing'
        }
    })
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        watch: {
            scss: {
                files: ['**/*.scss', '!scss/includes/*.scss'],
                tasks: ['sass_globbing', 'sass'],
            },
            scripts: {
                files: ['.core/**/*.js', 'js/src/*.js'],
                tasks: ['concat','uglify'],
                spawn: false,
                interrupt: true
            }
        },
        uglify: {
            build: {
                src: 'js/script.js',
                dest: 'js/script.min.js'
            }
        },
        concat: {
            options: {
                separator: ';',
            },
            js: {
                src: [
                    './.core/js/jquery.js',
                    './.core/js/_ajax.js',
                    './.core/js/_default.js',
                    './.core/js/jscrollpane.min.js', 
                    './.core/js/colorbox.js', 
                    './.core/js/page_handler.js', 
                    './.core/js/table-sorter.js', 
                    'js/src/*.js'
                ],
                dest: 'js/script.js',
                nonull: true,
            },
        },
        sass: {
            css: {
                options: {
                },
                files: {
                    'css/styles.css': 'scss/styles.scss',
                    'css/ckeditor.css': 'scss/ckeditor.scss',
                }
            }
        },
        sass_globbing: {
            css: {
                files: {
                    'scss/includes/partials.scss': 'scss/partials/*.scss',
                    'scss/includes/core.scss': '../.core/css/*.scss',
                    'scss/includes/modules.scss': '../inc/module/**/scss/*.scss',
                },
                options: {
                }
            }
        }
    });
    grunt.registerTask('default', ['watch']);
};
