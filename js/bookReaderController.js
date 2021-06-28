app.controller('bookReaderController', ['$scope', '$http','$routeParams', function($scope, $http, $routeParams) {
 

  //το παρακάτω fucntion κανει get από το API books χρησιμοποιόντας
  //το ID του βιβλίου που έχει λάβει ως παράμετρο (routeParams)
  //στη συνέχεια κάνει get από το API works χρησιμοποιόντας
  //το works.key που έχει λάβει από το προηγούμενο get
  $http({
      method : "GET",
      url : "https://openlibrary.org/books/" + $routeParams.bookID + ".json"
    }).then(function success(response) {
        $scope.bookReader = response.data;
        $http({
          method : "GET",
          url : "https://openlibrary.org" + $scope.bookReader.works[0].key + ".json"
        }).then(function success(response) {
            $scope.workReader = response.data;
          }, function error(response) {
            $scope.workReader = response.statusText;
        });
      }, function error(response) {
        $scope.bookReader = response.statusText;
    });
}]);