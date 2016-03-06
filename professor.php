<?php
  /* NextEx - Ferramenta de Avaliação
   * professor.php
   *
   * Painel de controle do professor
  */

  include("api/check_login.php");
  isLoggedIn();
  hasPermission("professor", NULL);

  include("api/atividade_aux.php");
  include("api/universidade_aux.php");
  include("api/turma_aux.php");

  $universidades = getUniversidades();
  $atividades = getAtividadesProfessor($_SESSION['id']);
  $turmas = getTurmasProfessor($_SESSION['id']);
?>

<!doctype html>
<html lang="pt" ng-app="nextex" ng-controller="langController">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Construtor de Mapa Mental">
    <title>NextEx - Dashboard</title>
    <link rel="stylesheet" href="./css/foundation.min.css" />
    <link rel="stylesheet" href="./css/style.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="shortcut icon" href="./images/icon.png">
  </head>

  <body>

    <div class="contain-to-grid sticky">
    <nav class="top-bar" data-topbar role="navigation">
      <ul class="title-area">
        <li class="name">
          <h1><a href="#"><i class="fa fa-chevron-right"></i> NextEx</a></h1>
        </li>
        <li class="toggle-topbar menu-icon"><a href="#"><span></span></a></li>
      </ul>

      <section class="top-bar-section">
        <ul class="right">
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
      <br>
      <div class="row">
        <h3>{{ str.atividades }}</h3>
        <br>
      </div>

      <div class="row">
        <h5 class="small-3 columns b">{{str.atividade}}</h5>
        <h5 class="small-3 columns b">{{str.turma}}</h5>
        <h5 class="small-2 columns b">{{str.prazo}}</h5>
        <h5 class="small-2 columns b">{{str.status}}</h5>
        <h5 class="small-2 columns b"><a href="atividade.php"><i class="fa fa-plus pad-r"></i> {{str.criar}}</a></h5>
      </div>

      <?php
        $count = 0;
        foreach ($atividades as $atividade) {
          $link = "atividade.php?id=" . $atividade['id'];
          if ($atividade['liberado']) {
            $link = "resultados.php?id=" . $atividade['id'];
          }
      ?>
        <div class="row <?php if($count%2 == 0) echo "darker"?>">
          <br>
          <a class="small-3 columns" href="<?php echo $link ?>"><?php echo $atividade['titulo'] ?></a>
          <span class="small-3 columns"><?php echo $atividade['turma'] ?></span>
          <span class="small-2 columns"><?php echo date("d/m/Y", strtotime($atividade['data_entrega'])) ?></span>
          <?php if ($atividade['liberado']) { ?>
            <span class="small-2 columns"><i class="fa fa-check-square-o pad-r"></i> {{str.liberado}}</span>
          <?php } else { ?>
            <a class="small-2 columns" onclick="liberar('<?php echo $atividade['id'] ?>');"><i class="fa fa-square-o pad-r"></i> {{str.liberar}}</a>
          <?php } ?>
          <a class="small-2 columns imp" onclick="removerAtividade('<?php echo $atividade['id'] ?>');"><i class="fa fa-minus-circle pad-r"></i> {{str.excluir}}</a>
          <br><br>
        </div>
      <?php
          $count++;
        } ?>

      <br><br>
      <div class="row">
        <h3>{{str.turmas}}</h3>
        <br>
      </div>

      <div class="row">
        <h5 class="small-4 columns b">{{str.turma}}</h5>
        <h5 class="small-4 columns b">{{str.universidade}}</h5>
        <h5 class="small-2 columns b">{{str.criado_em}}</h5>
        <h5 class="small-2 columns b"><a href="#" data-reveal-id="modalAddTurma"><i class="fa fa-plus pad-r"></i> {{str.criar}}</a></h5>
      </div>

      <?php
        $count = 0;
        foreach($turmas as $turma) { ?>
        <div class="row <?php if($count%2 == 0) echo "darker"?>">
          <br>
          <span class="small-4 columns"><?php echo $turma['nome'] ?></span>
          <span class="small-4 columns"><?php echo $turma['universidade'] ?></span>
          <span class="small-2 columns"><?php echo date("d/m/Y", strtotime($turma['data_criacao'])) ?></span>
          <a class="small-2 columns imp" onclick="removerTurma('<?php echo $turma['id'] ?>');"><i class="fa fa-minus-circle pad-r"></i> {{str.excluir}}</a>
          <br><br>
        </div>
      <?php
          $count++;
        } ?>
      <br><br>

      <div id="modalAddTurma" class="reveal-modal " data-reveal aria-labelledby="modalAddTurmaTitle" aria-hidden="true" role="dialog">
          <div class="row collapse">
            <h3 id="modalAddTurmaTitle" class="text-center">{{str.nova_turma}}</h3>
              <div id="modalAddTurmaContent" class="large-10 small-10 columns large-offset-1 small-offset-1">
                <form id="formAddTurma">
                  <br><br>
                  <div class="row">
                    <div class="small-10 small-offset-1 large-8 large-offset-2 columns">
                      <label>{{str.universidade}}
                        <select name="universidade" id="universidade">
                          <?php
                            foreach($universidades as $universidade) {
                              echo "<option value='{$universidade['id']}'>{$universidade['nome']}</option>";
                            } ?>
                        </select>
                      </label>
                    </div>
                  </div>

                  <div class="row">
                    <div class="small-10 small-offset-1 large-8 large-offset-2 columns">
                      <label>{{str.nome_da_turma}}
                        <input type="text" name="turma" id="turma" placeholder="{{str.nome_turma_placeholder}}" />
                      </label>
                    </div>
                  </div>
                  <div class="row">
                    <br>
                    <a class="button radius secondary small-5 small-offset-1 large-4 large-offset-2" onclick="$('#modalAddTurma').foundation('reveal', 'close');">{{str.cancelar}}</a>
                    <a class="button radius small-5 large-4" onclick="adicionar()">{{str.confirmar}}</a>
                  </div>
                </form>
              </div>
          </div>
      </div>

    </main>

    <script src="./js/jquery-2.1.4.min.js"></script>
    <script src="./js/foundation.min.js"></script>
    <script src="./js/professor.js"></script>
    <script src="./js/angular.min.js"></script>
    <script src="./js/lang-controller.js"></script>
    <script>
      $(document).foundation();
    </script>
  </body>
</html>

<?php
  mysqli_close($connection);
?>
