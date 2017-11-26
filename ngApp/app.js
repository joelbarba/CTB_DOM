'use strict';

// Declare app level module which depends on views, and components
angular.module('myApp', [
  'ngRoute'
  , 'myApp.view1'
  , 'myApp.view2'
  , 'myApp.view3'
  , 'myApp.version'
  , 'myApp.jDirectives'
  , 'ui.bootstrap'
  , 'angularUtils.directives.dirPagination'
  , 'ngResource'
  , 'angular-growl'
  , 'ngAnimate'
]).
config(function($locationProvider, $routeProvider, growlProvider) {
  "ngInject";

  $locationProvider.hashPrefix('!');
  growlProvider.globalTimeToLive(3000);
  growlProvider.globalDisableCountDown(true);

  $routeProvider.otherwise({redirectTo: '/view1'});
});
