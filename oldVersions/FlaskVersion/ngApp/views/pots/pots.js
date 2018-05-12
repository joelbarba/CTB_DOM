'use strict';

angular.module('myApp.pots', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/pots', {
    templateUrl: 'views/pots/pots.html',
    controller: 'PotsCtrl'
  });
}])
.controller('PotsCtrl', function($scope, growl, $uibModal, $resource) {
  "ngInject";


})


// <real-pots-crud></real-pots-crud>
.component('realPotsCrud', {
    templateUrl: 'views/pots/realPots.html',
    controller: 'realPotsController'
  }
)
.controller('realPotsController', function($scope, growl, $uibModal, $resource) {
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
      templateUrl : 'views/pots/realPotModal.html',
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

})




// <acc-pots-crud></acc-pots-crud>
.component('accPotsCrud', {
    templateUrl: 'views/pots/accPots.html',
    controller: 'accPotsController'
  }
)
.controller('accPotsController', function($scope, growl, $uibModal, $resource) {
  "ngInject";

  var accPotsResource = $resource('/api/v1/acc_pots/:accPotId', { accPotId: '@id' });

  // Load accPots list
  accPotsResource.get({ parent_id: null }, {}, function(data) {
    if (!!data && data.hasOwnProperty('acc_pots')) {
      $scope.accPotsList = angular.copy(data.acc_pots);
    }
  });

})
.component('accPotLevelList', {
    templateUrl: 'views/pots/accPotLevelList.html',
    controller: 'accPotLevelListCtrl',
    controllerAs: "$ctrl",
    bindings: {
      accPotsList: '=',
      level: '@'
    }
  }
)
.controller('accPotLevelListCtrl', function($scope, growl, $uibModal, $resource, $timeout) {
  "ngInject";
  var $ctrl = this;
  var accPotsAPI = $resource('/api/v1/acc_pots/:accPotId', { accPotId: '@id' });

  // Open add Real Pot modal
  $ctrl.openAddModal = function() {
    $ctrl.item = { pos: 1, amount: 0 };
    $ctrl.accPotsList.forEach(function(pot) {
      if ($ctrl.item.pos <= pot.pos) { $ctrl.item.pos = pot.pos + 1; }
    });
    openModal('add', $ctrl.item, $ctrl.accPotsList);
  };

  // Open edit Real Pot modal
  $ctrl.openEditModal = function(selectedItem) {
    accPotsAPI.get({ accPotId: selectedItem.id }, function(data) {
      $ctrl.item = angular.copy(data.acc_pot);
      openModal('edit', $ctrl.item, $ctrl.accPotsList);
    });
  };

  function openModal(task, item, list) {
    $uibModal.open({
      size        : 'md',
      templateUrl : 'views/pots/accPotModal.html',
      resolve     : {
        Task : function() { return task; },
        Item : function() { return item; },
        List : function() { return list; }
      },
      controller  : function($scope, $uibModalInstance, Task, Item, List) {
        "ngInject";

        $scope.task = Task;
        $scope.item = Item;

        $scope.createNewItem = function() {
          accPotsAPI.save($scope.item, function(data) {
            List.push(data.acc_pot);
            growl.success("New Pot created successfully");
            $uibModalInstance.close();
          });
        };

        $scope.saveItem = function() {
          var updatedItem = angular.copy($scope.item);
          updatedItem.amount = Number(updatedItem.amount);
          accPotsAPI.save(updatedItem, function(data) {
            var listItem = List.getById(data.acc_pot.id);
            if (listItem) {
              angular.merge(listItem, data.acc_pot);
            }
            growl.success("Pot saved successfully");
            $uibModalInstance.close();
          }, function(error) {
              growl.error(error.data.error);
            }
          );
        };

        $scope.removeItem = function() {
          accPotsAPI.remove({ accPotId: $scope.item.id }, function() {
            List.removeById($scope.item.id);
            $uibModalInstance.close();
          });
        };

      }
    });
  }

});