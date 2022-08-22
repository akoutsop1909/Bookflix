app.controller('bookListController', ['$scope', '$http','$routeParams', function($scope, $http, $routeParams) {
 
  $scope.ref = {loading : true}; 

  //το παρακάτω fucntion κανει get από το API subjects χρησιμοποιόντας
  //το subject που έχει λάβει ως παράμετρο (routeParams)
  $http({
      method : "GET",
      url : "https://openlibrary.org/subjects/"+$routeParams.subject+".json?limit=60"
    }).then(function success(response) {
        $scope.bookList = response.data;
        $scope.ref.loading = false; 
      }, function error(response) {
        $scope.bookList = response.statusText;
    });
}]);