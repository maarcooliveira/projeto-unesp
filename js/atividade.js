var width, height, g, renderer, layouter, src, dest, edge, canSelectDest = false, edgeCount = 0, row_width;
var complete_graph, incomplete_graph;
var mapa = {};
var gabarito = {};
var editado = false;
var str;

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
  getInterfaceStr();
});

function editar() {
  $("#form-p1").css('display', 'inline');
  $("#btn-cancelar").css('visibility', 'hidden');
  $("#canvas").css('display', 'none');
  $("#termos").tokenfield("destroy");
  $("#termos").val("");
  $("#termos").tokenfield();
  for (t in mapa['nodes']) {
    $('#termos').tokenfield('createToken', mapa['nodes'][t]);
  }
  $('#termos').tokenfield('disable');
  editado = true;
}

function continuar() {
  if (editado) {
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
        // TODO: remover nós antigos de g['nodes'] e todas as suas ligações
      }
    }

    for (i = 0; i < nodes.length; i++) {
      if (old_nodes.indexOf(nodes[i]) === -1) {
        // TODO: inserir os nós novos em g['nodes']
      }
    }
  }
  else {
    mostrarMapa();
  }
}

function mostrarMapa() {
  $("#form-p1").css('display', 'none');
  $("#canvas").html("");
  $("#tb_remover").css('display', 'none');
  $("#tb_salvar").css('display', 'block');
  $("#tb_cancelar").css('display', 'none');
  $("#tb_peso").css('display', 'none');

  width = row_width;
  height = $(window).height() - $('#navbar').height();
  g = new Graph();

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
      // TODO: alterar font-size quando o peso não for padrão (1)
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

  // Adiciona listener de seleção de termos
  addBoxClickListener();
  // Ignora click no texto do termo; o evento é então passado para o listener da caixa do termo
  $('#canvas text').css('pointer-events', 'none');
}

function salvar() {
  var mapa_nodes = Object.keys(g['nodes']);
  var titulo = $("#titulo").val();
  var descricao = $("#descricao").val();
  var peso_i = $("#peso_i").val();
  var peso_f = $("#peso_f").val();
  var id_turma = $('#id_turma').val();
  var data_entrega = $('#data_entrega').val();

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
  gabarito['grid'] = node_positions;

  $.ajax({
    url: "api/salvar_atividade.php",
    type:'POST',
    data:
    {
      dados_mapa: JSON.stringify(mapa),
      dados_gabarito: JSON.stringify(gabarito),
      id_turma: id_turma,
      data_entrega: data_entrega,
      titulo: titulo,
      continuacao: continuacao,
      id_atividade: _id,
    },
    success: function(msg)
    {
      console.log(msg);
      if (msg === "salvo") {
        var n = noty({
          text: '<i class="fa fa-floppy-o pad-r"></i> ' + str.atividade_salva,
          layout: 'topLeft',
          type: 'success',
          theme: 'relax',
          timeout: 3000
        });
      }
      else if (msg === "erro") {
        var n = noty({
          text: '<i class="fa fa-floppy-o pad-r"></i> ' + str.erro_salvar_atividade,
          layout: 'topLeft',
          type: 'error',
          theme: 'relax',
          timeout: 3000
        });
      }
      continuacao = 1;
    }
  });
}
