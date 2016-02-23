<?php
  include("api/check_login.php");
  if (isset($_GET['id']))
    hasPermission("resultados", $_GET['id']);
  else
    hasPermission(NULL, NULL);

  $id = isset($_GET['id']) ? $_GET['id'] : "";

  // $mapa_txt_php =  file_get_contents(getcwd() . "/atividades/" . $id . "/mapa.json");
  $gabarito_txt_php =  file_get_contents(getcwd() . "/atividades/" . $id . "/gabarito.json");
  $nome_resolucoes = preg_grep('/^([^.])/', scandir(getcwd() . "/atividades/" . $id . "/resolucoes"));
  $resolucoes_txt_php = array();

  foreach ($nome_resolucoes as $res) {
    array_push($resolucoes_txt_php, file_get_contents(getcwd() . "/atividades/" . $id . "/resolucoes/" . $res));
  }

  $resolucoes_array = json_encode($resolucoes_txt_php);

  // include db connect class
  require_once __DIR__ . '/api/db_connect.php';

  $queryAtividade = "SELECT atividade.*, turma.nome AS turma FROM atividade
                     INNER JOIN turma ON atividade.id_turma = turma.id
                     WHERE atividade.id = {$id}";

  $queryResolucoes = "SELECT resolucao.*, usuario.nome AS aluno FROM resolucao
                      INNER JOIN usuario ON usuario.id = resolucao.id_usuario
                      WHERE id_atividade = {$id} ORDER BY usuario.nome";

  $queryQtdEntrega = "SELECT COUNT(*) as qtd FROM resolucao r WHERE r.id_atividade = {$id} AND r.concluido = true";

  $atividades = mysqli_query($connection, $queryAtividade);
  $resolucoes = mysqli_query($connection, $queryResolucoes);
  $qtdEntrega = mysqli_query($connection, $queryQtdEntrega);
  if (!($resolucoes || $qtdEntrega || $atividades)) { die("Database query failed." . mysqli_error ($connection));}

  $atividade = mysqli_fetch_assoc($atividades);
  $qtd = mysqli_fetch_assoc($qtdEntrega);
?>


<!doctype html>
<html lang="pt" ng-app="nextex" ng-controller="langController">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Construtor de Mapa Mental">
    <title>NextEx - {{str.analise_de_resultados}}</title>
    <link rel="stylesheet" href="./css/foundation.min.css" />
    <link rel="stylesheet" href="./css/style.css" />
    <link rel="stylesheet" href="./css/responsive-tables.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="shortcut icon" href="./images/icon.png">
  </head>

  <body>
    <script type="text/javascript">
    var gabarito = <?php echo ($gabarito_txt_php); ?>;
    var resolucoes_txt = <?php echo ($resolucoes_array); ?>;
    </script>

    <div class="contain-to-grid sticky">
    <nav class="top-bar" data-topbar role="navigation" id="navbar">
      <ul class="title-area">
        <li class="name">
          <h1><a href="professor.php">NextEx <i class="fa fa-angle-right pad-l-r"></i> {{str.analise_de_resultados}}</a></h1>
        </li>
         <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
        <li class="toggle-topbar menu-icon"><a href="#"><span></span></a></li>
      </ul>

      <section class="top-bar-section">
        <!-- Right Nav Section -->
        <ul class="right">
          <li class="has-dropdown">
            <a class="dropdown-caller" href="#"><?php echo $_SESSION["nome"]; ?></a>
            <ul class="dropdown">
              <li><a href="api/logout.php"><i class="fa fa-sign-out"></i> {{str.sair}}</a></li>
            </ul>
          </li>
        </ul>
      </section>
    </nav>
    </div>


    <main class="container">

        <br><div class="row">
          <h2 class="text-center"><?php echo $atividade['titulo'] ?></h2>
          <br>
          <h5>{{str.turma}}: <strong><?php echo $atividade['turma'] ?></strong></h5>
          <h5>{{str.alunos_na_turma}}: <strong><?php echo $resolucoes->num_rows ?></strong></h5>
          <h5>{{str.atividades_entregues}}: <strong><?php echo $qtd['qtd'] ?></strong></h5>
          <h5>{{str.distancia_media_turma}}: <strong><span id="dmt"></span></strong></h5>
          <h5>{{str.data_limite_entrega}}: <strong><?php echo date("d/m/Y", strtotime($atividade['data_entrega'])) ?></strong></h5>
          <br>
          <a class="button radius small-5" data-reveal-id="modalGabarito" onclick="">{{str.ver_atividade_original}}</a>
          <a class="button radius small-5 small-offset-1" onclick="" data-reveal-id="modalResultadoTurma">{{str.ver_resultado_geral}}</a>
        </div>

        <br>
        <div class="row">
          <h5 class="small-7 columns b">{{str.aluno}}</h5>
          <h5 class="small-3 columns b">{{str.status}}</h5>
          <h5 class="small-2 columns b text-center">{{str.distancia}}</h5>
        </div>


        <?php
          $count = 0;
          while($resolucao = mysqli_fetch_assoc($resolucoes)) {
        ?>
          <div class="row">
            <a class="no-color-a" <?php if($resolucao['concluido']) echo "data-reveal-id='modalResultadoAluno'" ?> onclick="lastSelected(<?php echo $resolucao['id_usuario'] ?>)">
              <div class="row add-hover <?php if($count%2 == 0) echo "darker"?>">
                <br>
                <span class="small-7 columns"><?php echo $resolucao['aluno'] ?></span>
                <?php if ($resolucao['concluido']) { ?>
                  <span class="small-3 columns"><i class="fa fa-check-circle-o ok"></i> {{str.entregue}}</span>
                  <span class="small-2 columns text-center" id='<?php echo 'dma-' . $resolucao['id_usuario']?>'>-</span>
                <?php } else { ?>
                  <span class="small-3 columns"><i class="fa fa-times-circle-o not-ok"></i> {{str.nao_entregue}}</span>
                  <span class="small-2 columns text-center">-</span>
                <?php } ?>
                <br><br>
              </div>
            </a>
          </div>
        <?php $count++; } ?>
        <br><br>


        <div id="modalGabarito" class="reveal-modal full" data-reveal aria-labelledby="modalTitle" aria-hidden="false" role="dialog">
            <div id="gabarito"></div>
            <div><hr id="full-hr" class="invisible"></div>
          <a class="close-reveal-modal" aria-label="Close">&#215;</a>
        </div>


        <div id="modalResultadoTurma" class="reveal-modal full" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="">
          <h2 id="modalTitle">{{str.resultado_grafico_turma}}</h2>
          <p class="lead">{{str.funcionalidade_em_breve}}</p>
          <a class="close-reveal-modal" aria-label="Close">&#215;</a>
        </div>


        <div id="modalResultadoAluno" class="reveal-modal full" data-reveal aria-labelledby="modalTitle" aria-hidden="false" role="dialog">
          <div class="row" id="legenda">
            <div class="small-3 columns small-offset-2"><i class="fa fa-square psan"></i> {{str.apenas_professor_ligou}}</div>
            <div class="small-3 columns"><i class="fa fa-square pnas"></i> {{str.apenas_aluno_ligou}}</div>
            <div class="small-3 columns end"><i class="fa fa-square psas"></i> {{str.ambos_ligaram}}</div>
          </div>
          <div id="compara_aluno"></div>

            <br>
            <h3 class="text-center">{{str.tabela_de_distancias}}</h3>
            <div id="tabela"></div>
            <br>
          <a class="close-reveal-modal" aria-label="Close">&#215;</a>
        </div>

    </main>

    <script src="./js/jquery-2.1.4.min.js"></script>
    <script src="./js/foundation.min.js"></script>
    <script src="./js/angular.min.js"></script>
    <script src="./js/lang-controller.js"></script>
    <script src="./js/dracula/raphael-min.js" type="text/javascript" charset="utf-8"></script>
    <script src="./js/dracula/dracula_graph.js"></script>
    <script src="./js/dracula/dracula_algorithms.js"></script>
    <script src="./js/dracula/dracula_graffle.js"></script>
    <script src="./js/variables.js"></script>
    <script src="./js/resultados.js"></script>
    <script src="./js/responsive-tables.js"></script>
    <script>
      $(document).foundation();
    </script>
  </body>
</html>
