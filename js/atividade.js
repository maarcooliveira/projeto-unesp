var width, height, g, renderer, layouter, src, dest, edge, canSelectDest = false, edgeCount = 0, row_width;
var complete_graph, incomplete_graph;
var mapa = {};
var gabarito = {};
var editado = false;

if (continuacao) {
  mapa = _mapa;
  gabarito = _gabarito;
}

$(document).ready(function() {
    $("#tb_remover").css('display', 'none');
    $("#tb_salvar").css('display', 'none');
    $("#tb_cancelar").css('display', 'none');
    $("#tb_peso").css('display', 'none');

    $("#descricao").text(mapa['descricao']);
    $("#peso_i").val(mapa['peso_i']);
    $("#peso_f").val(mapa['peso_f']);

    row_width = $('#full-hr').width();

    if (continuacao) {
        $("#form-p1").css('display', 'none');
        mostrarMapa();
    }
    else {
      $("#form-p1").css('display', 'inline');
      $("#termos").tokenfield();
    }

    $("#confirma_peso").on('click', function() {
        addEdge($("#tb_peso").val());
        $('#myModal').foundation('reveal', 'close');
    });
    edgeCount = 0;
});

function editar() {
  $("#form-p1").css('display', 'inline');
  $("#btn-cancelar").css('visibility', 'hidden');
  $("#canvas").css('display', 'none');
  $("#termos").tokenfield();
  for (t in mapa['nodes']) {
    $('#termos').tokenfield('createToken', mapa['nodes'][t]);
  }
  $('#termos').tokenfield('disable');
  editado = true;
}

function continuar() {
  if (editado) {
    console.log("Verificando alterações");

    $("#form-p1").css('display', 'none');
    $("#canvas").css('display', 'inline');

    var nodes_tk = $('#termos').tokenfield('getTokens');
    var nodes = Array();

    for (i = 0; i < nodes_tk.length; i++) {
      nodes.push(nodes_tk[i].label);
    }

    old_nodes = Object.keys(g['nodes']);

    for (i = 0; i < old_nodes.length; i++) {
      if (nodes.indexOf(old_nodes[i]) === -1) {
        //call remove node function
      }
    }

    for (i = 0; i < nodes.length; i++) {
      if (old_nodes.indexOf(nodes[i]) === -1) {
        //call add node function
      }
    }

  }
  else {
    mostrarMapa();
  }
}

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

    if (continuacao) {
        for (i = 0; i < mapa['nodes'].length; i++) {
            var newNode = g.addNode(mapa['nodes'][i], {render:render});
            newNode.layoutPosX = mapa['grid'][i]['x'];
            newNode.layoutPosY = mapa['grid'][i]['y'];
        }

        for (var i = 0; i < gabarito['edges'].length; i++) {
            // var newEdge = g.addEdge(gabarito['edges'][i]['source'], gabarito['edges'][i]['target'], {label: gabarito['edges'][i]['weight'], stroke : EDGE_C, "font-size": "16px"});
            //TODO mostrar peso quando for diferente
            var newEdge = g.addEdge(gabarito['edges'][i]['source'], gabarito['edges'][i]['target'], {label: gabarito['edges'][i]['weight'], stroke : EDGE_C, "font-size": "0px"});
        }

    }
    else {
      var nodes = $('#termos').tokenfield('getTokens');

      for (i = 0; i < nodes.length; i++) {
        g.addNode(nodes[i].label, {render:render});
      }
    }

    if (continuacao) {
      layouter = new Graph.Layout.Ordered(g, true, null);
    }
    else {
      layouter = new Graph.Layout.Ordered(g, false, topological_sort(g));
    }


  //NEXTEX: comentar linhas abaixo para forçar novo layout em mapa;
  //  if (continuacao) {
  //     layouter = new Graph.Layout.Grid(g, true);
  //  }
  //  else {
  //     layouter = new Graph.Layout.Grid(g, false);
  //  }



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

    $('#canvas text').css('pointer-events', 'none');

    // $("html, body").animate({ scrollTop: $(document).height() }, 1000);
}

function showInput() {
    // $('#myModal').foundation('reveal','open');
    //TODO: temp apenas para aplicação UNESP
    addEdge(1);
}

function removeSelectColor(obj) {
  $(obj).attr('class', '');
  $(obj).attr('fill', NODE_C);
  text = $(obj).parent().next();
}

function addSelectColor(obj) {
  $(obj).attr('class', 'selected');
  $(obj).attr('fill', NODE_SEL_C);
  text = $(obj).parent().next();
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
	layouter = new Graph.Layout.Ordered(g, false, topological_sort(g));
  renderer.draw();
}

function addNode(name) {
	var newNode = g.addNode(name, {render:render});
  console.log(newNode);
  newNode.layoutPosX = 0.5;
  newNode.layoutPosY = 0.5;
}

function hasEdge(from, to) {
  for (var i = 0; i < g['edges'].length; i++) {
    var src_n = g['edges'][i]['source']['id'];
    var tgt_n = g['edges'][i]['target']['id'];
    if ((src_n === from && tgt_n === to) || (src_n === to && tgt_n === from)) {
      return true;
    }
  }
  return false;
}

function addEdge(weight) {
	var from = $(src).parent().attr('title');
  var to = $(dest).parent().attr('title');

  if (hasEdge(from, to)) {
    // Notificação de ligação já existente
    var n = noty({
      text: '<i class="fa fa-repeat"></i> \"<strong>' + from + '</strong>\" e \"<strong>' + to + '</strong>\" já estavam relacionados',
      layout: 'topCenter',
      type: 'warning',
      theme: 'relax',
      timeout: 3000
    });

    cancelSelect();
    return;
  }


	// var newEdge = g.addEdge(from, to, {label: weight, stroke : EDGE_C, "font-size": "16px"});
   //TODO: temp; deve mostrar o peso das ligações quando o peso não for 1 por padrão
   var newEdge = g.addEdge(from, to, {label: weight, stroke : EDGE_C, "font-size": "0px"});

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
      type: 'error',
      theme: 'relax',
      timeout: 3000
    });
}

function salvar() {
    var mapa_nodes = Object.keys(g['nodes']);

    var titulo = $("#titulo").val();
    var descricao = $("#descricao").val();
    var peso_i = $("#peso_i").val();
    var peso_f = $("#peso_f").val();

    mapa['titulo'] = titulo;
    mapa['descricao'] = descricao;
    mapa['peso_i'] = peso_i;
    mapa['peso_f'] = peso_f;

    gabarito['titulo'] = titulo;
    gabarito['descricao'] = descricao;
    gabarito['peso_i'] = peso_i;
    gabarito['peso_f'] = peso_f;

    var mapa_edges = Array();
    for (var i = 0; i < g['edges'].length; i++) {
        var src_n = g['edges'][i]['source']['id'];
        var tgt_n = g['edges'][i]['target']['id'];
        var wgt_n = g['edges'][i]['style']['label'];
        var edge_n = {'source': src_n, 'target': tgt_n, 'weight': wgt_n};
        mapa_edges.push(edge_n);
    }

    var node_positions = Array();
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
    $("#id_atividade").val(_id);
    $("#formAddMapa").submit();
}

function removeNode() {
    var name = $(src).parent().attr('title')
    g.removeNode(name);
    layouter = new Graph.Layout.Ordered(g, topological_sort(g));
    renderer.draw();
}

function removeNodeByName(name) {
    g.removeNode(name);
    // console.log(g);
    // layouter = new Graph.Layout.Ordered(g, true, null);
    // console.log(layouter);
    // renderer.draw();
    // console.log(renderer);
    console.log("vai mostrar mapa");
    mostrarMapa();
}

function colorEdges() {
	for(e in g.edges) {
		g.edges[e].style.stroke = "#aaa";
        g.edges[e].style.fill = "#00BBEF";
    }
    renderer.draw();
}
