'use strict';

module.exports = function (grunt) {

  // update the package version
  var pkgFile = 'package.json';
  var pkg = grunt.file.readJSON(pkgFile);

  require('load-grunt-tasks')(grunt);   // Load grunt tasks automatically
  require('time-grunt')(grunt);         // Time how long tasks take. Can help when optimizing build times

  grunt.loadNpmTasks('grunt-replace');
  grunt.loadNpmTasks("grunt-ts");
  grunt.loadNpmTasks('grunt-contrib-sass');


  // Configurable paths for the application minified
  var appConfig = {
    app:  'src',
    dist: 'dist'
  };

  // Define the configuration for all the tasks
  grunt.initConfig({
    yeoman: appConfig,    // Project settings
    pkg: pkg,

    // Empties folders to start fresh
    clean: {
      dist: {
        files: [{
          dot: true,
          src: ['<%= yeoman.dist %>/{,*/}*', '.tmp', '.tscache', '.sass-cache']
        }]
      },
      temps: {
        files: [{
          dot: true,
          src: ['.tmp', '.tscache', '.sass-cache']
        }]
      }
    },

    // Typescript compile
    ts: {
      options:{
        compile: true,
        comments:true,
        module: 'none',
        target: 'es5',
        sourceMap: true,
        sourceRoot: '',
        mapRoot: '',
        declaration: false,
        lib: [
          "es2015",
          "es5",
          "dom",
          "scripthost"
        ],
        // fast: 'never',
        typeRoots: [
          "./node_modules/@types/",
          "./typings/"
        ]
      },
      dev : {
        src: ["<%= yeoman.app %>/{,*/}{,*/}{,*/}*.ts", "!node_modules/**"]
      }
    },

    sass_globbing: {
      your_target: {
        files: {
          '<%= yeoman.app %>/styles/scss/index.scss': ['<%= yeoman.app %>/styles/scss/views/*.scss']
        },
        options: {
          useSingleQuotes: false,
          signature: '// This file is auto generated with sass-globbing, do not modify it manually (see gruntfile config)'
        }
      }
    },

    sass:{
      compile: {
        options: { style: 'expanded' },
        files: { '<%= yeoman.app %>/styles/core.css': '<%= yeoman.app %>/styles/scss/index.scss' }
      }
    },

    // Files to be included as dependencies
    includeSource: {
      options: {
        basePath: '<%= yeoman.app %>',
        baseUrl: ''
      },
      myTarget: {
        files: {
          '<%= yeoman.app %>/index.html': '<%= yeoman.app %>/index-tpl.html'
        }
      }
    },

    // Automatically inject Bower components into the app
    wiredep: {
      app: {
        src: ['<%= yeoman.app %>/index.html'],
        ignorePath:  /\.\.\//
      }
    },

    // Reads HTML for usemin blocks to enable smart builds that automatically concat, minify and revision files.
    // Creates configurations in memory so additional tasks can operate on them
    useminPrepare: {
      html: '<%= yeoman.app %>/index.html',
      options: {
        dest: '<%= yeoman.dist %>',
        flow: {
          html: {
            steps: {
              js: ['concat', 'uglifyjs'],
              css: ['cssmin']
            },
            post: {}
          }
        }
      }
    },

    imagemin: {
      dist: {
        files: [{
          expand: true,
          cwd: '<%= yeoman.app %>/images',
          src: '{,*/}*.{png,jpg,jpeg,gif}',
          dest: '<%= yeoman.dist %>/images'
        }]
      }
    },

    // Add vendor prefixed styles
    autoprefixer: {
      options: { browsers: ['last 1 version'] },
      dist: {
        files: [{
          expand: true,
          cwd: '.tmp/styles/',
          src: '{,*/}*.css',
          dest: '.tmp/styles/'
        }]
      }
    },

    // ng-annotate tries to make the code safe for minification automatically by using the Angular long form for dependency injection.
    ngAnnotate: {
      dist: {
        files: [{
          expand: true,
          cwd: '.tmp/concat/scripts',
          src: '*.js',
          dest: '.tmp/concat/scripts'
        }]
      }
    },

    // Renames files for browser caching purposes
    filerev: {
      dist: {
        src: [
          '<%= yeoman.dist %>/scripts/{,*/}*.js',
          '<%= yeoman.dist %>/styles/{,*/}*.css',
          '<%= yeoman.dist %>/styles/fonts/*'
        ]
      }
    },

    // Performs rewrites based on filerev and the useminPrepare configuration
    usemin: {
      html: ['<%= yeoman.dist %>/{,*/}*.html'],
      css: ['<%= yeoman.dist %>/styles/{,*/}*.css'],
      options: {
        assetsDirs: [
          '<%= yeoman.dist %>',
          '<%= yeoman.dist %>/images',
          '<%= yeoman.dist %>/styles'
        ]
      }
    },

    // Copies static files needed (script.min.js, fonts, js locales)
    copy: {
      dist: {
        files: [
          {
            expand: true,
            cwd: 'src/scripts/common/vendors',
            src: 'script.min.js',
            dest: '<%= yeoman.dist %>/scripts'
          }, {
            expand: true,
            cwd: 'bower_components/bootstrap/dist/fonts',
            src: '*',
            dest: '<%= yeoman.dist %>/fonts/bootstrap'
          }, {
            expand: true,
            cwd: 'bower_components/angular-i18n/',
            src: [
              'angular-locale_en-ie.js', // English - IE
              'angular-locale_en-gb.js', // English - UK
              'angular-locale_en-us.js', // English - US
              'angular-locale_it.js', // Italian
              'angular-locale_es.js', // Spanish
              'angular-locale_fr.js', // French
              'angular-locale_nl.js', // Dutch
              'angular-locale_de.js', // German
              'angular-locale_da.js', // Danish
              'angular-locale_sv.js', // Swedish
              'angular-locale_pt.js', // Portuguese
              'angular-locale_fi.js', // Finnish
              'angular-locale_pl.js', // Polish
              'angular-locale_no.js', // Norwegian
              'angular-locale_el.js'  // Greek
            ],
            dest: '<%= yeoman.dist %>/bower_components/angular-i18n'
          }
        ]
      }
    }

  });

  grunt.registerTask('tsc', [
    'ts:dev'
  ]);

  grunt.registerTask('build', [
    'clean:dist'            // Clean dist folder
    // ,'ts:dev'               // Compile typescript files
    ,'sass_globbing'        // Generates core.scss list with all scss files to compile
    ,'sass'                 // Compiles scss files into css
    ,'includeSource'        // Include file refs between include marks into index.html
    ,'wiredep'              // Inject bower components (js+css) into index.html
    ,'useminPrepare'        // Starts the usemin process
    // ,'imagemin'             // Minifies the images
    ,'autoprefixer'         // Add vendor prefixed styles for the css
    ,'concat'               // Concat all js and css files between build:js / build:css marks (place them into .tmp)
    // ,'ngAnnotate'           // Fixes the DI for minification
    ,'cssmin'               // Minifies css files (in the .tmp folder)
    ,'uglify'               // Minifies the js files (in the .tmp folder)
    // ,'filerev'              // Renames the files with .xxxxxx. extension
    ,'usemin'               // Finished the usmin process
    // ,'copy:dist'            // Copies static files (script.min.js, bootstrap fonts, and angular locales)
    ,'clean:temps'          // Clean the temporal folders (.tmp, .tscache, .sass-cache)
  ]);

  grunt.registerTask('default', function () {
    grunt.task.run('build');
  });
};
