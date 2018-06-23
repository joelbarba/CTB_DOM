'use strict';

var jFilters = angular.module('myApp.jFilters', []);

jFilters.filter('twoDecimal', function() {
  return function(inputVal) {
    try {
      var numVal = parseInt(inputVal);
      return numVal.toFixed(2) + ' €';

    } catch(err) {
      return inputVal;
    }
  };
});


