/**
 * angularjs filter
 *
 * @author samhou<samhou1988@gmail.com>
 * @date 2015/08/10 22:10
 */
;(function () {
  angular
    .module('myApp', [])
    .controller('MyController', ['$scope', function ($scope) {
      $scope.amount = 123456.00;
      $scope.today = new Date();
    }])
    .filter('truncate', function () {
      return function (text, length) {
        if (text.length > length) {
          return text.substring(0, length) + '...';
        } else {
          return text;
        }
      }
    })
})();
