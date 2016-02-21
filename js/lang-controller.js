'use strict';

angular.module('nextex', [])
.controller('langController', function($scope, $http){

  $http.get('./data/strings.json').success(function(data) {
    // $scope.str = data["pt-BR"];
    $scope.str = data["en-US"];
  });
});
