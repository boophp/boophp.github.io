/**
 * User Controller
 *
 * @author samhou<samhou1988@gmail.com>
 * @date 2015/08/10 23:15
 */
;(function () {
  angular
    .module('myApp')
    .controller('UserController', ['$scope', '$routeParams',
    function ($scope, $routeParams) {
      var userId = $routeParams.id;
      $scope.id = userId;
    }]);
})();
