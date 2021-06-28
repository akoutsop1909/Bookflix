var app = angular.module("Bookflix",
    ['ngResource', 'ngRoute', 'ngCookies']);

app.config(function ($routeProvider) {
    $routeProvider
        .when('/', {
            url: "/home",
            templateUrl: './views/home.html',
            controller: 'homeController'
        })
        .when("/login", {
            url: "/login",
            templateUrl: "./views/login.html",
            controller: "userController"
        })
        .when("/logout", {
            url: "/logout",
            templateUrl: "./views/logout.html",
            controller: "userController"
        })
        .when("/signup", {
            url: "/signup",
            templateUrl: "./views/signup.html",
            controller: "userController"
        })
        .when("/library", {
            url: "/library",
            templateUrl: "./views/library.html",
            // controller: "libraryController"
        })
        .when("/bookList/:subject", {
            url: "/bookList/:subject",
            templateUrl: "./views/bookList.html",
            controller: "bookListController"
        })
        .when("/bookDetails/:bookID", {
            url: '/bookDetails/:bookID',
            templateUrl: "./views/bookDetails.html",
            controller: "bookDetailsController"
        })
        .when("/contactForm", {
            url: "/contactForm",
            templateUrl: "./views/contactForm.html",
            // controller: "libraryController"
        })
        .when("/profile", {
            url: "/profile",
            templateUrl: "./views/profile.html",
            controller: "profileController"
        })
        .when("/bookReader/:bookID", {
            url: "/bookReader/:bookID",
            templateUrl: "./views/bookReader.html",
            controller: "bookReaderController"
        })
        .when("/about", {
            url: "/about",
            templateUrl: "./views/about.html",
        })
        .otherwise({
            redirectTo: "/"
        });


});

app.controller("mainController",
    function ($scope, $rootScope, $http, $location, $cookies) {

        //στο παρακάτω function κάνει get από την php 
        //που περιέχει τα στοιχεία ενός συνδεδεμένου χρήστη
        $http({
            method : "GET",
            url : "php/me.php"
          }).then(function success(response) {
              $rootScope.user = response.data.payload; //η php στέλνει δεδομένα τύπου {payload:{}}
           }, function error(response) {
        });

        //στο παρακάτω function γίνεται refresh η σελίδα
        //όταν ο χρήστης κάνει login, logout ή signup
        //για να ενημερωθεί το navigation bar με τα κατάλληλα links
        $rootScope.$on('stateChanged', function () {
            window.location.reload();
        });

        $scope.bfbodyClass = 'login-body';
        $rootScope.fullVersion = "1.0.0.0"; // version of api

        $location.path('/home');

        //cookies for login 
    });
