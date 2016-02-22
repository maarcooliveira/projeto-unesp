var str;

$(document).ready(function() {
  getInterfaceStr();
});

function getInterfaceStr() {
  $.getJSON( "./data/strings.json", function(data) {
    var userLang = navigator.language || navigator.userLanguage;

    if (userLang.split('-')[0] !== 'pt') {
      str = data["en-US"];
    }
    else {
      str = data["pt-BR"];
    }
  });
}

function removerAtividade(atividade) {
  var ajaxurl = 'api/remover_atividade.php';
  var data =  {'atividade': atividade};
  var res = window.confirm(str.confirma_excluir_atividade);
  if (res) {
    $.post(ajaxurl, data, function (response) {
      // TODO: mostrar erro se response retornar false
      location.reload();
    });
  }
}

function removerTurma(turma) {
  var ajaxurl = 'api/remover_turma.php';
  var data =  {'turma': turma};
  var res = window.confirm(str.confirma_excluir_turma);
  if (res) {
    $.post(ajaxurl, data, function (response) {
      // TODO: mostrar erro se response retornar false
      location.reload();
    });
  }
}

function liberar(id) {
    var ajaxurl = 'api/liberar_atividade.php';
    var data =  {'id': id};
    $.post(ajaxurl, data, function (response) {
    	// TODO: mostrar erro se response retornar false
    	location.reload();
    });
}

function adicionar() {
  var universidade = $('#universidade').val();
  var turma = $('#turma').val();
  var ajaxurl = 'api/cadastrar_turma.php';
  var data =  {'universidade': universidade, 'turma': turma};

  $.post(ajaxurl, data, function (response) {
    // TODO: mostrar erro se response retornar false
    location.reload();
  });
}
