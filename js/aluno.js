/* NextEx - Ferramenta de Avaliação
 * js/aluno.js
 *
 * Funções de interação com o painel de controle de aluno
*/

$(document).ready(function() {
  var ua = navigator.userAgent.toLowerCase();
  var isAndroid = ua.indexOf("android") > -1; //&& ua.indexOf("mobile");

  if(isAndroid) {
    initAndroid();
  }
});

// Remover o aluno de uma turma
function deixarTurma(turma) {
  var ajaxurl = 'api/deixar_turma.php';
  var data =  {'turma': turma};
  $.post(ajaxurl, data, function (response) {
    // TODO: mostrar erro se response = false
  	location.reload();
  });
}

// Inserir o aluno em uma turma
function entrarNaTurma() {
  var usuario = $('#usuario').val();
  var turma = $('#turma').val();
  var ajaxurl = 'api/participar_turma.php';
  var data =  {'usuario': usuario, 'turma': turma};
  $.post(ajaxurl, data, function (response) {
    // TODO: mostrar erro se response = false
    location.reload();
  });
}

// Remover navbar se o acesso for feito via aplicativo Android
function initAndroid() {
    $('nav').css("display", "none");
    Android.changeMenuContext('removeAll');
}
