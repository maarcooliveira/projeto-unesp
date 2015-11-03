<?php
  session_start();
  include("api/check_login.php");
  if ($_SESSION["tipo"] != "professor") {
    header("Location: index.php");
  }

  $id = isset($_GET['id']) ? $_GET['id'] : "";

  $mapa_txt_php =  file_get_contents(getcwd() . "/atividades/" . $id . "/mapa.json");
  $gabarito_txt_php =  file_get_contents(getcwd() . "/atividades/" . $id . "/gabarito.json");
  $nome_resolucoes = preg_grep('/^([^.])/', scandir(getcwd() . "/atividades/" . $id . "/resolucoes"));
  $resolucoes_txt_php = array();

  foreach ($nome_resolucoes as $res) {
    array_push($resolucoes_txt_php, file_get_contents(getcwd() . "/atividades/" . $id . "/resolucoes/" . $res));
  }

  $resolucoes_array = json_encode($resolucoes_txt_php);

  // include db connect class
  require_once __DIR__ . '/api/db_connect.php';

  $queryResolucoes = "SELECT resolucao.*, usuario.nome AS aluno FROM resolucao
                      INNER JOIN usuario ON usuario.id = resolucao.id_usuario
                      WHERE id_atividade = {$id} AND concluido = true
                      ORDER BY usuario.nome";

  $resolucoes = mysqli_query($connection, $queryResolucoes);
  if (!($resolucoes)) { die("Database query failed." . mysqli_error ($connection));}
?>


<!doctype html>
<html lang="pt">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Construtor de Mapa Mental">
    <title>NextEx - Análise de resultados</title>
    <link rel="stylesheet" href="./css/foundation.min.css" />
    <link rel="stylesheet" href="./css/style.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="shortcut icon" href="./images/icon.png">
  </head>

  <body>
    <script type="text/javascript">
    var mapa_txt = <?php echo ($mapa_txt_php); ?>;
    var gabarito_txt = <?php echo ($gabarito_txt_php); ?>;
    var resolucoes_txt = <?php echo ($resolucoes_array); ?>;
    console.log("RESOLUCOES:");
    console.log(resolucoes_txt);
    </script>

    <!-- <div class="contain-to-grid sticky"> -->
    <nav class="top-bar" data-topbar role="navigation" id="navbar">
      <ul class="title-area">
        <li class="name">
          <h1><a href="dashboard_professor.php"><i class="fa fa-arrow-left"></i> NextEx</a></h1>
        </li>
         <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
        <li class="toggle-topbar menu-icon"><a href="#"><span></span></a></li>
      </ul>

      <section class="top-bar-section">
        <!-- Right Nav Section -->
        <ul class="right">
          <li class="has-dropdown">
            <a href="#"><?php echo $nome; ?></a>
            <ul class="dropdown">
              <li><a href="api/logout.php">Sair</a></li>
            </ul>
          </li>
        </ul>
      </section>
    </nav>
    <!-- </div> -->


    <main class="container">

        <!-- <br><div class="row">
          <h3>Atividade <?php /*echo $_GET['id']*/?></h3>
        </div> -->

        <div id="mapa"></div>
        <div id="gabarito"></div>

        <div>
          <hr id="full-hr">
        </div>
        <div class="row" id="toolbar">
            <div class="small-10 small-offset-1 large-6 large-offset-0 columns">
              <label>Resultados
                <select name="resultado" id="aluno_resultado">
                  <option disabled selected> -- escolha um aluno -- </option>
                  <option value="-1">Resumo da turma</option>
                  <?php
                    while($resolucao = mysqli_fetch_assoc($resolucoes)) {
                      echo "<option value='{$resolucao['id_usuario']}'>{$resolucao['aluno']}</option>";
                    } ?>
                </select>
              </label>
            </div>
            <!-- <a href="dashboard_professor.php" class="button radius small-5 small-offset-1 large-3 large-offset-0">Voltar</a> -->
        </div>

        <div class="row" id="resultados">

        </div>

    </main>

    <script src="./js/jquery-2.1.4.min.js"></script>
    <script src="./js/foundation.min.js"></script>
    <script src="./js/raphael-min.js" type="text/javascript" charset="utf-8"></script>
    <script src="./js/atividade_professor.js"></script>
    <script src="./js/dracula_graph.js"></script>
    <script src="./js/dracula_algorithms.js"></script>
    <script src="./js/dracula_graffle.js"></script>
    <script>
      $(document).foundation();
    </script>
  </body>
</html>
