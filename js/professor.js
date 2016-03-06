/* NextEx - Ferramenta de Avaliação
 * js/professor.js
 *
 * Funções utilizadas na interação com o painel do professor
*/

var str;

$(document).ready(function() {
  getInterfaceStr();
});

// Carrega strings de acordo com o idioma do usuário para exibir nas janelas de
// confirmação de exclusão de dados
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

// Chama função na API que exclui uma atividade e todos os dados relacionados
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

// Chama função na API que excluir uma turma
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

// Chama função na API que libera uma atividade para os alunos
function liberar(id) {
    var ajaxurl = 'api/liberar_atividade.php';
    var data =  {'id': id};
    $.post(ajaxurl, data, function (response) {
    	// TODO: mostrar erro se response retornar false
    	location.reload();
    });
}

// Chama função na API que cadastra uma nova turma
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
