function remover(type, id) {
  console.log(type + " " + id);
    var ajaxurl = 'api/excluir_registro.php';
    var data =  {'tabela': type, 'id': id};
    // var res = window.confirm("Esta atividade e todas as resoluções enviadas serão excluídas, assim como seus resultados. Excluir mesmo assim?");
    var res = window.confirm("Esta " + type + " será permanentemente excluída. Confirmar?");
    if (res) {
      $.post(ajaxurl, data, function (response) {
      	console.log(response);
      	location.reload();
      });
    }
}

function liberar(id) {

    var ajaxurl = 'api/liberar_atividade.php';
    var data =  {'id': id};
    $.post(ajaxurl, data, function (response) {
    	console.log(response);
    	location.reload();
    });
}

function adicionar() {
  var uni = $('#universidade').val();
  var turma = $('#turma').val();
  console.log(uni + " t: " + turma);

  var ajaxurl = 'api/add_turma_professor.php';
  var data =  {'universidade': uni, 'turma': turma};
  $.post(ajaxurl, data, function (response) {
    console.log(response);
    location.reload();
  });
}
