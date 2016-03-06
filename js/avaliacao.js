/* NextEx - Ferramenta de Avaliação
 * js/avaliacao.js
 *
 * Funções de gerenciamento dos gráficos e da interface da página de avaliação (aluno)
*/

var width, height, g, renderer, layouter,
  src, dest, edge, matriz, canSelectDest = false, edgeCount = 0, concluido, str;

$(document).ready(function() {
  $("#confirma_peso").on('click', function() {
    addEdge($("#tb_peso").val());
    $('#myModal').foundation('reveal', 'close');
  });

  checkUserAgent();
  getInterfaceStr();
  inicializar();
});

// Confere se atividade está sendo acessada do app Android
function checkUserAgent() {
  var ua = navigator.userAgent.toLowerCase();
  var isAndroid = ua.indexOf("android") > -1;
  if(isAndroid) {
    initAndroid();
  }
}

// Inicializa visualização e controles da atividade
function inicializar() {

  width = $('#full-hr').width();
  height = $(window).height() - $('#navbar').height();
  $("#canvas").html("");
  $('#full-hr').css('display', 'none');
  $("#tb_cancelar").css('display', 'none');

  if (gabarito !== undefined) {
    $("#tb_salvar").css('display', 'none');
    $("#tb_enviar").css('display', 'none');
    $('#canvas').css('pointer-events', 'none'); // ignora clicks na atividade
    // TODO: descomentar linha abaixo para mostrar tabela e gráfico de gabarito
    // mostrarTabela();
  }
  else {
    addControles();
  }

  g = new Graph();

  g.edgeFactory.build = function(source, target) {
    var e = jQuery.extend(true, {}, this.template);
    e.source = source;
    e.target = target;
    return e;
  }

  mostrarMapa();
}

// Monta a atividade e insere ligações salvas previamente
function mostrarMapa() {

  for (i = 0; i < mapa['nodes'].length; i++) {
    var newNode = g.addNode(mapa['nodes'][i], {render:render});
    newNode.layoutPosX = mapa['grid'][i]['x'];
    newNode.layoutPosY = mapa['grid'][i]['y'];
  }

  if (resolucao !== undefined) {
    for (var i = 0; i < resolucao['edges'].length; i++) {
      // TODO: alterar font-size quando o peso não for padrão (1)
      g.addEdge(resolucao['edges'][i]['source'], resolucao['edges'][i]['target'], {label: resolucao['edges'][i]['weight'], stroke : EDGE_C, "font-size": "0px"});
    }
  }

  layouter = new Graph.Layout.Ordered(g, true, null);
  renderer = new Graph.Renderer.Raphael('canvas', g, width, height, true);
  renderer.draw();

  // Ignora click no texto do termo; o evento é então passado para o listener da caixa do termo
  $('#canvas text').css('pointer-events', 'none');

  // Click listener nas ligações para remover
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

  // Exibir a tela de ajuda sempre que o aluno abre uma avaliação que ele ainda não alterou
  if (g.edges.length === 0) {
    $('#modalDesc').foundation('reveal','open');
  }
}

// Mostra gabarito gráfico para o aluno
function mostrarGabarito() {
  var gab = new Graph();

  for (i = 0; i < gabarito['nodes'].length; i++) {
    var newNode = gab.addNode(gabarito['nodes'][i], {render:render});
    newNode.layoutPosX = gabarito['grid'][i]['x'];
    newNode.layoutPosY = gabarito['grid'][i]['y'];
  }

  if (resolucao !== undefined) {
    for (var i = 0; i < gabarito['edges'].length; i++) {
      // TODO: alterar font-size quando o peso não for padrão (1)
      gab.addEdge(gabarito['edges'][i]['source'], gabarito['edges'][i]['target'], {label: gabarito['edges'][i]['weight'], stroke : EDGE_C, "font-size": "0px"});
    }
  }

  layouter = new Graph.Layout.Ordered(gab, true, null);
  renderer = new Graph.Renderer.Raphael('gabarito', gab, width, height);
  renderer.draw();
}

// Adiciona controles iniciais na navbar
function addControles() {
  $("#tb_salvar").css('display', 'block');
  $("#tb_enviar").css('display', 'block');

  // Adiciona listener de seleção de termos
  addBoxClickListener();
}

function salvar() {
  concluido = false;
  enviar_form_salvar();
}

function enviar() {
  concluido = true;
  enviar_form_salvar();
}

// Salva os dados da atividade, marcando-a como concluída ou não
function enviar_form_salvar() {
  var mapa_edges = Array();
  for (var i = 0; i < g['edges'].length; i++) {
    var src_n = g['edges'][i]['source']['id'];
    var tgt_n = g['edges'][i]['target']['id'];
    var wgt_n = g['edges'][i]['style']['label'];
    var edge_n = {'source': src_n, 'target': tgt_n, 'weight': wgt_n};
    mapa_edges.push(edge_n);
  }

  mapa['edges'] = mapa_edges;
  mapa['aluno'] = id_aluno;

  $.ajax({
    url: "api/salvar_resolucao.php",
    type:'POST',
    data:
    {
      dados_mapa: JSON.stringify(mapa),
      id_atividade: id_atividade,
      id_aluno: id_aluno,
      concluido: concluido
    },
    success: function(msg)
    {
      if (msg === "enviado") {
        window.location.href = "aluno.php";
      }
      var n = noty({
        text: '<i class="fa fa-floppy-o pad-r"></i> ' + str.atividade_salva,
        layout: 'topLeft',
        type: 'success',
        theme: 'relax',
        timeout: 3000
     });
    }
  });
}

// Mostra tabela de distância do aluno
function mostrarTabela() {
  // valores utilizados para calculo da nota
  var max = gabarito['peso_f'];
  var min = gabarito['peso_i'];
  var n = Math.pow(max - min, 2);
  var np = gabarito['nodes'].length;
  var npr = Math.pow(np, 2) - np; //posicoes relevantes
  var distancia_cel = 0;

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
