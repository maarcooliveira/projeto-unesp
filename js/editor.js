var width, height, g, renderer, layouter, render, src, dest, edge, canSelectDest = false, edgeCount = 0, row_width;
var complete_graph, incomplete_graph;
var mapa = {};
var gabarito = {};

// PROVISORIO P/ ENVIAR LISTA DE NOS E EDGES A SEREM SALVAS;
var mapa_nodes;
var mapa_edges = Array();
var node_positions = Array();
if (continuacao) {
    var mapa = mapa_txt;
    var gabarito = gabarito_txt;
}


$(document).ready(function() {
    $("#tb_remover").css('display', 'none');
    $("#tb_salvar").css('display', 'none');
    $("#tb_cancelar").css('display', 'none');
    $("#tb_peso").css('display', 'none');

    row_width = $('#full-hr').width();

    if (continuacao) {
        $("#form-p1").css('display', 'none');
        mostrarMapa();
    }

    $("#confirma_peso").on('click', function() {
        addEdge($("#tb_peso").val());
        $('#myModal').foundation('reveal', 'close');
    });
    edgeCount = 0;
});


function mostrarMapa() {

    navbar_heigth = $('#navbar').height();
    // row_width = $('#full-hr').width();
    $("#form-p1").css('display', 'none');
    $("#canvas").html("");
    $("#tb_remover").css('display', 'none');
    $("#tb_salvar").css('display', 'block');
    $("#tb_cancelar").css('display', 'none');
    $("#tb_peso").css('display', 'none');

    width = row_width; //$(window).width();
    height = $(window).height() - navbar_heigth;
    g = new Graph();

    /* modify the edge creation to attach random weights */
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

    if (continuacao) {
        for (i = 0; i < mapa['nodes'].length; i++) {
            var newNode = g.addNode(mapa['nodes'][i], {render:render});
            newNode.layoutPosX = mapa['grid'][i]['x'];
            newNode.layoutPosY = mapa['grid'][i]['y'];
        }

        for (var i = 0; i < gabarito['edges'].length; i++) {
            // var newEdge = g.addEdge(gabarito['edges'][i]['source'], gabarito['edges'][i]['target'], {label: gabarito['edges'][i]['weight'], stroke : "#C7C7C7", "font-size": "16px"});
            //TODO mostrar peso quando for diferente
            var newEdge = g.addEdge(gabarito['edges'][i]['source'], gabarito['edges'][i]['target'], {label: gabarito['edges'][i]['weight'], stroke : "#C7C7C7", "font-size": "0px"});
        }

    }
    else {
        var nodes = $("#termos").val().split(/\n/);

        for (i = 0; i < nodes.length; i++) {
            if(nodes[i].length > 0)
               g.addNode(nodes[i], {render:render});
        }
        console.log(nodes);
    }

  // Algoritmo original para escolher posição dos nós
  //  layouter = new Graph.Layout.Ordered(g, topological_sort(g));

  //NEXTEX: comentar linhas abaixo para forçar novo layout em mapa;
   if (continuacao) {
      layouter = new Graph.Layout.Grid(g, true);
   }
   else {
      layouter = new Graph.Layout.Grid(g, false);
   }

   renderer = new Graph.Renderer.Raphael('canvas', g, width, height);
	renderer.draw();

    if (continuacao) {
        for (var i = 0; i < g.edges.length; i++) {
            var newEdge = g.edges[i];
            (function (_nE) {
                $(_nE.connection.fg[0]).on('click', function() {
                    removeEdge(_nE);
                });
            })(newEdge);
        }
    }

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

    if (!continuacao) {
        var titulo = $("#titulo").val();
        var descricao = $("#descricao").val();
        var peso_i = $("#peso_i").val();
        var peso_f = $("#peso_f").val();

      //   mapa['mapa'] = g;
        mapa['titulo'] = titulo;
        mapa['descricao'] = descricao;
        mapa['peso_i'] = peso_i;
        mapa['peso_f'] = peso_f;

        gabarito['titulo'] = titulo;
        gabarito['descricao'] = descricao;
        gabarito['peso_i'] = peso_i;
        gabarito['peso_f'] = peso_f;
    }

    $("html, body").animate({ scrollTop: $(document).height() }, 1000);
}

function showInput() {
    // $('#myModal').foundation('reveal','open');
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

function ocultarMapa() {
    $("#canvas").css('display', 'none');
}

function toggleItemSelected() {
    if (src && dest) {
        if(typeof Android != 'undefined')
            Android.changeMenuContext('bothSelected');
        else {

        }
    }
    else if (src) {
        if(typeof Android != 'undefined')
            Android.changeMenuContext('srcSelected');
        else {
            $("#tb_remover").css('display', 'none');
            $("#tb_salvar").css('display', 'none');
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

function sort() {
	layouter = new Graph.Layout.Ordered(g, topological_sort(g));
    renderer.draw();
}

function addNode(name) {
	g.addNode(name, {render:render});
	sort();
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
    mapa_nodes = Object.keys(g['nodes']);

    console.log("NODES::");
    console.log(mapa_nodes);

    for (var i = 0; i < g['edges'].length; i++) {
        var src_n = g['edges'][i]['source']['id'];
        var tgt_n = g['edges'][i]['target']['id'];
        var wgt_n = g['edges'][i]['style']['label'];
        var edge_n = {'source': src_n, 'target': tgt_n, 'weight': wgt_n};
        mapa_edges.push(edge_n);
    }

    for (var n = 0; n < mapa_nodes.length; n++) {
      var pos = {};
      pos.x = g.nodes[mapa_nodes[n]].layoutPosX;
      pos.y = g.nodes[mapa_nodes[n]].layoutPosY;
      node_positions.push(pos);
    }

    mapa['nodes'] = mapa_nodes;
    mapa['grid'] = node_positions;
    gabarito['nodes'] = mapa_nodes;
    gabarito['edges'] = mapa_edges;
   //  gabarito['mapa'] = g;
    gabarito['grid'] = node_positions;
    var desc_mapa = mapa; //JSON.decycle(mapa);
    $("#dados_mapa").val(JSON.stringify(desc_mapa));
    var desc_gabarito = gabarito; //JSON.decycle(gabarito);
    $("#dados_gabarito").val(JSON.stringify(desc_gabarito));
    $("#continuacao").val(continuacao);
    $("#id_atividade").val(id_atividade);
    $("#formAddMapa").submit();
}

function removeNode() {
    var name = $(src).parent().attr('title')
    g.removeNode(name);
    layouter = new Graph.Layout.Ordered(g, topological_sort(g));
    renderer.draw();
}

function colorEdges() {
	for(e in g.edges) {
		g.edges[e].style.stroke = "#aaa";
        g.edges[e].style.fill = "#00BBEF";
    }
    renderer.draw();
}
