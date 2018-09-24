'use strict';

var jFilters = angular.module('myApp.jFilters', []);

jFilters.filter('twoDecimal', function() {
  return function(inputVal) {
    if (isNaN(inputVal)) {
      return inputVal;
    } else {
      var numVal = parseFloat(inputVal);
      return numVal.toFixed(2) + ' €';
    }
  };
});


