<?php
  include("api/check_login.php");
  isLoggedIn();
  if (isset($_GET['id']))
    hasPermission("avaliacao", $_GET['id']);
  else
    hasPermission(NULL, NULL);

  // include db connect class
  require_once __DIR__ . '/api/db_connect.php';

  $id = isset($_GET['id']) ? $_GET['id'] : "";

  $resolucao_txt_php = "";
  if (file_exists(getcwd() . "/atividades/" . $id ."/resolucoes/" . $_SESSION['id'] . "_mapa.json")) {
    $resolucao_txt_php =  file_get_contents(getcwd() . "/atividades/" . $id ."/resolucoes/" . $_SESSION['id'] . "_mapa.json");
  }

  $queryResolucao = "SELECT * FROM resolucao WHERE id_atividade = {$id} AND id_usuario = {$_SESSION['id']}";
  $resolucao = mysqli_query($connection, $queryResolucao);
  if (!$resolucao) { die("Database query failed." . mysqli_error ($connection));}

  $foiResolvido = false;
  while($res = mysqli_fetch_assoc($resolucao)) {
    if ($res['concluido'])
      $foiResolvido = true;
  }
  //pode fechar conexão com db neste ponto

  $mapa_txt_php =  file_get_contents(getcwd() . "/atividades/" . $id . "/mapa.json");

  if ($foiResolvido) {
    $gabarito_txt_php =  file_get_contents(getcwd() . "/atividades/" . $id . "/gabarito.json");
  }
  else {
    $gabarito_txt_php = null;
  }
?>

<!doctype html>
<html lang="pt">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Construtor de Mapa Mental">
    <title>NextEx - Atividade</title>
    <link rel="stylesheet" href="./css/foundation.min.css" />
    <link rel="stylesheet" href="./css/style.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="shortcut icon" href="./images/icon.png">
  </head>

  <body>
    <script type="text/javascript">
    var mapa = <?php echo ($mapa_txt_php); ?>;
    var gabarito = undefined;
    var resolucao = undefined;
    var id_aluno = <?php echo ($_SESSION['id']); ?>;
    var id_atividade = <?php echo ($_GET['id']); ?>;
    <?php
      if (strlen($resolucao_txt_php) > 0)
        echo ("resolucao = " . $resolucao_txt_php);
      echo ("\n");
      if (strlen($gabarito_txt_php) > 0)
        echo ("var gabarito = " . $gabarito_txt_php);
    ?>;
    </script>

    <!-- <div class="contain-to-grid sticky"> -->
    <nav class="top-bar" data-topbar role="navigation" id="navbar">
      <ul class="title-area">
        <li class="name">
          <h1><a href="aluno.php"><i class="fa fa-arrow-left"></i> NextEx</a></h1>
        </li>
         <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
        <li class="toggle-topbar menu-icon"><a href="#"><span></span></a></li>
      </ul>

      <section class="top-bar-section">
        <!-- Right Nav Section -->
        <ul class="right">
          <li><a id="tb_remover" onclick="removeEdge();"><i class="fa fa-chain-broken"></i> Remover</a></li>
          <li><a id="tb_cancelar" onclick="cancelSelect();"><i class="fa fa-times"></i> Cancelar</a></li>
          <li><a id="tb_salvar" onclick="salvar();"><i class="fa fa-floppy-o"></i> Salvar</a></li>
          <li><a id="tb_enviar" onclick="enviar();"><i class="fa fa-check"></i> Enviar</a></li>
          <li><a id="tb_ajuda" onclick="mostrarDescricao();"><i class="fa fa-info-circle"></i> Ajuda</a></li>
          <li class="divider"></li>
          <li class="has-dropdown">
            <a href="#"><?php echo $_SESSION["nome"]; ?></a>
            <ul class="dropdown">
              <li><a href="api/logout.php">Sair</a></li>
            </ul>
          </li>
        </ul>
      </section>
    </nav>
    <!-- </div> -->

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
          <h2 id="modalDescTitle">Instruções para esta atividade</h2>
            <div id="modalDescContent" class="large-10 small-10 columns large-offset-1 small-offset-1"></div>
          </div>
      </div>

      <div class="row" id="resultados"></div>
      <div class="row" id="gabarito"></div>
    </main>

    <script src="./js/jquery-2.1.4.min.js"></script>
    <script src="./js/foundation.min.js"></script>
    <script src="./js/raphael-min.js" type="text/javascript" charset="utf-8"></script>
    <script src="./js/dracula_graph.js"></script>
    <script src="./js/dracula_algorithms.js"></script>
    <script src="./js/dracula_graffle.js"></script>
    <script src="./js/noty/packaged/jquery.noty.packaged.min.js"></script>
    <script src="./js/avaliacao.js"></script>
    <script>
      $(document).foundation();
    </script>
  </body>
</html>
