var renderer, layouter, matriz_turma, matriz_geral, distancia, id = -1, resolucoes = [];

for (var r = 0; r < _resolucoes.length; r++) {
  resolucoes.push(JSON.parse(_resolucoes[r]));
}

$(document).ready(function() {

  $(document).on('opened.fndtn.reveal', '#modalGabarito[data-reveal]', function () {
    var modal = $(this);
    mostrarGabarito();
  });

  $(document).on('opened.fndtn.reveal', '#modalResultadoAluno[data-reveal]', function () {
    var modal = $(this);
    mostrarResultadoAluno();
  });

  $(document).on('opened.fndtn.reveal', '#modalResultadoTurma[data-reveal]', function () {
    var modal = $(this);
    mostrarResultadoTurma();
  });

  calcularMatrizes();
});

function lastSelected(selId) {
  id = selId;
}

function mostrarResultadoTurma() {
  $("#compara_turma").html("");

  var height = $(window).height() - $('#legenda_turma').height();;
  var width = $(window).width() - 40;
  var geral = new Graph();

  geral.edgeFactory.build = edgeFactory;

  for (i = 0; i < gabarito['nodes'].length; i++) {
    console.log(gabarito['nodes'][i], gabarito['grid'][i]['x'], gabarito['grid'][i]['y']);
    var newNode = geral.addNode(gabarito['nodes'][i], {render:render});
    newNode.layoutPosX = gabarito['grid'][i]['x'];
    newNode.layoutPosY = gabarito['grid'][i]['y'];
  }

  for (i = 0; i < gabarito['nodes'].length; i++) {
    for (j = 0; j < gabarito['nodes'].length; j++) {
      if (i > j) {

        /* A cor foi dividida em duas escalas:
         * Verde-Amarelo - rgb(0,255,0) até rgb(255,255,0)
         * Amarelo-Vermelho - rgb(255,255,0) até rgb(255,0,0)
         * para evitar a tonalidade marrom nas ligações com valor próximo a 0.5
        */
        if (matriz_geral[i][j] <= 0.5) {
          var verde = 255;
          var vermelho = Math.ceil(matriz_geral[i][j] * 255);
        }
        else {
          var vermelho = 255;
          var verde = Math.ceil((0.5 - matriz_geral[i][j] % 0.5)/0.5 * 255);
        }

        var cor = "rgb(" + vermelho + "," + verde + ",0)";

        geral.addEdge(gabarito['nodes'][i], gabarito['nodes'][j], {label: matriz_geral[i][j].toFixed(3), stroke : cor, "font-size": "16px"});
      }
    }
  }

  layouter = new Graph.Layout.Ordered(geral, true, null);
  renderer = new Graph.Renderer.Raphael('compara_turma', geral, width, height);
  renderer.draw();
}

function mostrarGabarito() {
  $("#gabarito").html("");

  var height = $(window).height() - $('#navbar').height();
  var width = $(window).width() - 40;
  var g = new Graph();

  g.edgeFactory.build = edgeFactory;

  for (i = 0; i < gabarito['nodes'].length; i++) {
    var newNode = g.addNode(gabarito['nodes'][i], {render:render});
    newNode.layoutPosX = gabarito['grid'][i]['x'];
    newNode.layoutPosY = gabarito['grid'][i]['y'];
  }

  for (var i = 0; i < gabarito['edges'].length; i++) {
    // TODO: alterar font-size para exibir peso das ligações quando não for padrão (1)
    g.addEdge(gabarito['edges'][i]['source'], gabarito['edges'][i]['target'], {label: gabarito['edges'][i]['weight'], stroke : EDGE_C, "font-size": "0px"});
  }

  layouter = new Graph.Layout.Ordered(g, true, null);
  renderer = new Graph.Renderer.Raphael('gabarito', g, width, height);
  renderer.draw();
}

function calcularMatrizes() {
  // valores utilizados para calculo da nota
  var max = gabarito['peso_f'];
  var min = gabarito['peso_i'];
  var n = Math.pow(max - min, 2);
  var np = gabarito['nodes'].length; // numero de palavras
  var npr = Math.pow(np, 2) - np; //posicoes relevantes
  var qtd_respostas = 0;
  var distancia_total = 0;
  var distancia_cel;
  var matriz;
  distancia = [];
  matriz_turma = [];
  matriz_geral = [];

  for (var i = 0; i < np; i++) {
    matriz_geral[i] = [];
    for (var j = 0; j < np; j++) {
      matriz_geral[i][j] = 0;
    }
  }

  // repete para cada uma das resolucoes
  for (var r = 0; r < resolucoes.length; r++) {
    distancia_cel = 0;

    var resolucao = resolucoes[r];
    var foiEntregue = alunoEntregou(resolucao.aluno);

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
        if (foiEntregue) {
          matriz_geral[i][j] += Math.pow(matriz[i][j], 2);
        }
      }
    }

    distancia[r] = distancia_cel/npr;

    if (foiEntregue) {
      distancia_total += distancia[r];
      qtd_respostas++;
    }

    $("#dma-" + resolucao['aluno']).text(distancia[r].toFixed(3));
  }

  // Altera distancia_maxima^2 caso o peso seja único
  if (n === 0) {
    n = 1;
  }

  for (var i = 0; i < np; i++) {
    for (var j = 0; j < np; j++) {
      matriz_geral[i][j] = Math.sqrt((matriz_geral[i][j]/qtd_respostas)*(1/n));
    }
    console.log(matriz_geral[i]);
  }

  var dmt = isNaN(distancia_total/qtd_respostas) ? 0 : (distancia_total/qtd_respostas).toFixed(3);
  $("#dmt").text(dmt);

}

function alunoEntregou(id) {
  for (var r = 0; r < resolucoes_db.length; r++) {
    if (Number(resolucoes_db[r].id_usuario) === id) {
      if (resolucoes_db[r].concluido === "1") {
        return true;
      }
      else {
        return false;
      }
    }
  }
  return false;
}

function mostrarResultadoAluno() {
  var pos;

  $("#tabela").html("");

  if (id !== -1) {
    for (var m = 0; m < resolucoes.length; m++) {
      var resolucao = resolucoes[m];
      if (resolucao['aluno'] == id) {
        pos = m;
        break;
      }
    }

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

    switched = false;
    updateTables(); // Calling responsive-tables.js function
    mostrarMapa(pos);
  }
}

function mostrarMapa(pos) {
  $("#compara_aluno").html("");

  var width = $(window).width() - 40;
  var height = $(window).height() - $('#legenda').height();
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
    // TODO: alterar font-size para exibir peso das ligações quando não for padrão (1)
    r.addEdge(gabarito['edges'][i]['source'], gabarito['edges'][i]['target'], {label: gabarito['edges'][i]['weight'], stroke: gabarito['edges'][i]['color'], "font-size": "0px"});

    if (gabarito['edges'][i]['color'] === PSAS_C) {
      nPSAS++;
    }
    else {
      nPSAN++;
    }
  }

  for (var i = 0; i < resolucao['edges'].length; i++) {
    // TODO: alterar font-size para exibir peso das ligações quando não for padrão (1)
    if (resolucao['edges'][i]['color'] !== PSAS_C) { // Ligações tipo PSAS já adicionadas no passo anterior!
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
}
