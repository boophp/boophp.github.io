/**
 * xhr
 *
 * @author samhou<samhou1988@gmail.com>
 * @date 2015/08/11 21:21
 */
;(function () {
  angular
    .module('myApp', [])
    .controller('MyController', ['$scope', '$http',
    function ($scope, $http) {
      var persons = [];

      // fetch data from server side
      $http.get('/person.json').
        then(function(data) {
          console.log(data);
          if (data.data) {

            persons = persons.concat(data.data);
            $scope.persons = persons;
          }
        }, function(data) {
          console.error(data)
        });
    }]);
})();
