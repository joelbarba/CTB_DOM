'use strict';

module.exports = function (grunt) {

  // Read the package.json file and update the package version
  var pkg = grunt.file.readJSON('package.json');
  var versionTokens = pkg.version.split('.');
  var version = versionTokens.pop();
  version++;
  versionTokens.push(version);
  pkg.version = versionTokens.join('.');

  require('load-grunt-tasks')(grunt);     // Load grunt tasks automatically
  require('time-grunt')(grunt);           // Time how long tasks take. Can help when optimizing build times

  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // Configurable paths for the application
  var appConfig = {
    app     : 'ngApp',
    dist    : 'dist'
  };


  grunt.initConfig({
    yeoman: appConfig,
    pkg: pkg,

    // Empties folders
    clean: {
      prepare: {
        files: [{ dot: true, src: ['<%= yeoman.portal %>']}]
      },
      prepareScss : {
        files: [{ dot: true, src: ['<%= yeoman.app %>/styles/scss/portal/'] }]
      },
      dist: {
        files: [{ dot: true, src: [ '.tmp', '<%= yeoman.dist %>' ] }]
      }
    },

    // Copies remaining files to places other tasks can use
    copy: {
      dist: {
        files: [
          { expand: true,
            cwd: '<%= yeoman.app %>',
            src: 'index.html',
            dest: '<%= yeoman.dist %>'
          }, {
            expand: true, dot: true,
            cwd: '<%= yeoman.app %>',
            dest: '<%= yeoman.dist %>',
            src: [
              // 'portal/**/*.*',
              'portal/resources/**/*.*',
              'portal/components/**/*.html',
              'portal/components/common/home/homeController.js',
              'portal/components/common/login/controllers.js',
              'portal/packages/**/*.html',
              'portal/resources/**/*.*',
              'portal/core/views/**/*.*',
              'portal/core/scripts/vendors/script.min.js'
            ]
          }
        ]
      },
      verto: {
        files: [
          {
            expand: true,
            cwd: '<%= yeoman.app %>/../../../blueface_spliceuiportal/blueface_splice/dist/verto',
            src: ["*.*", "**/*.*", "!views/virtual-phone-app*"],
            dest: '<%= yeoman.app %>/verto'
          },
          { expand: true, cwd: '<%= yeoman.app %>/styles', src: ["fonts.css", "whitelabel.css"], dest: '<%= yeoman.app %>/verto/styles' },
          { expand: true, cwd: '<%= yeoman.app %>/portal/core/styles', src: ["*.css"], dest: '<%= yeoman.app %>/verto/styles' },
          { expand: true, cwd: '<%= yeoman.app %>/portal/resources/fonts', src: ["*.*"], dest: '<%= yeoman.app %>/verto/portal/resources/fonts' }
        ]
      }
    },

    // Copy and Replace
    replace: {

      // Copy the file "/ng-config/project-config.js" (replacing patterns)
      prepareConfig: {
        options: {
          patterns: [
            { match: 'version',         replacement: '"<%= pkg.version %>";' },
            { match: 'softphoneDomain', replacement: '"<%= pkg.softphoneDomain %>";' }
          ]
        },
        files: [
          {
            src: ['<%= yeoman.app %>/../ng-config/project-config.js'],
            dest: '<%= yeoman.app %>/scripts/project-config.js'}
        ]
      },

      // Replace the url of the views from /components/.. to /portal/components/.. at routing-config.json
      prepareRouting: {
        options: {
          usePrefix: false,
          patterns: [
            {
              match: '"components/',
              replacement: '"portal/components/'
            }
          ]
        },
        files: [
          {
            src: ['<%= yeoman.portal %>/resources/common/routing-config.json'],
            dest: '<%= yeoman.portal %>/resources/common/routing-config.json'}
        ]
      }
    },

    // Generates /styles/scss/import.scss with the list of all SCSS files
    sass_globbing: {
      your_target: {
        files: {
          '<%= yeoman.app %>/styles/scss/imports.scss': [
             '<%= yeoman.app %>/styles/scss/bootstrap/_bootstrap.scss'
            ,'<%= yeoman.app %>/styles/scss/basic/mixins.scss'
            ,'<%= yeoman.app %>/styles/scss/basic/variables.scss'
            ,'<%= yeoman.app %>/styles/scss/components/*.scss'
            ,'<%= yeoman.app %>/styles/scss/*.scss'
          ]
        },
        options: {
          useSingleQuotes: false,
          signature: '// This file is auto generated with sass-globbing, do not modify it manually (see gruntfile config)'
        }
      }
    },

    // Compile whitelabel.scss into /styles/whitelabel.css
    sass: {
      compile: {
        options: { style: 'expanded' },
        files: { '<%= yeoman.app %>/styles/app.css': '<%= yeoman.app %>/styles/scss/imports.scss' }
      }
    },

    // Include file refs between include marks into index.html
    includeSource: {
      options: {
        basePath: '<%= yeoman.app %>',
        baseUrl: ''
      },
      prepare: {
        files: {
          '<%= yeoman.app %>/index.html': '<%= yeoman.app %>/index.tpl.html'
        }
      },
      dist: {
        files: {
          '<%= yeoman.dist %>/index.html': '<%= yeoman.app %>/index.html'
        }
      }
    },

    // Automatically inject Bower components into the app
    wiredep: {
      app: {
        src: ['<%= yeoman.app %>/index.html'],
        directory: './ngApp/bower_components',
        cwd: './ngApp/',
        ignorePath:  /\.\.\//
      }
    },

    // -------------------------------------------------------------------------------

    // Reads HTML for usemin blocks to enable smart builds that automatically concat, minify and revision files.
    // Creates configurations in memory so additional tasks can operate on them
    useminPrepare: {
      html: '<%= yeoman.dist %>/index.html',
      options: {
        dest: '<%= yeoman.dist %>',
        flow: {
          html: {
            steps: {
              js: ['concat', 'uglifyjs'],
              css: ['cssmin']
            },
            post: {
              js: [{
                name: 'uglify',
                createConfig: function (context, block) {
                  var generated = context.options.generated;
                  generated.options = {
                    banner: '/*! <%= pkg.name %> - v<%= pkg.version %> - ' +
                      '<%= grunt.template.today("yyyy-mm-dd") %> */'
                  };
                }
              }]
            }
          }
        }
      }
    },

    // Minifies images
    imagemin: {
      dist: {
        files: [{
          expand: true,
          cwd: '<%= yeoman.app %>/images',
          src: '{,*/}*.{png,jpg,jpeg,gif,ico}',
          dest: '<%= yeoman.dist %>/images'
        }]
      }
    },

    // Add vendor prefixed styles
    autoprefixer: {
      options: {
        browsers: ['last 1 version']
      },
      server: {
        options: {
          map: true,
        },
        files: [{
          expand: true,
          cwd: '.tmp/styles/',
          src: '{,*/}*.css',
          dest: '.tmp/styles/'
        }]
      },
      dist: {
        files: [{
          expand: true,
          cwd: '.tmp/styles/',
          src: '{,*/}*.css',
          dest: '.tmp/styles/'
        }]
      }
    },

   // Renames files for browser caching purposes
    filerev: {
      dist: {
        src: [
          '<%= yeoman.dist %>/scripts/{,*/}{,*/}*.js',
          '<%= yeoman.dist %>/styles/{,*/}*.css',
          '<%= yeoman.dist %>/styles/fonts/*'
        ]
      }
    },

    // Html minification
    htmlmin: {
      dist: {
        options: {
          collapseWhitespace: true,
          conservativeCollapse: true,
          collapseBooleanAttributes: true,
          removeCommentsFromCDATA: true,
          removeOptionalTags: true
        },
        files: [{
          expand: true,
          cwd: '<%= yeoman.dist %>',
          src: ['*.html', 'components/views/{,*/}*.html'],
          dest: '<%= yeoman.dist %>'
        }]
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


    // -------------------------------------------------------------------------------


    // Concat js files from the same folder (packages) to improve performance on lazy loading
    concat: {
      // packages: {
      //   options: {
      //     sourceMap: true
      //   },
      //   files: concatDistFilesConfig
      // }
    },

    // Fixes the DI for minification
    ngAnnotate: {
      generated: {
        files: [{
          expand: true,
          cwd: '.tmp/concat/scripts',
          src: '*.js',
          dest: '.tmp/concat/scripts'
        }]
      },
      packages: {
        files: [{
          expand: true, cwd: '<%= yeoman.dist %>',
          src: ['packages/**/*.js'],
          dest: '<%= yeoman.dist %>'
        }, {
          expand: true, cwd: '<%= yeoman.dist %>/portal/components/common',
          src: ['home/homeController.js', 'login/controllers.js'],
          dest: '<%= yeoman.dist %>/portal/components/common'
        }]
      }
    },

    // Minifies the (lazy loaded) JS files from the packages folder
    uglify: {
      packages: {
        files: [{
            expand: true, cwd: '<%= yeoman.dist %>', src: ['packages/**/*.js'],
            dest: '<%= yeoman.dist %>'
          // }, {
          //   expand: true, cwd: '<%= yeoman.dist %>/portal/components/common/home',
          //   src: ['home/homeController.js', 'login/controllers.js'],
          //   dest: '<%= yeoman.dist %>/portal/components/common/home'
          }
        ]
      }
    },


  });

  grunt.registerTask('prepare', [
    'sass_globbing'                   // Generate the /styles/scss/imports.scss file (list of includes)
    ,'sass:compile'                   // Compile /styles/scss/imports.scss into /styles/app.css
    ,'includeSource:prepare'          // Include file refs between include marks from "app/index.tpl.html" into "app/index.html"
    ,'wiredep'                        // Automatically inject bower components into index.html (between bower:css, bower:js)
  ]);

  grunt.registerTask('build', [
     'clean:dist'                     // Remove "dist" folder
    ,'copy:dist'                      // Copy files from "app/" to "dist/"

    ,'useminPrepare'                  // Starts the usemin process
    ,'imagemin'                       // Minifies the images (from /app/images --- to ---> /dist/images)
    ,'autoprefixer'                   // Add vendor prefixed styles for the css
    ,'concat'                         // Concat all js and css files between build:js / build:css marks (place them into .tmp)
    ,'ngAnnotate:generated'           // Solve DI for all ".tmp/concat/scripts/*.js" (temporary files in usemin process)
    ,'cssmin'                         // Minifies css files (in the .tmp folder)
    ,'uglify'                         // Minifies the js files (in the .tmp folder)
    ,'filerev'                        // Renames the files with .xxxxxx. extension
    ,'usemin'                         // Finished the usmin process

    ,'packages-dependencies'          // Set the packages dependencies for each route at routing-config.json
    ,'concat:packages'                // Pack all "app/portal/components/*js" files into "dist/packages/*.js"
    ,'ngAnnotate:packages'            // Solve DI for all "dist/packages/*.js" files
    ,'uglify:packages'                // Minifies the (lazy loaded) JS files from the packages folder

    // ,'htmlmin' // let's leave this out at the moment because has a bug with some angular scripts with the html
  ]);



  grunt.registerTask('packages-dependencies', function () {

    // It loops the JSON file routing-config.json,
    // and for each route, it takes the dependencies, and

    var routingConfigFileSrc = appConfig.portal + '/resources/common/routing-config.json';
    var routingConfigFileDst = appConfig.dist + '/portal/resources/common/routing-config.json';

    var routingConfig = grunt.file.readJSON(routingConfigFileSrc);
    routingConfig.forEach(function(config){
      var packages = [];
      var usedPackages = {};
      // console.log('config.name', config.name);
      if (config.hasOwnProperty('dependencies')) {
        config.dependencies.forEach(function(dep) {
          // if it is a dependency from the common folder it doesn't have a package, so let's use it as is

          if (dep.indexOf('common') !== -1) {
            packages.push(dep);
          } else {

            // Remove the 2 first folders of the path (portal/components/)
            var path = dep.split('/');
            path.shift();
            path.shift();

            // Remove the last element of the path (the name of the file)
            path.reverse();
            path.shift();
            path.reverse();

            // Generate the path of the package
            var packageName = path.join('/') + '.js';

            // we want to use the same package only once
            if(!usedPackages[packageName]){
              usedPackages[packageName] = true;
              packages.push('packages/' + packageName);
            }
          }
        });
      }
      // console.log('  - packages', packages);
      config.packages = packages;

    });

    // grunt.log.writeln(JSON.stringify(routingConfig, null, 2));

    grunt.file.write(routingConfigFileDst, JSON.stringify(routingConfig, null, 2));
  });


  grunt.registerTask('default', function () {
    grunt.task.run('build');
  });
};
//
