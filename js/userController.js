app.controller('userController', ['$scope', '$rootScope', '$http', '$routeParams', function ($scope, $rootScope, $http, $routeParams) {
    $scope.login = function () {
        $http.post("php/login.php", {
            username: $scope.username,
            password: $scope.password
        }).then(
            function success(data) {
                $rootScope.$broadcast('stateChanged');

            },
            function error(data) {
                var response = data.data;
                if (!response.state) {
                    $scope.errMessage = response.errorMessage;
                }
            }
        );
    }

    $scope.logout = function () {
        $http({
            type: "POST",
            url: "php/logout.php"
        }).then(function success() {
            setTimeout(() => {
                $rootScope.$broadcast('stateChanged');
            }, 2000);
        });
    }

    $scope.signup = function () {
        $http.post("php/signup.php", {
            username: $scope.username,
            email: $scope.email,
            password: $scope.password
        }).then(
            function success(data) {
                $rootScope.$broadcast('stateChanged');
            },
            function error(data) {
                var response = data.data;
                if (!response.state) {
                    $scope.errMessage = response.errorMessage;
                }
            }
        );
    }
}]);