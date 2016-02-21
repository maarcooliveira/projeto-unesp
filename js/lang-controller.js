'use strict';

angular.module('nextex', [])
.controller('langController', function($scope, $http){

  $http.get('./data/strings.json').success(function(data) {
    var userLang = navigator.language || navigator.userLanguage;
		var queryDict = {};
		// location.search.substr(1).split("&").forEach(function(item) {queryDict[item.split("=")[0]] = item.split("=")[1]});

		if (userLang.split('-')[0] !== 'pt') {
      $scope.str = data["en-US"];
		}
    else {
      $scope.str = data["pt-BR"];
    }
  });
});
