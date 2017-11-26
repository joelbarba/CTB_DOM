'use strict';

angular.module('myApp.view1', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/view1', {
    templateUrl: 'views/view1/view1.html',
    controller: 'View1Ctrl'
  });
}])

.controller('View1Ctrl', function($scope, growl, $uibModal, $resource) {
  "ngInject";

  var realPotsResource = $resource('/api/v1/real_pots/:realPotId', { realPotId: '@id' });

  // Load realPots list
  realPotsResource.get(function(data) {
    if (!!data && data.hasOwnProperty('real_pots')) {
      $scope.realPotsList = angular.copy(data.real_pots);
    }
  });

  // Open add Real Pot modal
  $scope.openAddModal = function() {
    $scope.task = 'add';
    $scope.item = { pos: 1, amount: 0 };
    $scope.realPotsList.forEach(function(pot) {
      if ($scope.item.pos <= pot.pos) { $scope.item.pos = pot.pos + 1; }
    });
    openModal();
  };

  // Open edit Real Pot modal
  $scope.openEditModal = function(selectedItem) {
    $scope.task = 'edit';
    realPotsResource.get({ realPotId: selectedItem.id }, function(data) {
      $scope.item = angular.copy(data.real_pot);
      openModal();
    });
  };

  function openModal() {
    $uibModal.open({
      size        : 'md',
      templateUrl : 'views/view1/item-modal.html',
      scope       : $scope,
      controller  : function($scope, $uibModalInstance) {
        "ngInject";

        $scope.createNewItem = function() {
          realPotsResource.save($scope.item, function(data) {
            $scope.realPotsList.push(data.real_pot);
            growl.success("New Pot created successfully");
            $uibModalInstance.close();
          });
        };

        $scope.saveItem = function() {
          var updatedItem = angular.copy($scope.item);
          updatedItem.amount = Number(updatedItem.amount);
          realPotsResource.save(updatedItem, function(data) {
            var listItem = $scope.realPotsList.getById(data.real_pot.id);
            if (listItem) {
              angular.merge(listItem, data.real_pot);
            }
            growl.success("Pot saved successfully");
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
  }

});