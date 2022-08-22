app.controller('bookDetailsController', ['$scope','$http', '$routeParams', function ($scope, $http, $routeParams) {

  $scope.isLogin = false; //chack if user login then make it true

  $scope.getNumber = function (item) {
    if (item.color) {
      return item.color;
    }
    var hue = Math.floor(Math.random() * 360);
    var color = `hsl(${hue}, 70%, 80%)`;
    //var color = "#" + ((1 << 24) * Math.random() | 0).toString(16);
    item.color = color;
    return color;
  };

  //function δανεισμού βιβλίου
  //αυτό το fuction στέλνει το ID του βιβλίου, τίτλο, το όνομα του συγγραφέα και την ημερομηνία επιστροφής
  //στην php για να δημιουργίσει μια καταγραφή στον πίνακα bookings
  $scope.addBooking = function (bookID, title, author) {
    bookingDate = new Date();
    returnDate = new Date();
    returnDate.setDate(bookingDate.getDate() + 30);

    $http({
      method: "POST",
      url: "php/bookDetails.php",
      data: {
        'bookID': bookID,
        'title': title,
        'author': author,
        'returnDate': Math.floor(returnDate.getTime() / 1000),
        'type' : 'addBooking'
      }
    }).then(function success() {
        $scope.isBooking = true;
    }, function error() {
      console.log('error');
    });
  }

  //function επιστροφής δανεισμένου βιβλίου
  //αυτό το fuction στέλνει το ID του βιβλίου στην php
  //για να αφαιρέσει την κατάλληλη καταγραφή από τον πίνακα bookings
  $scope.returnBooking = function (bookID) {

    $http({
      method: "DELETE",
      url: "php/bookDetails.php",
      data: {
        'bookID': bookID,
        'type' : 'returnBooking'
      }
    }).then(function success() {
        $scope.isBooking = false;
    }, function error() {
      console.log('error');
    });
  }

  //function προσθήκης βιβλίου στα αγαπημένα
  //αυτό το fuction στέλνει το ID του βιβλίου, τον τίτλο και το όνομα του συγγραφέα
  //στην php για να δημιουργίσει μια καταγραφή στον πίνακα favorites
  $scope.addFavorite = function (bookID, title, author) {

    $http({
      method: "POST",
      url: "php/bookDetails.php",
      data: {
        'bookID': bookID,
        'title': title,
        'author': author,
        'type': 'addFavorite'
      }
    }).then(function success() {
        $scope.isFavorite = true;
    }, function error() {
      console.log('error');
    });
  }

  //function αφαίρεσης βιβλίου από τα αγαπημένα
  //αυτό το fuction στέλνει το ID του βιβλίου στην php
  //για να αφαιρέσει την κατάλληλη καταγραφή από τον πίνακα favorites
  $scope.removeFavorite = function (bookID) {

    $http({
      method: "DELETE",
      url: "php/bookDetails.php",
      data: {
        'bookID': bookID,
        'type' : 'removeFavorite'
      }
    }).then(function success() {
      $scope.isFavorite = false;
    }, function error() {
      console.log('error');
    });
  }

  //το παρακάτω fucntion κανει get από το API books χρησιμοποιόντας
  //το ID του βιβλίου που έχει λάβει ως παράμετρο (routeParams)
  //στη συνέχεια κάνει get από το API works χρησιμοποιόντας
  //το works.key που έχει λάβει από το προηγούμενο get
  //τέλος, κάνει get από το API authors χρησιμοποιόντας
  //author.authors.key που έχει λάβει από το προηγούμενο get
  $http({
    method: "GET",
    url: "https://openlibrary.org/books/" + $routeParams.bookID + ".json"
  }).then(function success(response) {
    $scope.bookDetails = response.data;
    $http({
      method: "GET",
      url: "https://openlibrary.org" + $scope.bookDetails.works[0].key + ".json"
    }).then(function success(response) {
      $scope.workDetails = response.data;
      $http({
        method: "GET",
        url: "https://openlibrary.org" + $scope.workDetails.authors[0].author.key + ".json"
      }).then(function success(response) {
        $scope.authorDetails = response.data;
      }, function error(response) {
        $scope.authorDetails = response.statusText;
      });
    }, function error(response) {
      $scope.workDetails = response.statusText;
    });
  }, function error(response) {
    $scope.bookDetails = response.statusText;
  });

  //το παρακάτω fucntion κανει post στην php
  //το ID του βιβλίου που έχει λάβει ως παράμετρο (routeParams)
  //για να δει αν είναι καταχωρημένο στον πίνακα bookings
  //στη συνέχεια κάνει get από την php τα αποτελέσματα
  $http({
    method: "GET",
    url: "php/bookDetails.php",
    params: {
      'bookID': $routeParams.bookID
    }
  }).then(function success(response) {
      var actualData = response.data.payload;
      if (actualData.bookStates.length > 0) {
          $scope.isBooking = actualData.bookStates[0].booked;
          $scope.isFavorite = actualData.bookStates[0].favorited;
      }
  }, function error() {
    console.log('error');
  });

}]);