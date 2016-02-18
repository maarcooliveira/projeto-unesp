function removerAtividade(atividade) {
  var ajaxurl = 'api/remover_atividade.php';
  var data =  {'atividade': atividade};
  var str = "Esta atividade e todas as resoluções enviadas serão excluídas permanentemente, assim como seus resultados. Deseja mesmo excluir?"
  var res = window.confirm(str);
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
  var str = "Esta turma só será excluída se não houverem mais atividades associadas à ela. Deseja realmente excluir?"
  var res = window.confirm(str);
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
