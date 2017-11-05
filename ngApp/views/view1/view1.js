'use strict';

angular.module('myApp.view1', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/view1', {
    templateUrl: 'views/view1/view1.html',
    controller: 'View1Ctrl'
  });
}])

.controller('View1Ctrl', function($scope, $uibModal, $resource) {
  "ngInject";

  var tasksResource = $resource('/api/v1/tasks/:taskId', { taskId: '@id' });


  // Load tasks list
  tasksResource.get(function(data) {
    if (!!data && data.hasOwnProperty('tasks')) {
      $scope.tasksList = angular.copy(data.tasks);
    }
  });

  // Open add Task modal
  $scope.openAddModal = function() {
    $scope.task = 'add';
    $scope.item = {};
    openModal();
  };

  // Open edit Task modal
  $scope.openEditModal = function(selectedItem) {
    $scope.task = 'edit';
    tasksResource.get({ taskId: selectedItem.id }, function(data) {
      $scope.item = angular.copy(data.task);
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
          tasksResource.save($scope.item, function(data) {
            $scope.tasksList.push(data.task);
            $uibModalInstance.close();
          });
        };

        $scope.saveItem = function() {
          delete $scope.item.done;
          tasksResource.save($scope.item, function(data) {
            var listItem = $scope.tasksList.getById(data.task.id);
            if (listItem) {
              angular.merge(listItem, data.task);
            }
            $uibModalInstance.close();
          });
        };

        $scope.removeItem = function() {
          tasksResource.remove({ taskId: $scope.item.id }, function() {
            $scope.tasksList.removeById($scope.item.id);
            $uibModalInstance.close();
          });
        };

      }
    });
  }

});