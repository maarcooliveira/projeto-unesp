<?php
  include("api/check_login.php");
  isLoggedIn();
  hasPermission("aluno", NULL);

  include("api/atividade_aux.php");
  include("api/turma_aux.php");

  $turmas = getTurmasAluno($_SESSION['id']);
  $turmasNaoInscrito = getTurmasAlunoNaoInscrito($_SESSION['id']);
  $atividades = getAtividadesAluno($_SESSION['id']);
  insertResolucoesAluno($_SESSION['id']);
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

    <nav class="top-bar" data-topbar role="navigation">
      <ul class="title-area">
        <li class="name">
          <h1><a href="#"><i class="fa fa-bars"></i> NextEx</a></h1>
        </li>
         <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
        <li class="toggle-topbar menu-icon"><a href="#"><span></span></a></li>
      </ul>

      <section class="top-bar-section">
        <!-- Right Nav Section -->
        <ul class="right">
          <li class="has-dropdown">
            <a href="#"><?php echo $_SESSION["nome"]; ?></a>
            <ul class="dropdown">
              <li><a href="api/logout.php">{{str.sair}}</a></li>
            </ul>
          </li>
        </ul>
      </section>
    </nav>

    <main class="container">
      <br>

      <div class="row">
        <h3>{{str.minhas_atividades}}</h3>
        <br>
      </div>

      <div class="row">
        <h5 class="small-3 columns b">{{str.atividade}}</h5>
        <h5 class="small-3 columns b">{{str.turma}}</h5>
        <h5 class="small-3 columns b">{{str.prazo}}</h5>
        <h5 class="small-3 columns b">{{str.status}}</h5>
      </div>


      <?php $count = 0;
        foreach($atividades as $atividade) { ?>
          <div class="row <?php if($count%2 == 0) echo "darker"?>">
            <br>
            <a class="small-3 columns" href="avaliacao.php?id=<?php echo $atividade['id'] ?>"><?php echo $atividade['titulo'] ?></a>
            <span class="small-3 columns"><?php echo $atividade['turma'] ?></span>
            <span class="small-3 columns"><?php echo date("d/m/Y", strtotime($atividade['data_entrega'])) ?></span>
            <span class="small-3 columns"><?php if ($atividade['concluido'] == 1) echo "{{str.entregue}}"; else echo "{{str.nao_entregue}}"; ?></span>
            <br><br>
          </div>
      <?php $count++; } ?>

      <br><br>
      <div class="row">
        <h3>{{str.turmas_que_participo}}</h3>
        <br>
      </div>

      <div class="row">
        <h5 class="small-6 columns b">{{str.turma}}</h5>
        <h5 class="small-3 columns b">{{str.professor}}</h5>
        <h5 class="small-3 columns b"><a href="#" data-reveal-id="modalAddTurma"><i class="fa fa-plus"></i> {{str.nova_turma}}</a></h5>
      </div>

      <?php $count = 0;
        foreach($turmas as $turma) { ?>
          <div class="row <?php if($count%2 == 0) echo "darker"?>">
            <br>
            <span class="small-6 columns"><?php echo $turma['nome'] ?></span>
            <span class="small-3 columns"><?php echo $turma['professor'] ?></span>
            <a class="small-3 columns imp" onclick="deixarTurma('<?php echo $turma['id'] ?>');"><i class="fa fa-minus-circle"></i> {{str.sair_da_turma}}</a>
            <br><br>
          </div>
      <?php $count++; } ?>
      <br><br>


      <div id="modalAddTurma" class="reveal-modal " data-reveal aria-labelledby="modalAddTurmaTitle" aria-hidden="true" role="dialog">
          <div class="row collapse">
            <h3 id="modalAddTurmaTitle" class="text-center">{{str.participar_nova_turma}}</h3>
              <div id="modalAddTurmaContent" class="large-10 small-10 columns large-offset-1 small-offset-1">

                <br><br>
                <div class="row">
                  <div class="small-10 small-offset-1 large-8 large-offset-2 columns">
                    <label>{{str.turmas_na_universidade}}
                      <select id="turma">
                        <?php
                          $cntTurma = 0;
                          foreach($turmasNaoInscrito as $turma) {
                            $cntTurma++;
                            echo "<option value='{$turma['id']}'>{$turma['nome']}" . " - Prof. " . "{$turma['professor']}</option>";
                          } ?>
                      </select>
                    </label>
                  </div>
                  <input id="usuario" type="hidden" value="<?php echo $_SESSION['id'];?>">
                </div>

                <div class="row">
                  <br>
                  <button onclick="$('#modalAddTurma').foundation('reveal', 'close');" class="button radius secondary small-5 small-offset-1 large-4 large-offset-2">{{str.cancelar}}</button>
                  <button <?php if($cntTurma == 0) echo "disabled";?> onclick="entrarNaTurma()" class="button radius small-5 large-4">{{str.confirmar}}</button>
                </div>

              </div>
          </div>
      </div>

    </main>

    <script src="./js/jquery-2.1.4.min.js"></script>
    <script src="./js/foundation.min.js"></script>
    <script src="./js/angular.min.js"></script>
    <script src="./js/lang-controller.js"></script>
    <!-- <script src="http://connect.facebook.net/en_US/all.js"></script> -->
    <script src="./js/aluno.js"></script>
    <script>
      $(document).foundation();
    </script>
  </body>
</html>

<?php
  mysqli_close($connection);
?>
