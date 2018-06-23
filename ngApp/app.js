'use strict';

// Declare app level module which depends on views, and components
angular.module('myApp', [
  'ngRoute'
  , 'myApp.pots'
  , 'myApp.ledger'
  , 'myApp.view3'
  , 'myApp.version'
  , 'myApp.jDirectives'
  , 'myApp.jFilters'
  , 'ui.bootstrap'
  , 'angularUtils.directives.dirPagination'
  , 'ngResource'
  , 'angular-growl'
  , 'ngAnimate'
]).
config(function($locationProvider, $routeProvider, growlProvider) {
  "ngInject";

  // $locationProvider.hashPrefix('!');
  // $locationProvider.html5Mode({
  //   enabled: true,
  //   requireBase: true,
  //   rewriteLinks: false
  // });

  growlProvider.globalTimeToLive(3000);
  growlProvider.globalDisableCountDown(true);

  $routeProvider.otherwise({redirectTo: '/pots'});

})
.run(function($rootScope) {
  $rootScope.apiURL = 'http://127.0.0.1:3000';
});
