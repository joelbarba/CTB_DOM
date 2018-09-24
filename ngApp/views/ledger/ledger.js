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

  var ledgerResource = $resource($rootScope.apiURL + '/api/v1/ledger/:movId/:action',
  { movId: '@id', action: '@action' },
  { withCredentials: false,
    patch: { method: 'PATCH' }
    // push_down: { method: 'PATCH', params: { action: 'push_down' } }
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
        $scope.setList();
      });
    }
  });

  // Arranges the list.
  // Adds pots object to every mov
  // Orders the list by num_mov
  $scope.setList = function() {
    $scope.ledgerList.forEach(function(mov) {
      mov.realPot = $scope.realPots.getById(mov.real_pot_id);
      mov.accPot = AccPotsService.accPotsFlatList.getById(mov.acc_pot_id);
    });
    $scope.ledgerList.sort(function(itemA, itemB) {
      return itemA.mov_num - itemB.mov_num;
    });
  };

  // Open edit movement modal
  $scope.openEditMov = function(movement) {
    $scope.task = 'edit';
    ledgerResource.get({ movId: movement.id }, function(data) {
      $scope.item = angular.copy(data.movement);
      $uibModal.open({
        size        : 'lg',
        templateUrl : 'views/ledger/editMovModal.html',
        scope       : $scope,
        controller  : function($scope, $uibModalInstance, growl) {
          "ngInject";
          $scope.item.mov_date = new Date($scope.item.mov_date);
          $scope.item.amount = isNaN($scope.item.amount) ? 0 : parseFloat($scope.item.amount).toFixed(2);
          $scope.originalItem = angular.copy($scope.item);

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
          };
          $scope.selRealPot($scope.realPots.getById($scope.item.real_pot_id));

          $scope.selAccPot = function(accPot) {
            if (!!accPot) {
              $scope.accPotSelected = accPot;
              $scope.item.acc_pot_id = accPot.id;
            } else {
              $scope.accPotSelected = { displayTabName: '' };
              $scope.item.acc_pot_id = null;
            }
          };
          $scope.selAccPot($scope.accPots.getById($scope.item.acc_pot_id));

          // return true if any value has changed
          $scope.hasChanged = function() {
            return (($scope.item.mov_date.getAPIDate() !== $scope.originalItem.mov_date.getAPIDate())
                || ($scope.item.mov_num      !== $scope.originalItem.mov_num)
                || ($scope.item.amount       !== $scope.originalItem.amount)
                || ($scope.item.description  !== $scope.originalItem.description)
                || ($scope.item.real_pot_id  !== $scope.originalItem.real_pot_id)
                || ($scope.item.acc_pot_id   !== $scope.originalItem.acc_pot_id)
                || ($scope.item.mov_group_id !== $scope.originalItem.mov_group_id));
          };

          $scope.saveItem = function() {
            var updateItem = { id: $scope.originalItem.id };

            if ($scope.item.mov_date.getAPIDate() !== $scope.originalItem.mov_date.getAPIDate()) {
              updateItem.mov_date = $scope.item.mov_date.getAPIDate();
            }
            if ($scope.item.mov_num      !== $scope.originalItem.mov_num)      { updateItem.amount       = $scope.item.mov_num;      }
            if ($scope.item.amount       !== $scope.originalItem.amount)       { updateItem.amount       = $scope.item.amount + '';  }
            if ($scope.item.description  !== $scope.originalItem.description)  { updateItem.description  = $scope.item.description;  }
            if ($scope.item.real_pot_id  !== $scope.originalItem.real_pot_id)  { updateItem.real_pot_id  = $scope.item.real_pot_id;  }
            if ($scope.item.acc_pot_id   !== $scope.originalItem.acc_pot_id)   { updateItem.acc_pot_id   = $scope.item.acc_pot_id;   }
            if ($scope.item.mov_group_id !== $scope.originalItem.mov_group_id) { updateItem.mov_group_id = $scope.item.mov_group_id; }

            if ($scope.hasChanged()) {
              ledgerResource.patch(updateItem, function(data) {
                var listItem = $scope.ledgerList.getById(data.movement.id);
                if (listItem) {
                  angular.merge(listItem, data.movement);
                  $scope.setList();
                }
                growl.success("Movement saved successfully");
                $scope.originalItem = angular.copy($scope.item);
                // $uibModalInstance.close();
              }, function(error) {
                  growl.error(error.data.error);
                }
              );
            }

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

  // Move one position down
  $scope.moveUpDown = function(movement, action) {
    var updateItem = {
      action: action,
      id: movement.id,
      mov_num: movement.mov_num,
      mov_date: movement.mov_date
    };
    ledgerResource.patch(updateItem, function(data) {
      // console.log('RESULT', data);
      data.movements.forEach(function(mov) {
        var listItem = $scope.ledgerList.getById(mov.id);
        angular.merge(listItem, mov);
      });
      $scope.ledgerList.sort(function(itemA, itemB) {
        return itemA.mov_num - itemB.mov_num;
      });
    });
  };

});