'use strict';

angular.module('myApp.ledger', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/ledger', {
    templateUrl: 'views/ledger/ledger.html',
    controller: 'LedgerCtrl'
  });
}])

.controller('LedgerCtrl', function($scope, $resource, $rootScope, $q, AccPotsService, $uibModal) {

  $scope.ledgerList = [];
  $scope.realPots = [];
  $scope.accPots = [];

  var ledgerResource = $resource($rootScope.apiURL + '/api/v1/ledger/:movId', 
  { movId: '@id' }, 
  { withCredentials: false,
    patch: { method: 'PATCH' }
  });


  // Load realPots list
  var realPotsPromise = $resource($rootScope.apiURL + '/api/v1/real_pots/:realPotId').get(function(data) {
    if (!!data && data.hasOwnProperty('real_pots')) {
      $scope.realPots = angular.copy(data.real_pots);
      $scope.realPots.push({id: 0, pos: 0, name: 'Total'});
      $scope.realPots.sort(function(itemA, itemB) {
        return itemA.pos - itemB.pos;
      });
      $scope.colRPot = 0;
    }
  }).$promise;

  // Load acc pots list
  var accPotsPromise = AccPotsService.loadAccPots();

  // Load movements list
  ledgerResource.get(function(data) {
    if (!!data && data.hasOwnProperty('ledger')) {
      $scope.ledgerList = angular.copy(data.ledger);
      $q.all([realPotsPromise, accPotsPromise]).then(function() {
        $scope.accPots = angular.copy(AccPotsService.accPotsFlatList);
        $scope.ledgerList.forEach(function(mov) {
          mov.realPot = $scope.realPots.getById(mov.real_pot_id);
          mov.accPot = AccPotsService.accPotsFlatList.getById(mov.acc_pot_id);
        })
      });
    }
  });

  // Open edit movement modal
  $scope.openEditMov = function(movement) {
    $scope.task = 'edit';
    ledgerResource.get({ movId: movement.id }, function(data) {
      $scope.item = angular.copy(data.movement);
      $uibModal.open({
        size        : 'lg',
        templateUrl : 'views/ledger/editMovModal.html',
        scope       : $scope,
        controller  : function($scope, $uibModalInstance) {
          "ngInject";

          $scope.item.mov_date = new Date($scope.item.mov_date);
          $scope.item.amount = isNaN($scope.item.amount) ? 0 : parseInt($scope.item.amount).toFixed(2);

          $scope.dateOptions = {
            formatYear: 'yyyy',
            startingDay: 1
          };
          $scope.datePopup = {
            opened: false
          };

          $scope.selRealPot = function(realPot) {
            if (!!realPot) {
              $scope.realPotSelected = realPot;
              $scope.item.real_pot_id = realPot.id;
            } else {
              $scope.realPotSelected = { pos: '', name: '' };
              $scope.item.real_pot_id = null;
            }
          }
          $scope.selRealPot($scope.realPots.getById($scope.item.real_pot_id));

          $scope.selAccPot = function(accPot) {
            if (!!accPot) {
              $scope.accPotSelected = accPot;
              $scope.item.acc_pot_id = accPot.id;
            } else {
              $scope.accPotSelected = { displayTabName: '' };
              $scope.item.acc_pot_id = null;
            }
          }
          $scope.selAccPot($scope.accPots.getById($scope.item.acc_pot_id));

  
          $scope.saveItem = function() {
            var updatedItem = angular.copy($scope.item);
            updatedItem.amount = Number(updatedItem.amount);
            ledgerResource.patch(updatedItem, function(data) {
              var listItem = $scope.ledgerList.getById(data.movement.id);
              if (listItem) {
                angular.merge(listItem, data.movement);
              }
              growl.success("Movement saved successfully");
              $uibModalInstance.close();
            }, function(error) {
                growl.error(error.data.error);
              }
            );
          };
  
          $scope.removeItem = function() {
            realPotsResource.remove({ realPotId: $scope.item.id }, function() {
              $scope.realPotsList.removeById($scope.item.id);
              $uibModalInstance.close();
            });
          };
  
        }
      });
    });
  };

});