/**
 * Angularjs Controller
 *
 * @author samhou<samhou1988@gmail.com>
 * @date 2015/08/10 20:56
 */
;(function () {
  angular
    .module('myApp', [])
    .controller('MyController',
    function ($scope) {
      $scope.counter = 0;

      // add counter
      $scope.add = function (num) {
        $scope.counter += num * 1;
      }

      // minus counter
      $scope.minus = function (num) {
        $scope.counter -= num;
      }
      var person = {
        name: 'samhou',
        age: 27,
        height: '176cm'
      };
      $scope.person = person;
    });
})();
