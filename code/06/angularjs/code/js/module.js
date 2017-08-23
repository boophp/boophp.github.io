/**
 * module declear or reference
 *
 * @author samhou<samhou1988@gmail.com>
 * @date 2015/08/10 20:30
 */
;(function () {

  // module declear
  angular.module('myApp', []);

  var myApp = angular.module('myApp').run(['$rootScope', function ($rootScope) {
    $rootScope.title = 'module demo'
  }]);
})();
