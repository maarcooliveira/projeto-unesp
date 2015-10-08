var ua = navigator.userAgent.toLowerCase();
var isAndroid = ua.indexOf("android") > -1; //&& ua.indexOf("mobile");
if(isAndroid) {
    initAndroid();
}

esconderAddTurma();

function esconderAddTurma() {
	$('#formAddTurma').css('display', 'none');
    $('#botaoAddTurma').css('display', 'inline');
}

function mostrarAddTurma() {
	$('#formAddTurma').css('display', 'inline');
    $('#botaoAddTurma').css('display', 'none');
}

function remove(type, id) {

    var ajaxurl = 'excluir_registro.php';
    var data =  {'tabela': type, 'id': id};
    $.post(ajaxurl, data, function (response) {
    	console.log(response);
    	location.reload();
    });
}

// function liberar(id) {

//     var ajaxurl = 'liberar_atividade.php';
//     var data =  {'id': id};
//     $.post(ajaxurl, data, function (response) {
//     	console.log(response);
//     	location.reload();
//     });
// }

function initAndroid() {
    $('nav').css("display", "none");
    Android.changeMenuContext('removeAll');
}
