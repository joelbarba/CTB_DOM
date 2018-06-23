'use strict';

angular.module('myApp.pots', ['ngRoute'])

.config(function($routeProvider) {
  "ngInject";
  $routeProvider.when('/pots', {
    templateUrl: 'views/pots/pots.html',
    controller: 'PotsCtrl'
  });
})
.controller('PotsCtrl', function() {
  "ngInject";

})


// <real-pots-crud></real-pots-crud>
.component('realPotsCrud', {
    templateUrl: 'views/pots/realPots.html',
    controller: 'realPotsController'
  }
)
.controller('realPotsController', function($scope, $rootScope, growl, $uibModal, $resource) {
  "ngInject";

  var realPotsResource = $resource($rootScope.apiURL + '/api/v1/real_pots/:realPotId', 
    { realPotId: '@id' }, 
    { withCredentials: false,
      patch: { method: 'PATCH' }
    });

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
          realPotsResource.patch(updatedItem, function(data) {
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
    controller: 'accPotsController',
    controllerAs: '$ctrl'
  }
)
.controller('accPotsController', function($scope, growl, $uibModal, AccPotsAPI, AccPotsService) {
  "ngInject";
  var $ctrl = this;

  AccPotsService.loadAccPots().then(function() {
    $ctrl.accPotsList = AccPotsService.accPotsList;
    $ctrl.accPotsFlatList = AccPotsService.accPotsFlatList;
  });

  // Open add Acc Pot modal
  $ctrl.openAddModal = function() {
    var item = { pos: 1, amount: 0 };
    $ctrl.accPotsList.forEach(function(pot) {
      if (item.pos <= pot.pos) { item.pos = pot.pos + 1; }
    });
    AccPotsService.openModal('addRoot', item, $ctrl.accPotsList);
  };

})
.component('accPotLevelList', {
    templateUrl: 'views/pots/accPotLevelList.html',
    controller: 'accPotLevelListCtrl',
    controllerAs: "$ctrl",
    bindings: {
      accPotsList: '=',
      parentPot: '<?',
      level: '@'
    }
  }
)
.controller('accPotLevelListCtrl', function($scope, $rootScope, growl, $uibModal, AccPotsAPI, AccPotsService) {
  "ngInject";
  var $ctrl = this;

  // Open add sub Acc Pot modal
  $ctrl.openAddModal = function() {
    $ctrl.item = { pos: 1, amount: 0, parent_name: $ctrl.parentPot.name, parent_id: $ctrl.parentPot.id };
    $ctrl.accPotsList.forEach(function(pot) {
      if ($ctrl.item.pos <= pot.pos) { $ctrl.item.pos = pot.pos + 1; }
    });
    AccPotsService.openModal('addSub', $ctrl.item);
  };

  // Open edit Acc Pot modal
  $ctrl.openEditModal = function(selectedItem) {
    AccPotsAPI.get({ accPotId: selectedItem.id }, function(data) {
      $ctrl.item = angular.copy(data.acc_pot);
      AccPotsService.openModal('edit', $ctrl.item, $ctrl.parentPot).then(function(res) {
        // console.log(res);
      });
    });
  };

})


.service('AccPotsAPI', function($rootScope, $resource) {
  "ngInject";
  return $resource($rootScope.apiURL + '/api/v1/acc_pots/:accPotId', 
  { accPotId: '@id' }, 
  { withCredentials: false,
    patch: { method: 'PATCH' }
  });
})
.service('AccPotsService', function($rootScope, growl, $uibModal, AccPotsAPI, $timeout) {
  "ngInject";

  var AccPotsService = {
    accPotsList     : [], // recursive list
    accPotsFlatList : []  // flat list (everything at the same level)
  };

  // Load accPots list
  AccPotsService.loadAccPots = function() {
    return AccPotsAPI.get({ parent_id: null }, {}).$promise.then(function(data) {
      if (!!data && data.hasOwnProperty('acc_pots')) {
        AccPotsService.accPotsList = angular.copy(data.acc_pots);
        
        AccPotsService.accPotsFlatList = [];
        flatenLevel(AccPotsService.accPotsList, '', 0);
        
        function flatenLevel(list, parentPos, level) {
          list.sort(function(itemA, itemB) {
            return itemA.pos > itemB.pos;
          });
          list.forEach(function(pot) {
            pot.level = level;
            
            var flatPot = angular.copy(pot);
            flatPot.fullPos = (parentPos ? (parentPos + '.') : '') + pot.pos;
            flatPot.displayName = flatPot.fullPos + '. ' + flatPot.name;
            flatPot.displayTabName = ' '.repeat(level * 4) + flatPot.fullPos + '. ' + flatPot.name;
            delete flatPot.children;
            AccPotsService.accPotsFlatList.push(flatPot);
            if (!!pot.children.length) {
              flatenLevel(pot.children, flatPot.fullPos, level+1);
            }  
          });
        }
      }
    });
  }

  // Returns the Acc pot finding recursively by ID
  AccPotsService.getPotById = function(id) {
    return findRecursivePot(AccPotsService.accPotsList);
    function findRecursivePot(levelList) {
      for (var ind = 0; ind < levelList.length; ind++) {
        var pot = levelList[ind];
        if (pot.id === id) {
          return pot;
        } else {
          if (!!pot.children.length) {
            var result = findRecursivePot(pot.children);
            if (!!result) {
              return result;
            }
          }
        }
      }
      return null;
    }

  };

  AccPotsService.openModal = function(task, item, parentPot) {
    return $uibModal.open({
      size        : 'md',
      templateUrl : 'views/pots/accPotModal.html',
      resolve     : {
        Task      : function() { return task; },
        Item      : function() { return item; },
        ParentPot : function() { return parentPot; }
      },
      controller  : function($scope, $uibModalInstance, Task, Item, ParentPot, AccPotsService) {
        "ngInject";

        $scope.task = Task;
        $scope.item = Item;
        $scope.accPotsFlatList = angular.copy(AccPotsService.accPotsFlatList);
        $scope.accPotsFlatList.push({id:null, name:'No Parent', pos: 0});
        $scope.accPotsFlatList.sort(function(itemA, itemB) {
          return itemA.pos - itemB.pos;
        })

        $scope.createNewItem = function() {
          AccPotsAPI.save($scope.item, function(data) {
            // var parent = AccPotsService.accPotsList.getById($scope.item.parent_id);
            var parent = AccPotsService.getPotById($scope.item.parent_id);
            if (parent) {
              parent.children.push(data.acc_pot);
            } else {
              AccPotsService.accPotsList.push(data.acc_pot);
            }
            AccPotsService.accPotsFlatList.push(data.acc_pot);

            growl.success("New Pot created successfully");
            $uibModalInstance.close(data.acc_pot);
          });
        };

        $scope.saveItem = function() {
          var updatedItem = angular.copy($scope.item);
          updatedItem.amount = Number(updatedItem.amount);
          AccPotsAPI.patch(updatedItem, function(data) {
            var listItem = AccPotsService.getPotById(data.acc_pot.id);
            if (listItem) {
              angular.merge(listItem, data.acc_pot);
            }
            
            updatePot(data.acc_pot);
            function updatePot(updatedPot) {
              var listItem = AccPotsService.getPotById(updatedPot.id);
              if (listItem) {
                listItem.amount = updatedPot.amount;
              }
              if (!!updatedPot.parent.id) {
                updatePot(updatedPot.parent);
              }
            }

            growl.success("Pot saved successfully");
            $uibModalInstance.close(data.acc_pot);
          }, function(error) {
              growl.error(error.data.error);
            }
          );
        };

        $scope.removeItem = function() {
          AccPotsAPI.remove({ accPotId: $scope.item.id }, function() {
            AccPotsService.accPotsFlatList.removeById($scope.item.id);
            var parent = AccPotsService.accPotsList.getById($scope.item.parent_id);
            if (parent) {
              parent.children.removeById($scope.item.id);
            } else {
              AccPotsService.accPotsList.removeById($scope.item.id);
            }
            $uibModalInstance.close();
          });
        };

      }
    }).result;
  };

  return AccPotsService;
});