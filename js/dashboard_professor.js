function remove(type, id) {

    var ajaxurl = 'api/excluir_registro.php';
    var data =  {'tabela': type, 'id': id};
    $.post(ajaxurl, data, function (response) {
    	console.log(response);
    	location.reload();
    });
}

function liberar(id) {

    var ajaxurl = 'api/liberar_atividade.php';
    var data =  {'id': id};
    $.post(ajaxurl, data, function (response) {
    	console.log(response);
    	location.reload();
    });
}
