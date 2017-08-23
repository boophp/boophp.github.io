/**
 * Angular Directive
 *
 * @author samhou<samhou1988@gmail.com>
 * @date 2015/08/10 22:30
 */
;(function () {
  angular
    .module('myApp', [])
    .controller('MyController', ['$scope',
    function ($scope) {
      $scope.show = true;
      $scope.checked = true;
      $scope.user = {
        id: 37
      }
    }])
    .directive('myDirective', function () {
      return {
        restrict: 'EA',
        replace: true,
        template: '<a href="{{myUrl}}">{{myLinkText}}({{userId}})</a>',
        scope: {
          myUrl: '@',
          myLinkText: '@',
          userId: '@myUserId'
        }
      }
    });
})()
