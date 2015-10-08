var width, height, g, renderer, layouter, render, src, dest, edge, canSelectDest = false;
var complete_graph, incomplete_graph;
var mapa = {};
var matriz_t = [];
var qtd_respostas, distancia_total, np, distancia;

//TODO: permitir professor editar enquanto não libera atividade!

gabarito = gabarito_txt;

$( document ).ready(function() {

        toolbar_height = $('#toolbar').height();
        navbar_heigth = $('#navbar').height();
        row_width = $('#full-hr').width();

        width = row_width;
        height = $(window).height() - toolbar_height - navbar_heigth;

        g = new Graph();

        /* modify the edge creation to attach random weights */
        g.edgeFactory.build = function(source, target) {
            var e = jQuery.extend(true, {}, this.template);
            e.source = source;
            e.target = target;
            return e;
        }

        render = function(r, n) {
            var color = Raphael.getColor();
            /* the Raphael set is obligatory, containing all you want to display */

            rec = r.rect(n.point[0], n.point[1], 130, 20);
            txt = r.text(n.point[0], n.point[1], (n.label || n.id)).attr({"font-size": "16px"});

            w = txt.getBBox().width + 50;
            h = txt.getBBox().height + 20;
            x = txt.getBBox().x - 25;
            y = txt.getBBox().y - 10;

            attrs = {"title": (n.label || n.id), "fill": color, "stroke": color, r: "1px", "stroke-width": "1px", "width": w, "height": h, "x": x, "y": y};
            rec.attr(attrs);
            var set = r.set().push(rec).push(txt);
            return set;
        };

        for (i = 0; i < gabarito['nodes'].length; i++) {
            var newNode = g.addNode(gabarito['nodes'][i], {render:render});
            newNode.layoutPosX = gabarito['grid'][i]['x'];
            newNode.layoutPosY = gabarito['grid'][i]['y'];
        }

        for (var i = 0; i < gabarito['edges'].length; i++) {
            g.addEdge(gabarito['edges'][i]['source'], gabarito['edges'][i]['target'], {label: gabarito['edges'][i]['weight'], stroke : "#C7C7C7", "font-size": "16px"});
        }

      //   layouter = new Graph.Layout.Ordered(g, topological_sort(g));
        layouter = new Graph.Layout.Grid(g, true);
        renderer = new Graph.Renderer.Raphael('gabarito', g, width, height);
        renderer.draw();
        calcularMatrizes();
});

function calcularMatrizes() {
    // valores utilizados para calculo da nota
    var max = gabarito['peso_f'];
    var min = gabarito['peso_i'];
    var n = Math.pow(max - min, 2);
    np = gabarito['nodes'].length;
    var npr = Math.pow(np, 2) - np; //posicoes relevantes
    qtd_respostas = resolucoes_txt.length;
    distancia_total = 0;
    var distancia_cel;
    var matriz;
    distancia = [];

    // repete para cada uma das resolucoes
    for (var r = 0; r < resolucoes_txt.length; r++) {
        distancia_cel = 0;

        var resolucao = JSON.parse(resolucoes_txt[r]);
        console.log(resolucao);

        matriz_t[r] = [];
        matriz = matriz_t[r];

        // inicializa matriz com valores das ligacoes do aluno
        for (var i = 0; i < np; i++) {
            matriz[i] = [];
            for (var j = 0; j < np; j++) {
                matriz[i][j] = 0;
                for (var nc = 0; nc < resolucao['edges'].length; nc++) {
                    if (gabarito['nodes'][i] === resolucao['edges'][nc]['source'] && gabarito['nodes'][j] === resolucao['edges'][nc]['target']) {
                        matriz[i][j] = resolucao['edges'][nc]['weight'];
                        break;
                    }
                    else if (gabarito['nodes'][i] === resolucao['edges'][nc]['target'] && gabarito['nodes'][j] === resolucao['edges'][nc]['source']) {
                        matriz[i][j] = resolucao['edges'][nc]['weight'];
                        break;
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
                        break;
                    }
                    else if (gabarito['nodes'][i] === gabarito['edges'][nc]['target'] && gabarito['nodes'][j] === gabarito['edges'][nc]['source']) {
                        matriz[i][j] -= gabarito['edges'][nc]['weight'];
                        break;
                    }
                }
                distancia_cel += Math.pow(matriz[i][j], 2);
            }
        }
        distancia[r] = Math.sqrt(distancia_cel/(n*npr));
        distancia_total += distancia[r];
    }
}

$("#aluno_resultado").change(function() {
    var id = $("#aluno_resultado").val();
    console.log(id);
    var pos;

    $("#resultados").html("");

    if (id === "-1") {
        //TODO: matriz consolidada da sala;
        $("#resultados").html("<br>Distância média da sala: " + distancia_total/qtd_respostas);
    }
    else {
        for (var m = 0; m < resolucoes_txt.length; m++) {
            var resolucao = JSON.parse(resolucoes_txt[m]);
            if (resolucao['aluno'] == id) {
                pos = m;
                break;
            }
        }

        var matriz = matriz_t[pos];

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
                result += "<td>" + matriz[i][j] + "</td>";
            }
            result += "</tr>"
        }

        result += "</table>";
        result += "Distancia do aluno: " + distancia[pos] + "<br><br>";
        $("#resultados").append(result);
    }
});
