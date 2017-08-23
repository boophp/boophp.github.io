/**
 * Application Run here
 *
 * @author samhou<samhou1988@gmail.com>
 * @date 2015/08/10 23:00
 */
;(function () {
  angular
    .module('myApp', ['ngRoute'])
    .config(['$routeProvider', '$locationProvider',
    function ($routeProvider, $locationProvider) {
      $locationProvider.html5Mode(false);
      $locationProvider.hashPrefix('!');

      $routeProvider
        .when('/', {
          templateUrl: 'views/home.html'
        })
        .when('/about', {
          templateUrl: 'views/about.html'
        })
        .when('/user', {
          templateUrl: 'views/user.html'
        })
        .when('/user/:id', {
          templateUrl: 'views/user.html',
          controller: 'UserController'
        })
        .otherwise({
          redirectTo: '/'
        })
    }])
    .run(['$rootScope', function ($rootScope) {
      $rootScope.title = 'route demo'
    }]);
})();
