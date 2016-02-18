$(document).ready(function() {
  var ua = navigator.userAgent.toLowerCase();
  var isAndroid = ua.indexOf("android") > -1; //&& ua.indexOf("mobile");

  if(isAndroid) {
    initAndroid();
  }
});

function deixarTurma(turma) {
  var ajaxurl = 'api/deixar_turma.php';
  var data =  {'turma': turma};
  $.post(ajaxurl, data, function (response) {
    // TODO: mostrar erro se response = false
  	location.reload();
  });
}

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

function initAndroid() {
    $('nav').css("display", "none");
    Android.changeMenuContext('removeAll');
}
