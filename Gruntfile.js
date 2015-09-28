module.exports = function(grunt) {
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
            dist: {
                src: ['./.core/js/jquery.js', './.core/js/_ajax.js', './.core/js/_default.js', './.core/js/jscrollpane.min.js', './.core/js/page_handler.js', './.core/js/table-sorter.js', 'js/src/*.js'],
                dest: 'js/script.js',
                nonull: true,
            },
        },
        sass: {
            dist: {
                options: {
                },
                files: {
                    'css/styles.css': 'scss/styles.scss'
                }
            }
        },
	sass_globbing: {
    	    your_target: {
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
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-sass');
    grunt.loadNpmTasks('grunt-sass-globbing');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.registerTask('default', ['watch']);
};
