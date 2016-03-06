/* NextEx - Ferramenta de Avaliação
 * js/lang-controller.js
 *
 * Controle do idioma da interface utilizando Angular.js. Carrega frases em portugês
 * caso o navegador do usuário esteja neste idioma e em inglês caso contrário
*/

'use strict';

angular.module('nextex', [])
.controller('langController', function($scope, $http){

  $http.get('./data/strings.json').success(function(data) {
    var userLang = navigator.language || navigator.userLanguage;
		var queryDict = {};

		if (userLang.split('-')[0] !== 'pt') {
      $scope.str = data["en-US"];
		}
    else {
      $scope.str = data["pt-BR"];
    }
  });
});
