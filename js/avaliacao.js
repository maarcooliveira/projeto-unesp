var width, height, g, renderer, layouter, render, src, dest, edge, canSelectDest = false, edgeCount = 0;
var complete_graph, incomplete_graph, matriz;

// TODO: Separar em modo editor e modo leitura de resultados


// PROVISORIO P/ ENVIAR LISTA DE NOS E EDGES A SEREM SALVAS;
var mapa_nodes;
var mapa_edges = Array();
var mapa = mapa_txt; //JSON.parse(mapa_txt);
var gabarito = gabarito_txt; //JSON.parse(gabarito_txt);
var resolucao = resolucao_txt; //JSON.parse(resolucao_txt);

$(document).ready(function() {
    $("#tb_remover").css('display', 'none');
    if (gabarito !== undefined) {
      $("#tb_salvar").css('display', 'none');
      $("#tb_enviar").css('display', 'none');
    }
    $("#tb_cancelar").css('display', 'none');
    $("#tb_peso").css('display', 'none');

    $("#confirma_peso").on('click', function() {
        addEdge($("#tb_peso").val());
        $('#myModal').foundation('reveal', 'close');
    });
    edgeCount = 0;
});

var ua = navigator.userAgent.toLowerCase();
var isAndroid = ua.indexOf("android") > -1; //&& ua.indexOf("mobile");
if(isAndroid) {
    initAndroid();
}


inicializar();
mostrarMapa();
if (gabarito !== undefined) {
  // mostrarTabela(); //TODO: removido para teste unesp
  $('#canvas').css('pointer-events', 'none'); // ignora clicks
}
else {
    addControles();
}

function inicializar() {
    navbar_heigth = $('#navbar').height();
    row_width = $('#full-hr').width();
    $("#canvas").html("");
    $("#tb_remover").css('display', 'none');
    if (gabarito !== undefined) {
      $("#tb_salvar").css('display', 'block');
      $("#tb_enviar").css('display', 'block');
    }
    $("#tb_cancelar").css('display', 'none');
    $("#tb_peso").css('display', 'none');

    $('#full-hr').css('display', 'none');

    width = row_width; //$(window).width();
    height = $(window).height() - navbar_heigth;
    g = new Graph();

    g.edgeFactory.build = function(source, target) {
        var e = jQuery.extend(true, {}, this.template);
        e.source = source;
        e.target = target;
        return e;
    }

    render = function(r, n) {
        var color = "#07AEFF"; //Raphael.getColor();
        /* the Raphael set is obligatory, containing all you want to display */

        rec = r.rect(n.point[0], n.point[1], 130, 20);
        txt = r.text(n.point[0], n.point[1], (n.label || n.id)).attr({"font-size": "12px"});

        w = txt.getBBox().width + 20;
        h = txt.getBBox().height + 20;
        x = txt.getBBox().x - 10;
        y = txt.getBBox().y - 10;

        attrs = {"title": (n.label || n.id), "fill": color, "stroke": color, r: "1px", "stroke-width": "1px", "width": w, "height": h, "x": x, "y": y};
        rec.attr(attrs);
        var set = r.set().push(rec).push(txt);
        return set;
    };
}

function mostrarMapa() {

    for (i = 0; i < mapa['nodes'].length; i++) {
        var newNode = g.addNode(mapa['nodes'][i], {render:render});
        newNode.layoutPosX = mapa['grid'][i]['x'];
        newNode.layoutPosY = mapa['grid'][i]['y'];
    }

    if (resolucao !== undefined) {
        for (var i = 0; i < resolucao['edges'].length; i++) {
            // g.addEdge(resolucao['edges'][i]['source'], resolucao['edges'][i]['target'], {label: resolucao['edges'][i]['weight'], stroke : "#C7C7C7", "font-size": "16px"});
            //TODO: mostrar edge quando nao for 1
            g.addEdge(resolucao['edges'][i]['source'], resolucao['edges'][i]['target'], {label: resolucao['edges'][i]['weight'], stroke : "#C7C7C7", "font-size": "0px"});
        }
    }

    layouter = new Graph.Layout.Ordered(g, true, null);
    // layouter = new Graph.Layout.Grid(g, true);
    renderer = new Graph.Renderer.Raphael('canvas', g, width, height);
    renderer.draw();

    if (resolucao !== undefined) {
        for (var i = 0; i < g.edges.length; i++) {
            var newEdge = g.edges[i];
            (function (_nE) {
                $(_nE.connection.fg[0]).on('click', function() {
                    removeEdge(_nE);
                });
            })(newEdge);
        }
    }

    $('#modalDescContent').text(mapa['descricao']);

    if (g.edges.length === 0) {
        $('#modalDesc').foundation('reveal','open');
    }
}

function mostrarGabarito() {
    var gab = new Graph();

    for (i = 0; i < gabarito['nodes'].length; i++) {
        gab.addNode(gabarito['nodes'][i], {render:render});
    }

    if (resolucao !== undefined) {
        for (var i = 0; i < gabarito['edges'].length; i++) {
            for (var j = 0; j < gabarito['edges'].length; j++) {
                if (matriz[i][j] !== 0) {
                  //   gab.addEdge(gabarito['edges'][i]['source'], gabarito['edges'][i]['target'], {label: gabarito['edges'][i]['weight'], stroke : "#C7C7C7", "font-size": "16px"});
                  //TODO: mostrar peso
                  gab.addEdge(gabarito['edges'][i]['source'], gabarito['edges'][i]['target'], {label: gabarito['edges'][i]['weight'], stroke : "#C7C7C7", "font-size": "0px"});
                }
            }
        }
    }

    layouter = new Graph.Layout.Ordered(gab, topological_sort(gab));
    renderer = new Graph.Renderer.Raphael('gabarito', gab, width, height);
    renderer.draw();
}

function addControles() {
    $("#tb_salvar").css('display', 'block');
    $("#tb_enviar").css('display', 'block');

    $(document).on('click', '#canvas rect', function () {
        // Se o elemento já estava selecionado, desceleciona;
        if($(this).attr('class') === 'selected') {
            removeSelectColor(src);
            src = undefined;
        }
        // Foi selecionado um elemento não selecionado anteriormente
        else {
            // Se já tem um elemento previamente selecionado...
            if (src) {
                toggleDestSelection(true);
                // Se o usuário ativou a função de link, define 'this' como dest e adiciona ligação;
                if(!dest && canSelectDest) {
                    dest = this;
                    showInput();
                }
                // Se o usuário não ativou a função de link, ele quer substituir a seleção;
                else {
                    removeSelectColor(src);
                    removeSelectColor(dest);
                    src = this;
                    dest = undefined;
                    addSelectColor(src);
                }
            }
            // Não há elementos selecionados, portanto, 'this' se torna src
            else {
                src = this;
                addSelectColor(src);
            }
        }
        toggleItemSelected();
    });

    $('#canvas text').css('pointer-events', 'none');
}

function showInput() {
    // if(typeof Android != 'undefined') {
    //     Android.askEdgeWeight();
    // }
    // else {
    //     $('#myModal').foundation('reveal','open');
    // }
    //TODO: temp apenas para aplicação UNESP
    addEdge(1);
}

function removeSelectColor(obj) {
    $(obj).attr('class', '');
    $(obj).attr('fill', $(src).attr('stroke'));
    text = $(obj).parent().next();
    $(text).attr('fill', '#000000');
}

function addSelectColor(obj) {
    $(obj).attr('class', 'selected');
    $(obj).attr('fill', '#F8B500');
    text = $(obj).parent().next();
    $(text).attr('fill', '#000000');
}

function toggleItemSelected() {
    // if (src && dest) {
    //     if(typeof Android != 'undefined')
    //         Android.changeMenuContext('bothSelected');
    //     else {

    //     }
    // }
    // else
    if (src) {
        if(typeof Android != 'undefined')
            Android.changeMenuContext('srcSelected');
        else {
            $("#tb_remover").css('display', 'none');
            $("#tb_salvar").css('display', 'none');
            $("#tb_enviar").css('display', 'none');
            $("#tb_cancelar").css('display', 'block');
            $("#tb_peso").css('display', 'block');
        }
    }
    else {
        if(typeof Android != 'undefined')
            Android.changeMenuContext('noneSelected');
        else {
            $("#tb_remover").css('display', 'none');
            $("#tb_salvar").css('display', 'block');
            $("#tb_enviar").css('display', 'block');
            $("#tb_cancelar").css('display', 'none');
            $("#tb_peso").css('display', 'none');
        }
    }
}

function toggleDestSelection(val) {
    canSelectDest = val;
}

function cancelSelect() {
    removeSelectColor(src);
    src = undefined;
    dest = undefined;
    edge = undefined;
    toggleDestSelection(false);
    toggleItemSelected();
}

function redraw() {
    renderer.draw();
}

function addEdge(weight) {
   var from = $(src).parent().attr('title');
   var to = $(dest).parent().attr('title');
   // var newEdge = g.addEdge(from, to, {label: weight, stroke : "#C7C7C7", "font-size": "16px"});
   //TODO: temp; deve mostrar o peso das ligações quando o peso não for 1 por padrão
   var newEdge = g.addEdge(from, to, {label: weight, stroke : "#C7C7C7", "font-size": "0px"});

    renderer.draw();

    // Notificação de ligação inserida
    var n = noty({
      text: '<i class="fa fa-check"></i> \"<strong>' + from + '</strong>\" e \"<strong>' + to + '</strong>\" foram relacionados',
      layout: 'topCenter',
      type: 'information',
      theme: 'relax',
      timeout: 3000
    });

    cancelSelect();

    (function (_eC, _nE) {
        $(_nE.connection.fg[0]).data('edgeId', _eC);
        $(_nE.connection.fg[0]).on('click', function() {
            removeEdge(_nE);
        });
    })(edgeCount, newEdge);

    edgeCount++;
}

function removeEdge(edge) {
    var index = g.edges.indexOf(edge);
    edge.remove();
    g.edges.splice(index, 1);

    var from = edge.source.id;
    var to = edge.target.id;
    var n = noty({
      text: '<i class="fa fa-times"></i> \"<strong>' + from + '</strong>\" e \"<strong>' + to + '</strong>\" foram desrelacionados',
      layout: 'topCenter',
      type: 'warning',
      theme: 'relax',
      timeout: 3000
    });
}

function salvar() {
    // window.alert("vai salvar");
    $("#concluido").val("false");
    enviar_form_salvar();
}

function enviar() {
    $("#concluido").val("true");
    enviar_form_salvar();
}

function enviar_form_salvar() {
    for (var i = 0; i < g['edges'].length; i++) {
        var src_n = g['edges'][i]['source']['id'];
        var tgt_n = g['edges'][i]['target']['id'];
        var wgt_n = g['edges'][i]['style']['label'];
        var edge_n = {'source': src_n, 'target': tgt_n, 'weight': wgt_n};
        mapa_edges.push(edge_n);
    }
    console.log("EDGES::");
    console.log(mapa_edges);

    mapa['edges'] = mapa_edges;
   //  mapa['mapa'] = null;
    mapa['aluno'] = id_aluno;

    var desc_mapa = mapa; //JSON.decycle(mapa);
    $("#dados_mapa").val(JSON.stringify(desc_mapa));
    // $("#formSaveMapaAluno").submit();


    $.ajax({
            url: "api/salvar_arquivo_aluno.php",
            type:'POST',
            data:
            {
                dados_mapa: $("#dados_mapa").val(),
                id_atividade: $("#id_atividade").val(),
                id_aluno: $("#id_aluno").val(),
                concluido: $("#concluido").val()
            },
            success: function(msg)
            {
                if (msg === "enviado") {
                    window.location.href = "aluno.php";
                }
                var n = noty({
                  text: '<i class="fa fa-floppy-o"></i> Atividade salva',
                  layout: 'topCenter',
                  type: 'success',
                  theme: 'relax',
                  timeout: 3000
               });
            }
        });

}


function mostrarTabela() {
    // valores utilizados para calculo da nota
    var max = gabarito['peso_f'];
    var min = gabarito['peso_i'];
    var n = Math.pow(max - min, 2);
    var np = gabarito['nodes'].length;
    var npr = Math.pow(np, 2) - np; //posicoes relevantes
    var distancia_cel = 0;

   //  console.log(resolucao);

    matriz = [];

    // inicializa matriz com valores das ligacoes do aluno
    for (var i = 0; i < np; i++) {
        matriz[i] = [];
        for (var j = 0; j < np; j++) {
            matriz[i][j] = 0;
            for (var nc = 0; nc < resolucao['edges'].length; nc++) {
                if (gabarito['nodes'][i] === resolucao['edges'][nc]['source'] && gabarito['nodes'][j] === resolucao['edges'][nc]['target']) {
                    matriz[i][j] = resolucao['edges'][nc]['weight'];
                }
                else if (gabarito['nodes'][i] === resolucao['edges'][nc]['target'] && gabarito['nodes'][j] === resolucao['edges'][nc]['source']) {
                    matriz[i][j] = resolucao['edges'][nc]['weight'];
                }
            }
        }
    }

    // completa matriz subtraindo com valores das ligacoes do professor
    for (var i = 0; i < np; i++) {
        for (var j = 0; j < np; j++) {
            for (var nc = 0; nc < gabarito['edges'].length; nc++) {
                if (gabarito['nodes'][i] === gabarito['edges'][nc]['source'] && gabarito['nodes'][j] === gabarito['edges'][nc]['target']) {
                    matriz[i][j] -= gabarito['edges'][nc]['weight'];
                }
                else if (gabarito['nodes'][i] === gabarito['edges'][nc]['target'] && gabarito['nodes'][j] === gabarito['edges'][nc]['source']) {
                    matriz[i][j] -= gabarito['edges'][nc]['weight'];
                }
                distancia_cel += Math.pow(matriz[i][j], 2);
            }
        }
    }

    var distancia = Math.sqrt(distancia_cel/(n*npr));

    $("#resultados").append("<br><h3>Seus resultados</h3><br>");
    // cria e preenche elemento table no html
    var result = "<table>";
    result += "<tr>"
    result += "<td></td>";
    for (var i = 0; i < np; i++) {
        result += "<td>" + gabarito['nodes'][i] + "</td>";
    }
    result += "</tr>"

    for (var i = 0; i < np; i++) {
        result += "<tr><td>" + gabarito['nodes'][i] + "</td>";

        for (var j = 0; j < gabarito['nodes'].length; j++) {
            if (i === j) {
                result += "<td> </td>";
            }
            else {
                result += "<td>" + matriz[i][j] + "</td>";
            }
        }

        result += "</tr>"
    }

    result += "</table>";
    result += "Distância da resposta do professor: " + distancia + "<br><br>";
    $("#resultados").append(result);

    mostrarGabarito();

}

function initAndroid() {
    $('nav').css("display", "none");
    Android.changeMenuContext('noneSelected');
}

function mostrarDescricao() {
    $('#modalDesc').foundation('reveal','open');
}
