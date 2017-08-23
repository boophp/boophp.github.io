/**
 * angularjs scope
 *
 * @author samhou<samhou1988@gmail.com>
 * @date 2015/08/10 20:40
 */
;(function () {

  // module declear
  angular.module('myApp', [])
    .controller('ParentCtrl', ['$scope',
    function ($scope) {
      $scope.name = 'parent';
      $scope.sex = 'Male';
    }])
    .controller('ChildCtrl', ['$scope',
    function ($scope) {
      $scope.name = 'child';
    }]);
})();
