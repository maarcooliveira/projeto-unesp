<?php
  include("api/check_login.php");
  isLoggedIn();
  hasPermission("professor", NULL);

  // include db connect class
  require_once __DIR__ . '/api/db_connect.php';

  $queryUni  = "SELECT * FROM universidade ORDER BY nome";
  $queryMapas = "SELECT atividade.*, turma.nome AS turma FROM atividade
                 INNER JOIN turma ON atividade.id_turma = turma.id
                 WHERE id_turma IN (SELECT id FROM turma WHERE id_professor = {$_SESSION['id']})";
  $queryTurmas = "SELECT turma.*, universidade.nome AS universidade FROM turma
                  INNER JOIN universidade ON turma.id_universidade = universidade.id
                  WHERE id_professor = {$_SESSION['id']}";

  $universidades = mysqli_query($connection, $queryUni);
  $mapas = mysqli_query($connection, $queryMapas);
  $turmas = mysqli_query($connection, $queryTurmas);
  if (!($universidades && $mapas && $turmas)) { die("Database query failed."); }
?>

<!doctype html>
<html lang="pt">
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
              <li><a href="api/logout.php">Sair</a></li>
            </ul>
          </li>
        </ul>
      </section>
    </nav>

    <main class="container">
      <br>
      <div class="row">
        <h3>Atividades</h3>
        <br>
      </div>

      <div class="row">
        <h5 class="small-3 columns b">Título</h5>
        <h5 class="small-3 columns b">Turma</h5>
        <h5 class="small-2 columns b">Entrega</h5>
        <h5 class="small-2 columns b">Status</h5>
        <h5 class="small-2 columns b"><a href="atividade.php"><i class="fa fa-plus"></i> Criar</a></h5>
      </div>

      <?php
        $count = 0;
        while($mapa = mysqli_fetch_assoc($mapas)) {
          $link = "atividade.php?id=" . $mapa['id'];
          if ($mapa['liberado']) {
            $link = "resultados.php?id=" . $mapa['id'];
          }
      ?>
        <div class="row <?php if($count%2 == 0) echo "darker"?>">
          <br>
          <a class="small-3 columns" href="<?php echo $link ?>"><?php echo $mapa['titulo'] ?></a>
          <span class="small-3 columns"><?php echo $mapa['turma'] ?></span>
          <span class="small-2 columns"><?php echo date("d/m/Y", strtotime($mapa['data_entrega'])) ?></span>
          <?php if ($mapa['liberado']) { ?>
            <span class="small-2 columns"><i class="fa fa-check-square-o"></i> Liberado</span>
          <?php } else { ?>
            <a class="small-2 columns" onclick="liberar('<?php echo $mapa['id'] ?>');"><i class="fa fa-square-o"></i> Liberar</a>
            <a class="small-2 columns imp" onclick="remover('atividade', '<?php echo $mapa['id'] ?>');"><i class="fa fa-minus-circle"></i> Excluir</a>
          <?php } ?>
          <br><br>
        </div>
      <?php $count++; } ?>

      <br><br>
      <div class="row">
        <h3>Turmas</h3>
        <br>
      </div>

      <div class="row">
        <h5 class="small-4 columns b">Turma</h5>
        <h5 class="small-4 columns b">Universidade</h5>
        <h5 class="small-2 columns b">Criado em</h5>
        <h5 class="small-2 columns b"><a href="#" data-reveal-id="modalAddTurma"><i class="fa fa-plus"></i> Criar</a></h5>
      </div>

      <?php
        $count = 0;
        while($turma = mysqli_fetch_assoc($turmas)) { ?>
        <div class="row <?php if($count%2 == 0) echo "darker"?>">
          <br>
          <span class="small-4 columns"><?php echo $turma['nome'] ?></span>
          <span class="small-4 columns"><?php echo $turma['universidade'] ?></span>
          <span class="small-2 columns"><?php echo date("d/m/Y", strtotime($turma['data_criacao'])) ?></span>
          <a class="small-2 columns imp" onclick="remover('turma', '<?php echo $turma['id'] ?>');"><i class="fa fa-minus-circle"></i> Excluir</a>
          <br><br>
        </div>
      <?php $count++; } ?>
      <br><br>

      <div id="modalAddTurma" class="reveal-modal " data-reveal aria-labelledby="modalAddTurmaTitle" aria-hidden="true" role="dialog">
          <div class="row collapse">
            <h3 id="modalAddTurmaTitle" class="text-center">Nova turma</h3>
              <div id="modalAddTurmaContent" class="large-10 small-10 columns large-offset-1 small-offset-1">
                <form id="formAddTurma">
                  <br><br>
                  <div class="row">
                    <div class="small-10 small-offset-1 large-8 large-offset-2 columns">
                      <label>Universidade
                        <select name="universidade" id="universidade">
                          <?php
                            while($universidade = mysqli_fetch_assoc($universidades)) {
                              echo "<option value='{$universidade['id']}'>{$universidade['nome']}</option>";
                            } ?>
                        </select>
                      </label>
                    </div>
                  </div>

                  <div class="row">
                    <div class="small-10 small-offset-1 large-8 large-offset-2 columns">
                      <label>Nome da turma
                        <input type="text" name="turma" id="turma" placeholder="Ex: Algoritmos e Programação I" />
                      </label>
                    </div>
                  </div>
                  <div class="row">
                    <br>
                    <a class="button radius secondary small-5 small-offset-1 large-4 large-offset-2" onclick="$('#modalAddTurma').foundation('reveal', 'close');">Cancelar</a>
                    <a class="button radius small-5 large-4" onclick="adicionar()">Confirmar</a>
                  </div>
                </form>
              </div>
          </div>
      </div>

    </main>

    <script src="./js/jquery-2.1.4.min.js"></script>
    <script src="./js/foundation.min.js"></script>
    <script src="http://connect.facebook.net/en_US/all.js"></script>
    <script src="./js/professor.js"></script>
    <script>
      $(document).foundation();
    </script>
  </body>
</html>

<?php
  mysqli_free_result($universidades);
  mysqli_free_result($mapas);
  mysqli_free_result($turmas);
  mysqli_close($connection);
?>
