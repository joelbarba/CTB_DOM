'use strict';

angular.module('myApp.view1', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/view1', {
    templateUrl: 'view1/view1.html',
    controller: 'View1Ctrl'
  });
}])

.controller('View1Ctrl', ['$scope', function($scope) {

  $scope.expensesList = [
    { name: 'First',  value: 24.23 },
    { name: 'Second', value: 18.48 },
    { name: 'fsdsdf', value: 97.61 },
    { name: 'Last',   value: 57.19 },
  ]

}]);