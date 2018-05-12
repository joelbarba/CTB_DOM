'use strict';

var jDirectives = angular.module('myApp.jDirectives', []);

jDirectives.directive('navbar', function() {
  return {
    restrict: 'AE',
    replace: true,
    scope: {},
    templateUrl: 'components/jDirectives/views/navbar.html',
    controller: function() {

    }
  };
});



/**
 * @ngdoc directive
 * @name confirm
 * @requires $uibModal
 * @param {confirm=} confirm - (Optional) Text to display
 * @description
 *  Opens a modal to confirm an action
 * @example
 */
jDirectives.directive('confirm', ['$uibModal', function($uibModal) {
  return {
    priority: 1,
    restrict: 'A',
    scope: {
      confirm: '@?',
      ngClick: '&'
    },
    link: function(scope, element, attrs) {
      element.unbind('click').bind('click', function($event) {
        $event.preventDefault();

        var modalInstance = $uibModal.open({
          animation: true,
          ariaLabelledBy: 'modal-title',
          ariaDescribedBy: 'modal-body',
          templateUrl: 'components/jDirectives/views/confirm.html',
          size: 'sm',
          resolve: {
            Message: function () {
              return scope.confirm;
            }
          },
          controller: function($scope, Message, $uibModalInstance) {
            "ngInject";
            $scope.message = Message || 'Are you sure?';
            $scope.ok = function() {
              $uibModalInstance.close('ok');
            };
            $scope.cancel = function() {
              $uibModalInstance.dismiss();
            };
          }
        }).result.then(function(res) {
          // ok
          scope.ngClick();
        }).catch(function(res) {
          // cancel
          // console.log('modal canceled');
        });
      });

    }
  };
}]);


/**
 * @ngdoc directive
 * @name jDirectives.listColTitle
 * @param {String} titleText The title to show (name of the label to translate).
 * @param {String} [field] It must be defined if we want to set a sortable column. The name of the data field for this column. It'll be used to order the list by.
 * @param {Object} [orderParams] It must be linked if we want to set a sortable column. Object with the properties used to order the list.
 * @param {Object} [orderParams.orderByField] Name of the variable that gives the values to the column.
 * @param {Object} [orderParams.reverseSort] Direction of the order (false=asc, true=desc).
 * @param {Object} [orderParams.orderFunPre] Function to execute just before reordering the list (on column header click).
 * @param {Object} [orderParams.orderFunPost] Function to execute after reordering the list (on column header click).
 * @description
 * This directive generates one column title for a common caption list
 * It can be a sortable column or not. For sortable columns the filed parameter and orderParams must be set.
 *
 * @example
 * // Sortable column:
 * <div class="col-md-3" list-col-title title-text="view.common.name"
 *      field="function_type" order-params="orderParams"></div>
 *
 * @example
 * // Non sortable column:
 * <div class="col-md-3" list-col-title title-text="view.common.name"></div>
 *
 * @example
 * // Usage for the ngRepeat:
 * // To order the list into the dirPaginate directive, use the linked object as:
 * <li dir-paginate="item in itemsList | filter: searchBox | orderBy: orderParams.orderByField: orderParams.reverseSort | itemsPerPage: 10">
 *
 * @example
 * // Custom additional logic:
 * $scope.orderParams = {};
 * $scope.orderParams.orderFunPre = function(oldOrderByField, oldReverseSort) {
   *   console.log('Executed before reordering', oldOrderByField);
   * };
 * $scope.orderParams.orderFunPost = function(newOrderByField, newReverseSort) {
   *   console.log('Executed after reordering', newOrderByField);
   * };
 *
 */
jDirectives.directive('listColTitle', [
function () {
  return {
    restrict    : 'A',
    replace     : true,
    scope       : {
      titleText   : '@',
      field       : '@',
      orderParams : '='
    },
    template: function(tElem, tAttrs) {

      var templateHtml = '';
      if (tAttrs.orderParams) { // Sortable column title
        templateHtml =
          '<div ng-click="orderColumn()">' +
          ' <h4 class="sortable-col-header">' +
          '    <span>{{titleText}}</span>' +
          '    <span ng-show="orderParams.orderByField !== field">' +
          '      <span class="glyphicon glyphicon-sort"></span>' +
          '    </span>' +
          '    <span ng-show="orderParams.orderByField == field">' +
          '      <span ng-show="!orderParams.reverseSort" class="glyphicon glyphicon-sort-by-attributes"></span>' +
          '      <span ng-show="orderParams.reverseSort"  class="glyphicon glyphicon-sort-by-attributes-alt"></span>' +
          '    </span>' +
          '  </h4>' +
          '</div>';

      } else {  // Non sortable column title
        templateHtml = '<h4><span>{{titleText}}</span></h4>';
      }
      return templateHtml;
    },
    link: function($scope, element, attr) {

      if (attr.orderParams) { // Logic for sortable columns

        if (!$scope.orderParams) {  // If the order parameter object doesn't exists, the first render will set it
          $scope.orderParams = {
            orderByField: $scope.field,
            reverseSort:  false
          };
        }
        $scope.orderColumn = function() {

          // If there is a custom function for ordering, call it
          if (typeof $scope.orderParams.orderFunPre === 'function') {
            $scope.orderParams.orderFunPre($scope.orderParams.orderByField, $scope.orderParams.reverseSort);
          }

          // If the order field is the same, revert the order. Otherwise (changing the field), set it always false (as a default order)
          if ($scope.orderParams.orderByField !== $scope.field) {
            $scope.orderParams.reverseSort = false;
          } else {
            $scope.orderParams.reverseSort = !$scope.orderParams.reverseSort;
          }

          $scope.orderParams.orderByField = $scope.field;

          // If there is a custom function for ordering, call it
          if (typeof $scope.orderParams.orderFunPost === 'function') {
            $scope.orderParams.orderFunPost($scope.orderParams.orderByField, $scope.orderParams.reverseSort);
          }

        };
      }
    }
  };
}]);
