var ua = navigator.userAgent.toLowerCase();
var isAndroid = ua.indexOf("android") > -1; //&& ua.indexOf("mobile");
if(isAndroid) {
    initAndroid();
}

function remove(type, id) {

    var ajaxurl = 'api/excluir_registro.php';
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
