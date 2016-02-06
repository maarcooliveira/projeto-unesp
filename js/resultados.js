var width, height, g, renderer, layouter, render, edgeFactory;
var matriz_turma;
var distancia;
var resolucoes = [];

for (var r = 0; r < resolucoes_txt.length; r++) {
  resolucoes.push(JSON.parse(resolucoes_txt[r]));
}

$( document ).ready(function() {
  $('.psas').css('color', PSAS_C);
  $('.psan').css('color', PSAN_C);
  $('.pnas').css('color', PNAS_C);

  toolbar_height = $('#toolbar').height();
  navbar_heigth = $('#navbar').height();
  row_width = $('#full-hr').width();
  $('#full-hr').css("margin", "0");

  width = row_width;
  height = $(window).height() - toolbar_height - navbar_heigth;

  edgeFactory = function(source, target) {
    var e = jQuery.extend(true, {}, this.template);
    e.source = source;
    e.target = target;
    return e;
  }

  g = new Graph();
  g.edgeFactory.build = edgeFactory;

  for (i = 0; i < gabarito['nodes'].length; i++) {
    var newNode = g.addNode(gabarito['nodes'][i], {render:render});
    newNode.layoutPosX = gabarito['grid'][i]['x'];
    newNode.layoutPosY = gabarito['grid'][i]['y'];
  }

  for (var i = 0; i < gabarito['edges'].length; i++) {
    // g.addEdge(gabarito['edges'][i]['source'], gabarito['edges'][i]['target'], {label: gabarito['edges'][i]['weight'], stroke : EDGE_C, "font-size": "16px"});
    //TODO mostrar peso quando !== 1
    g.addEdge(gabarito['edges'][i]['source'], gabarito['edges'][i]['target'], {label: gabarito['edges'][i]['weight'], stroke : EDGE_C, "font-size": "0px"});
  }

  layouter = new Graph.Layout.Ordered(g, true, null);
  // layouter = new Graph.Layout.Grid(g, true);
  renderer = new Graph.Renderer.Raphael('gabarito', g, width, height);
  renderer.draw();
  calcularMatrizes();
});

function calcularMatrizes() {
  // valores utilizados para calculo da nota
  var max = gabarito['peso_f'];
  var min = gabarito['peso_i'];
  var n = Math.pow(max - min, 2);
  var np = gabarito['nodes'].length; // numero de palavras
  var npr = Math.pow(np, 2) - np; //posicoes relevantes
  var qtd_respostas = resolucoes.length;
  var distancia_total = 0;
  var distancia_cel;
  var matriz;
  distancia = [];
  matriz_turma = [];

  $("#dm_aluno").css("display", "none");

  // repete para cada uma das resolucoes
  for (var r = 0; r < resolucoes.length; r++) {
    distancia_cel = 0;

    var resolucao = resolucoes[r];

    matriz_turma[r] = [];
    matriz = matriz_turma[r];

    // inicializa matriz com valores das ligacoes do aluno
    for (var i = 0; i < np; i++) {
      matriz[i] = [];
      for (var j = 0; j < np; j++) {
        matriz[i][j] = 0;

        for (var nc = 0; nc < resolucao['edges'].length; nc++) {
          resolucao['edges'][nc]['color'] = PNAS_C; // assumimos primeiro que aluno ligou e prof. nao;
          if (gabarito['nodes'][i] === resolucao['edges'][nc]['source'] && gabarito['nodes'][j] === resolucao['edges'][nc]['target']) {
            matriz[i][j] = resolucao['edges'][nc]['weight'];
            break;
          }
          else if (gabarito['nodes'][i] === resolucao['edges'][nc]['target'] && gabarito['nodes'][j] === resolucao['edges'][nc]['source']) {
            matriz[i][j] = resolucao['edges'][nc]['weight'];
            break;
          }
        }

        // completa matriz subtraindo com valores das ligacoes do professor
        for (var nc = 0; nc < gabarito['edges'].length; nc++) {
          if (gabarito['nodes'][i] === gabarito['edges'][nc]['source'] && gabarito['nodes'][j] === gabarito['edges'][nc]['target']) {
            matriz[i][j] -= gabarito['edges'][nc]['weight'];
            break;
          }
          else if (gabarito['nodes'][i] === gabarito['edges'][nc]['target'] && gabarito['nodes'][j] === gabarito['edges'][nc]['source']) {
            matriz[i][j] -= gabarito['edges'][nc]['weight'];
            break;
          }
        }
        distancia_cel += Math.sqrt(Math.pow(matriz[i][j], 2));
      }
    }

    distancia[r] = distancia_cel/npr;
    distancia_total += distancia[r];
  }
  $("#dmt").text((distancia_total/qtd_respostas).toFixed(3));
}

$("#aluno_resultado").change(function() {
  var id = $("#aluno_resultado").val();
  var pos;

  $("#tabela").html("");

  if (id === "-1") {
    //TODO: matriz consolidada da sala;
    $("#dm_aluno").css("display", "none");
    $("#compara_aluno").css("display", "none");
  }
  else {
    for (var m = 0; m < resolucoes.length; m++) {
      var resolucao = resolucoes[m];
      if (resolucao['aluno'] == id) {
        pos = m;
        break;
      }
    }

    $("#dm_aluno").css("display", "inline");
    $("#dma").text(distancia[pos].toFixed(3));

    var matriz = matriz_turma[pos];
    var np = gabarito['nodes'].length;

    // cria e preenche elemento table no html
    var result = "<br><table class='responsive'>";
    result += "<tr>"
    result += "<td class='text-center'></td>";
    for (var i = 0; i < np; i++) {
      result += "<td class='text-center'>" + gabarito['nodes'][i] + "</td>";
    }
    result += "</tr>"

    for (var i = 0; i < np; i++) {
      result += "<tr><td class='text-center'>" + gabarito['nodes'][i] + "</td>";
      for (var j = 0; j < np; j++) {
        if (i !== j) {
          result += "<td class='text-center'>" + matriz[i][j] + "</td>";
        }
        else {
          result += "<td class='text-center'></td>";
        }
      }
      result += "</tr>"
    }

    result += "</table>";
    $("#tabela").append(result);

    $("#compara_aluno").fadeOut("slow", function() {
      mostrarMapa(pos);
      switched = false;
      updateTables(); // Calling responsive-tables.js function
    });
  }
});

function mostrarMapa(pos) {
  $("#compara_aluno").css("display", "inline");
  $("#compara_aluno").css("visibility", "hidden");
  $("#compara_aluno").html("");
  $("#legenda").css("display", "inline");

  var resolucao = resolucoes[pos];

  r = new Graph();
  r.edgeFactory.build = edgeFactory;

  // Usa gabarito para pegar todos os nós
  for (i = 0; i < gabarito['nodes'].length; i++) {
      var newNode = r.addNode(gabarito['nodes'][i], {render:render});
      newNode.layoutPosX = gabarito['grid'][i]['x'];
      newNode.layoutPosY = gabarito['grid'][i]['y'];
  }

  for (var nc = 0; nc < gabarito['edges'].length; nc++) {
    var both = false;
    for (var nc2 = 0; nc2 < resolucao['edges'].length; nc2++) {
      if (gabarito['edges'][nc]['source'] === resolucao['edges'][nc2]['source'] && gabarito['edges'][nc]['target'] === resolucao['edges'][nc2]['target']) {
        gabarito['edges'][nc]['color'] = PSAS_C; // os dois ligaram
        resolucao['edges'][nc2]['color'] = PSAS_C; // os dois ligaram
        both = true;
        break;
      }
      if (gabarito['edges'][nc]['source'] === resolucao['edges'][nc2]['target'] && gabarito['edges'][nc]['target'] === resolucao['edges'][nc2]['source']) {
        gabarito['edges'][nc]['color'] = PSAS_C; // os dois ligaram
        resolucao['edges'][nc2]['color'] = PSAS_C; // os dois ligaram
        both = true;
        break;
      }
    }
    if (!both) {
      gabarito['edges'][nc]['color'] = PSAN_C; // so um ligou
    }
  }
  var nPSAS = 0;
  var nPSAN = 0;
  var nPNAS = 0;

  for (var i = 0; i < gabarito['edges'].length; i++) {
    // g.addEdge(gabarito['edges'][i]['source'], gabarito['edges'][i]['target'], {label: gabarito['edges'][i]['weight'], stroke : EDGE_C, "font-size": "16px"});
    //TODO mostrar peso quando !== 1
    r.addEdge(gabarito['edges'][i]['source'], gabarito['edges'][i]['target'], {label: gabarito['edges'][i]['weight'], stroke: gabarito['edges'][i]['color'], "font-size": "0px"});
    if (gabarito['edges'][i]['color'] === PSAS_C) {
      nPSAS++;
    }
    else {
      nPSAN++;
    }
  }

  for (var i = 0; i < resolucao['edges'].length; i++) {
    // g.addEdge(gabarito['edges'][i]['source'], gabarito['edges'][i]['target'], {label: gabarito['edges'][i]['weight'], stroke : EDGE_C, "font-size": "16px"});
    //TODO mostrar peso quando !== 1
    if (resolucao['edges'][i]['color'] !== PSAS_C) { //PSAS já foi adicionado no for anterior!
      r.addEdge(resolucao['edges'][i]['source'], resolucao['edges'][i]['target'], {label: resolucao['edges'][i]['weight'], stroke: resolucao['edges'][i]['color'], "font-size": "0px"});
      nPNAS++;
    }
  }

  var nTOTAL = nPSAS + nPSAN + nPNAS;
  console.log("PSAS: " + nPSAS);
  console.log("PSAN: " + nPSAN);
  console.log("PNAS: " + nPNAS);
  console.log("TOTAL: " + nTOTAL);

  layouter = new Graph.Layout.Ordered(r, true, null);
  renderer = new Graph.Renderer.Raphael('compara_aluno', r, width, height);
  renderer.draw();

  $("#compara_aluno").css("display", "none");
  $("#compara_aluno").css("visibility", "visible");
  $("#compara_aluno").fadeIn("fast", function() {
    $('html, body').animate({
      scrollTop: $("#toolbar").offset().top
    }, 500);
  });
}
