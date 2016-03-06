<?php
  include("api/check_login.php");
  isLoggedIn();
  if (isset($_GET['id']))
    hasPermission("avaliacao", $_GET['id']);
  else
    hasPermission(NULL, NULL);

  include("api/arquivo_aux.php");
  include("api/atividade_aux.php");

  $id = isset($_GET['id']) ? $_GET['id'] : "";
  $resolucao = getResolucao($id, $_SESSION['id']);
  $atividade_json = getJsonAtividade($id);
  $resolucao_json = getJsonResolucao($id, $_SESSION['id']);
  $gabarito_json = $resolucao['concluido'] ? getJsonGabarito($id) : null;
?>

<!doctype html>
<html lang="pt" ng-app="nextex" ng-controller="langController">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Construtor de Mapa Mental">
    <title>NextEx - {{str.atividade}}</title>
    <link rel="stylesheet" href="./css/foundation.min.css" />
    <link rel="stylesheet" href="./css/style.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="shortcut icon" href="./images/icon.png">
  </head>

  <body>
    <script type="text/javascript">
    var mapa = <?php echo ($atividade_json); ?>;
    var gabarito = undefined;
    var resolucao = undefined;
    var id_aluno = <?php echo ($_SESSION['id']); ?>;
    var id_atividade = <?php echo ($_GET['id']); ?>;
    <?php
      if (strlen($resolucao_json) > 0)
        echo ("resolucao = " . $resolucao_json);
      echo ("\n");
      if (strlen($gabarito_json) > 0)
        echo ("gabarito = " . $gabarito_json);
    ?>;
    </script>

    <div class="contain-to-grid sticky">
    <nav class="top-bar" data-topbar role="navigation" id="navbar">
      <ul class="title-area">
        <li class="name">
          <h1><a href="aluno.php">NextEx <i class="fa fa-angle-right pad-l-r"></i> {{str.atividade}}</a></h1>
        </li>
         <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
        <li class="toggle-topbar menu-icon"><a href="#"><span></span></a></li>
      </ul>

      <section class="top-bar-section">
        <!-- Right Nav Section -->
        <ul class="right">
          <li><a id="tb_cancelar" onclick="cancelSelect();"><i class="fa fa-times pad-r"></i> {{str.cancelar}}</a></li>
          <li><a id="tb_salvar" onclick="salvar();"><i class="fa fa-floppy-o pad-r"></i> {{str.salvar}}</a></li>
          <li><a id="tb_enviar" onclick="enviar();"><i class="fa fa-check pad-r"></i> {{str.enviar}}</a></li>
          <li><a id="tb_ajuda" onclick="mostrarDescricao();"><i class="fa fa-info-circle pad-r"></i> {{str.ajuda}}</a></li>
          <li class="divider"></li>
          <li class="has-dropdown">
            <a class="dropdown-caller" href="#"><i class="fa fa-user pad-l-r"></i> <?php echo $_SESSION["nome"]; ?></a>
            <ul class="dropdown">
              <li><a href="api/logout.php"><i class="fa fa-sign-out pad-l-r"></i> {{str.sair}}</a></li>
            </ul>
          </li>
        </ul>
      </section>
    </nav>
    </div>

    <main class="container">
      <div><hr id="full-hr"></div>
      <div id="canvas"></div>

      <div id="myModal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
          <div class="row collapse">
          <h2 id="modalTitle">Peso da ligação</h2>
            <div class="large-12 small-12 columns">
              <input id="tb_peso" type="number" placeholder="Peso" value="0">
            </div>
            <button id="confirma_peso">Confirmar</button>
          </div>
      </div>

      <div id="modalDesc" class="reveal-modal" data-reveal aria-labelledby="modalDescTitle" aria-hidden="true" role="dialog">
          <div class="row collapse">
          <h2 id="modalDescTitle">{{str.instrucoes_atividade}}</h2>
            <div id="modalDescContent" class="large-10 small-10 columns large-offset-1 small-offset-1"></div>
          </div>
      </div>

      <div class="row" id="resultados"></div>
      <div id="gabarito"></div>
    </main>

    <script src="./js/jquery-2.1.4.min.js"></script>
    <script src="./js/foundation.min.js"></script>
    <script src="./js/angular.min.js"></script>
    <script src="./js/lang-controller.js"></script>
    <script src="./js/dracula/raphael-min.js" type="text/javascript" charset="utf-8"></script>
    <script src="./js/dracula/dracula_graph.js"></script>
    <script src="./js/dracula/dracula_algorithms.js"></script>
    <script src="./js/dracula/dracula_graffle.js"></script>
    <script src="./js/noty/noty.packaged.js"></script>
    <script src="./js/nextex_graph.js"></script>
    <script src="./js/avaliacao.js"></script>
    <script>
      $(document).foundation();
    </script>
  </body>
</html>
