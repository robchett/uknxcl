var root = './';
var target = root + 'public/';
var src = root + 'assets/';
module.exports = function (grunt) {
    require('grunt-task-loader')(grunt, {
        mapping: {
            sass_globbing: 'grunt-sass-globbing'
        }
    })

    var sass_globbing_files = {};
    sass_globbing_files[src + 'scss/includes/partials.scss'] = src + 'scss/partials/*.scss';
    sass_globbing_files[src + 'scss/includes/core.scss'] = src + 'scss/core/*.scss';
    sass_globbing_files[src + 'scss/includes/modules.scss'] = root + '/inc/module/**/scss/*.scss';

    var sass_files = {};
    sass_files[target + 'css/styles.scss'] = src + 'scss/*.scss';
    sass_files[target + 'css/'] = src + 'scss/*.scss';

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        watch: {
            scss: {
                files: [src + 'scss/**/*.scss', src + '!scss/includes/*.scss'],
                tasks: ['sass_globbing', 'sass'],
            },
            scripts: {
                files: [src + 'js/**/*.js', src + 'js/*.js'],
                tasks: ['concat', 'uglify'],
                spawn: false,
                interrupt: true
            }
        },
        uglify: {
            build: {
                src: target + 'js/script.js',
                dest: target + 'js/script.min.js'
            }
        },
        concat: {
            options: {
                separator: ';'
            },
            fontend: {
                src: [
                    src + 'js/core/01_jquery.js',
                    src + 'js/core/02_ajax.js',
                    src + 'js/core/03_default.js',
                    src + 'js/core/04_jscrollpane.min.js',
                    src + 'js/core/05_page_handler.js',
                    src + 'js/core/06_table-sorter.js',
                    src + 'js/core/07_content_collapse.js',
                    src + 'js/core/08_colorbox.js',
                    src + 'js/*.js'
                ],
                dest: target + 'js/script.js',
                nonull: true
            },
            cms: {
                src: [
                    src + 'js/core/01_jquery.js',
                    src + 'js/core/02_ajax.js',
                    src + 'js/core/07_content_collapse.js',
                    src + 'js/cms/bootstrap/*.js'
                ],
                dest: target + 'js/cms.js',
                nonull: true
            }
        },
        sass: {
            options: {
                implementation: require('node-sass'),
                sourceMap: true
            },
            css: {
                cwd: src + '/scss',
                expand: true,
                src: '*.scss',
                dest: target + 'css/',
                ext: '.css'
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
