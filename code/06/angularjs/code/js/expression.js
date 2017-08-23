/**
 * Angularjs Expression
 *
 * @author samhou<samhou1988@gmail.com>
 * @date 2015/08/10 21:05
 */
;(function () {
  angular
    .module('myApp', [])
    .controller('MyController',
    function ($scope, $parse) {
      $scope.$watch('expr', function (newVal, oldVal, scope) {
        if (newVal !== oldVal) {
          var parseFun = $parse(newVal);
          $scope.parseValue = parseFun(scope);
        }
      })
    });
})();
