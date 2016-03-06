/* NextEx - Ferramenta de Avaliação
 * js/nextex_graph.js
 *
 * Variáveis e funções utilizadas em diferentes páginas da aplicação, relacionadas
 * à configuração dos gráficos. Funções e variáveis aqui definidas são utilizadas
 * em atividade.js, avaliação.js e resultados.js
*/

var PSAN_C = "#E33258"; // Cor para ligações feitas apenas pelo professor
var PSAS_C = "#4ECDC4"; // Cor para ligações feitas pelo professor e aluno
var PNAS_C = "#F8CA00"; // Cor para ligações feitas apenas pelo aluno
var NODE_C = "#1565c0"; // Cor da caixa dos termos
var EDGE_C = "#b0bec5"; // Cor padrão das ligações
var FILL_C = "#ffffff"; // Cor do texto dos termos
var NODE_SEL_C = "#F8B500"; // Cor da caixa dos termos selecionados

// Aqui são definidos o tamanho, padding, etc da caixa dos termos (rec) e propriedades
// do texto dos termos (txt) como cor e tamanho da fonte
var render = function(r, n) {
  var color = NODE_C;

  rec = r.rect(n.point[0], n.point[1], 130, 20);
  txt = r.text(n.point[0], n.point[1], (n.label || n.id)).attr({"font-size": "12px", "fill": FILL_C});

  w = txt.getBBox().width + 20;
  h = txt.getBBox().height + 20;
  x = txt.getBBox().x - 10;
  y = txt.getBBox().y - 10;

  attrs = {"title": (n.label || n.id), "fill": color, "stroke": color, r: "2px", "stroke-width": "0px", "width": w, "height": h, "x": x, "y": y};
  rec.attr(attrs);
  var set = r.set().push(rec).push(txt);
  return set;
};

var edgeFactory = function(source, target) {
  var e = jQuery.extend(true, {}, this.template);
  e.source = source;
  e.target = target;
  return e;
}

// Aqui fica a lógica do click nos termos, quando realizar ligação, quando descelecionar, etc
function addBoxClickListener() {
  $(document).on('click', '#canvas rect', function () {
    // Se o elemento clicado já estava selecionado, remover seleção;
    if($(this).attr('class') === 'selected') {
      removeSelectColor(src);
      src = undefined;
    }
    // Foi selecionado um elemento que não estava selecionado
    else {
      // Se já tem um elemento previamente selecionado...
      if (src) {
        toggleDestSelection(true);
        // Se o usuário ativou a função de link, define 'this' como destino e adiciona ligação;
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
      // Não há elemento previamente selecionado, portanto, 'this' se torna src da ligação
      else {
        src = this;
        addSelectColor(src);
      }
    }
    toggleItemSelected();
  });
}

/* Sempre que um item é selecionado, o contexto do menu na navbar também muda.
 Se o usuário estiver no app android, a mudança deve ser feita no app criando
 uma função changeMenuContext, que vai receber como parâmetro 'srcSelected'
 para quando um termo foi selecionado ou 'noneSelected' quando não há termos
 selecionados. */
function toggleItemSelected() {
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

// Retorna a caixa do termo para a cor padrão
function removeSelectColor(obj) {
  $(obj).attr('class', '');
  $(obj).attr('fill', NODE_C);
  text = $(obj).parent().next();
}

// Coloca a cor de seleção para a caixa de texto
function addSelectColor(obj) {
  $(obj).attr('class', 'selected');
  $(obj).attr('fill', NODE_SEL_C);
  text = $(obj).parent().next();
}

// Cancela a seleção de termos
function cancelSelect() {
  removeSelectColor(src);
  src = undefined;
  dest = undefined;
  edge = undefined;
  toggleDestSelection(false);
  toggleItemSelected();
}

// Adiciona uma ligação entre os termos em src e dest
function addEdge(weight) {
  var from = $(src).parent().attr('title');
  var to = $(dest).parent().attr('title');

  if (hasEdge(from, to)) {
    // Notificação de ligação já existente
    var n = noty({
      text: '<i class="fa fa-repeat pad-r"></i> \"<strong>' + from + '</strong>\" ' + str.e + ' \"<strong>' + to + '</strong>\" ' + str.ja_relacionados,
      layout: 'topLeft',
      type: 'warning',
      theme: 'relax',
      timeout: 3000
    });

    cancelSelect();
    return;
  }

  // TODO: Quando o peso não for padrão (1), alterar font-size para exibir o peso em cada ligação
  var newEdge = g.addEdge(from, to, {label: weight, stroke : EDGE_C, "font-size": "0px"});

  renderer.draw();

  // Notificação de ligação inserida
  var n = noty({
    text: '<i class="fa fa-link pad-r"></i> \"<strong>' + from + '</strong>\" ' + str.e + ' \"<strong>' + to + '</strong>\" ' + str.foram_relacionados,
    layout: 'topLeft',
    type: 'information',
    theme: 'relax',
    timeout: 3000
  });

  cancelSelect();

  // Click listener para remover ligação
  (function (_eC, _nE) {
    $(_nE.connection.fg[0]).data('edgeId', _eC);
    $(_nE.connection.fg[0]).on('click', function() {
      removeEdge(_nE);
    });
  })(edgeCount, newEdge);
  edgeCount++;
}

// Remove uma ligação
function removeEdge(edge) {
  var index = g.edges.indexOf(edge);
  edge.remove();
  g.edges.splice(index, 1);

  var from = edge.source.id;
  var to = edge.target.id;
  var n = noty({
    text: '<i class="fa fa-chain-broken pad-r"></i> \"<strong>' + from + '</strong>\" ' + str.e + ' \"<strong>' + to + '</strong>\" ' + str.foram_desrelacionados,
    layout: 'topLeft',
    type: 'error',
    theme: 'relax',
    timeout: 3000
  });
}

// Verifica se já há uma ligação entre dois termos
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

// Exibe caixa de entrada do peso da ligação. Devido pedidos para simplicidade
// nesta fase do projeto, todas as ligações tem como padrão o peso 1
function showInput() {
  // TODO: descomentar código abaixo e remover addEdge(1) quando o peso não for (1) como padrão
  // if(typeof Android != 'undefined') {
  //     Android.askEdgeWeight();
  // }
  // else {
  //     $('#myModal').foundation('reveal','open');
  // }

  addEdge(1);
}

// Carrega strings de acordo com o idioma do usuário para ser utilizado nas notificações
// de ligação
function getInterfaceStr() {
  $.getJSON( "./data/strings.json", function(data) {
    var userLang = navigator.language || navigator.userLanguage;

    if (userLang.split('-')[0] !== 'pt') {
      str = data["en-US"];
    }
    else {
      str = data["pt-BR"];
    }
  });
}
