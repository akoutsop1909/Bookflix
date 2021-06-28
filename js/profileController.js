app.controller('profileController', ['$scope', '$route', '$http', function($scope, $route, $http) {

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
    }).then(function success(response) {
      $scope.userData.bookings = [...$scope.userData.bookings,response.data.payload];
      $scope.userData.bookStates.filter(b=>b.bookID === bookID)[0].booked = true;
    }, function error(response) {
      alert(response.data.errorMessage);
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
      $scope.userData.bookings = $scope.userData.bookings.filter(b=>b.book.bookID !== bookID);
      $scope.userData.bookStates.filter(b=>b.bookID === bookID)[0].booked = false;
    }, function error(response) {
      alert(response.data.errorMessage);
    });

  }

  //function προσθήκης βιβλίου στα αγαπημένα
  //αυτό το fuction στέλνει το ID του βιβλίου, τον τίτλο και το όνομα του συγγραφέα
  //στην php για να δημιουργίσει μια καταγραφή στον πίνακα favorites
  $scope.addFavorite = function (book) {

    $http({
      method: "POST",
      url: "php/bookDetails.php",
      data: {
        'bookID': book.bookID,
        'title': book.title,
        'author': book.author,
        'type': 'addFavorite'
      }
    }).then(function success(response) {
      $scope.userData.favorites = [...$scope.userData.favorites,response.data.payload];
      $scope.userData.bookStates.filter(b=>b.bookID === book.bookID)[0].favorited = true;
    }, function error(response) {
      alert(response.data.errorMessage);
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
      $scope.userData.favorites = $scope.userData.favorites.filter(b=>b.bookID !== bookID);
      $scope.userData.bookStates.filter(b=>b.bookID === bookID)[0].favorited = false;
    }, function error(response) {
      alert(response.data.errorMessage);
    });

  }

  $scope.isBooked = function (bookID) {
    return $scope.userData.bookStates.filter(o=>o.bookID === bookID && o.booked).length>0;
  }
  $scope.isFavorited = function (bookID) {
    return $scope.userData.bookStates.filter(o=>o.bookID === bookID && o.favorited).length>0;
  }

  //το παρακάτω fucntion κάνει get από την php
  //τα στοιχεία του πίνακα bookings και favorites
  $http({
    method : "GET",
    url : "php/profile.php"
  }).then(function success(response) {
    $scope.userData = response.data.payload; //η php στέλνει δεδομένα τύπου {payload:{}}
  }, function error(response) {
    alert(response.data.errorMessage);
  });

}]);