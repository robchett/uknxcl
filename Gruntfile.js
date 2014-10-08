module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        watch: {
            scss: {
                files: '**/*.scss',
                tasks: 'sass',
                spawn: false,
                interrupt: true
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
            dist: {
                src: ['./.core/js/jquery.js', './.core/js/_ajax.js', './.core/js/_default.js', './.core/js/jscrollpane.min.js', './.core/js/page_handler.js', './.core/js/table-sorter.js', 'js/src/*.js'],
                dest: 'js/script.js',
                nonull: true,
            },
        },
        sass: {
            dist: {
                options: {
                    style: 'expanded'
                },
                files: {
                    'css/styles.css': 'scss/styles.scss'
                }
            }
        }
    });
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.registerTask('default', ['watch']);
};