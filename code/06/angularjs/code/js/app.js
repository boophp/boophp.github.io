/**
 * Application Run
 *
 * @author samhou<samhou1988@gmail.com>
 * @date 2015/08/10 20:00
 */
;(function () {
  angular
    .module('myApp', ['ngRoute', 'ngToast', function (a, ngToast))
    .controller('MyCtrl',
    function ($scope) {
      $scope.clock = {
        now: new Date()
      };

      var updateClock = function () {
        $scope.clock.now = new Date();
      };

      setInterval(function () {
        $scope.$apply(updateClock);
      }, 1000)

      updateClock();
    });
}])();
