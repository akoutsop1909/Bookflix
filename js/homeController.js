app.controller('homeController', ['$scope', '$http', function($scope, $http) {
  
    //η παρακάτω λίστα περιέχει 3 ID βιβλίων
    someBookID = ["OL11665757M", "OL384019M", "OL496851M"];
    //η παρακάτω λίστα θα φιλοξενίσει τα αποτελέσματα από το API books
    $scope.someBook = [];
    //η παρακάτω λίστα θα φιλοξενίσει τα αποτελέσματα από το API works
    $scope.someWork = [];
 
    //το παρακάτω fucntion κανει get από το API books χρησιμοποιόντας
    //το ID του βιβλίου από το someBookID
    //στη συνέχεια κάνει get από το API works χρησιμοποιόντας
    //το works.key που έχει λάβει από το προηγούμενο get
    //αυτό πραγματοποιείται 3 φορές για το κάθε ID του someBookID
    for (i=0; i<=2; i++) {
      $http({
          method : "GET",
          url : "https://openlibrary.org/books/"+someBookID[i]+".json"
        }).then(function success(response) {
            $scope.someBook.push(response.data);
            someWorkKey = response.data.works[0].key;
            $http({
              method : "GET",
              url : "https://openlibrary.org"+someWorkKey+".json"
            }).then(function success(response) {
                $scope.someWork.push(response.data);
              }, function error(response) {
                $scope.someWork.push(response.statusText);
            });
          }, function error(response) {
            $scope.someBook.push(response.statusText);
        });
    }

    //η παρακάτω λίστα περιέχει 9 ID βιβλίων με τις καλύτερες πωλήσεις
    bestBookID = ["OL9708865M", "OL376045M", "OL25430837M", "OL25916870M", "OL3945760M", "OL7512267M", "OL9379568M", "OL24876352M", "OL28322906M"];
    //η παρακάτω λίστα θα φιλοξενίσει τα αποτελέσματα από το API books
    $scope.bestBook = [];
    //η παρακάτω λίστα θα φιλοξενίσει τα αποτελέσματα από το API works
    $scope.bestWork = [];
    //η παρακάτω λίστα θα φιλοξενίσει τα αποτελέσματα από το API authors
    $scope.bestAuthor = [];

    //το παρακάτω fucntion κανει get από το API books χρησιμοποιόντας
    //το ID του βιβλίου από το bestBookID
    //στη συνέχεια κάνει get από το API works χρησιμοποιόντας
    //το works.key που έχει λάβει από το προηγούμενο get
    //τέλος, κάνει get από το API authors χρησιμοποιόντας
    //author.authors.key που έχει λάβει από το προηγούμενο get
    for (i=0; i<=8; i++) {
      $http({
          method : "GET",
          url : "https://openlibrary.org/books/"+bestBookID[i]+".json"
        }).then(function success(response) {
          $scope.bestBook.push(response.data);
          bestWorkKey = response.data.works[0].key;
            $http({
              method : "GET",
              url : "https://openlibrary.org"+bestWorkKey+".json"
            }).then(function success(response) {
                $scope.bestWork.push(response.data);
                $http({
                  method : "GET",
                  url : "https://openlibrary.org"+response.data.authors[0].author.key+".json"
                }).then(function success(response) {
                    $scope.bestAuthor.push(response.data);
                  }, function error(response) {
                    $scope.bestAuthor.push(response.statusText);
                });
              }, function error(response) {
                $scope.bestWork.push(response.statusText);
            });
          }, function error(response) {
            $scope.bestBook.push(response.statusText);
        });
    }
  }]);