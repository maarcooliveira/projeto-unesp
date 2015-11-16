<?php
  session_start();
  include("api/check_login.php");
  if ($_SESSION["tipo"] != "aluno") {
    header("Location: index.php");
  }

  require_once __DIR__ . '/api/db_connect.php';

  if (isset($_POST['submit'])) {

    $turma = isset($_POST['turma']) ? $_POST['turma'] : "";

    $query  = "INSERT INTO usuario_turma (id_usuario, id_turma) VALUES ({$_SESSION['id']}, {$turma})";
    $result = mysqli_query($connection, $query);
    if (!$result) { die("Database query failed. " . mysqli_error ($connection)); }
  }

  $queryOutrasTurmas = "SELECT * FROM turma WHERE id_universidade IN (SELECT id_universidade FROM usuario WHERE id = {$_SESSION['id']}) AND id NOT IN (SELECT id_turma FROM usuario_turma WHERE id_usuario = {$_SESSION['id']})";
  $queryInsertResolucao = "INSERT IGNORE INTO resolucao (id_atividade, id_usuario, concluido) SELECT id, {$_SESSION['id']}, false FROM atividade WHERE id_turma IN (SELECT id_turma FROM usuario_turma WHERE id_usuario = {$_SESSION['id']})";

  $queryMapas = "SELECT atividade.*, turma.nome AS turma, resolucao.concluido AS concluido FROM atividade
                 INNER JOIN turma ON atividade.id_turma = turma.id
                 INNER JOIN resolucao ON (atividade.id = resolucao.id_atividade AND resolucao.id_usuario = {$_SESSION['id']})
                 WHERE id_turma IN (SELECT id_turma FROM usuario_turma WHERE id_usuario = {$_SESSION['id']})
                 AND liberado = true";

  $queryTurmas = "SELECT turma.*, usuario.nome AS professor FROM turma
                  INNER JOIN usuario ON turma.id_professor = usuario.id
                  WHERE turma.id IN (SELECT id_turma FROM usuario_turma WHERE id_usuario = {$_SESSION['id']})";

  $insertResolucao = mysqli_query($connection, $queryInsertResolucao);
  $mapas = mysqli_query($connection, $queryMapas);
  $turmas = mysqli_query($connection, $queryTurmas);
  $outrasTurmas = mysqli_query($connection, $queryOutrasTurmas);
  if (!($insertResolucao && $outrasTurmas && $mapas && $turmas)) { die("Database query failed." . mysqli_error ($connection));}
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
            <a href="#"><?php echo $nome; ?></a>
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
        <h3>Minhas Atividades</h3>
        <br>
      </div>

      <div class="row">
        <h5 class="small-3 columns b">Título</h5>
        <h5 class="small-3 columns b">Turma</h5>
        <h5 class="small-3 columns b">Entrega</h5>
        <h5 class="small-3 columns b">Status</h5>
      </div>


      <?php $count = 0;
        while($mapa = mysqli_fetch_assoc($mapas)) { ?>
          <div class="row <?php if($count%2 == 0) echo "darker"?>">
            <br>
            <a class="small-3 columns" href="avaliacao.php?id=<?php echo $mapa['id'] ?>"><?php echo $mapa['titulo'] ?></a>
            <span class="small-3 columns"><?php echo $mapa['turma'] ?></span>
            <span class="small-3 columns"><?php echo date("d/m/Y", strtotime($mapa['data_entrega'])) ?></span>
            <span class="small-3 columns"><?php if ($mapa['concluido'] == 1) echo "Entregue"; else echo "Não entregue"; ?></span>
            <br><br>
          </div>
      <?php $count++; } ?>

      <br><br>
      <div class="row">
        <h3>Turmas das quais participo</h3>
        <br>
      </div>

      <div class="row">
        <h5 class="small-6 columns b">Turma</h5>
        <h5 class="small-3 columns b">Professor</h5>
        <h5 class="small-3 columns b"><a href="#" data-reveal-id="modalAddTurma"><i class="fa fa-plus"></i> Nova turma</a></h5>
      </div>


      <?php $count = 0;
        while($turma = mysqli_fetch_assoc($turmas)) { ?>
          <div class="row <?php if($count%2 == 0) echo "darker"?>">
            <br>
            <span class="small-6 columns"><?php echo $turma['nome'] ?></span>
            <span class="small-3 columns"><?php echo $turma['professor'] ?></span>
            <a class="small-3 columns imp" onclick="remover('usuario_turma', '<?php echo $turma['id'] ?>');"><i class="fa fa-minus-circle"></i> Sair da turma</a>
            <br><br>
          </div>
      <?php $count++; } ?>
      <br><br>


      <div id="modalAddTurma" class="reveal-modal " data-reveal aria-labelledby="modalAddTurmaTitle" aria-hidden="true" role="dialog">
          <div class="row collapse">
            <h3 id="modalAddTurmaTitle" class="text-center">Participar de nova turma</h3>
              <div id="modalAddTurmaContent" class="large-10 small-10 columns large-offset-1 small-offset-1">


                <form action="aluno.php" method="post" id="formAddTurma">
                  <br><br>
                  <div class="row">
                    <div class="small-10 small-offset-1 large-8 large-offset-2 columns">
                      <label>Turmas na sua universidade
                        <select name="turma">
                          <?php
                            while($outraTurma = mysqli_fetch_assoc($outrasTurmas)) {
                              echo "<option value='{$outraTurma['id']}'>{$outraTurma['nome']}</option>";
                            } ?>
                        </select>
                      </label>
                    </div>
                  </div>

                  <div class="row">
                    <br>
                    <a class="button radius secondary small-5 small-offset-1 large-4 large-offset-2" onclick="$('#modalAddTurma').foundation('reveal', 'close');">Cancelar</a>
                    <input type="submit" name="submit" class="button radius small-5 large-4" value="Confirmar">
                  </div>
                </form>

              </div>
          </div>
      </div>

    </main>

    <script src="./js/jquery-2.1.4.min.js"></script>
    <script src="./js/foundation.min.js"></script>
    <script src="http://connect.facebook.net/en_US/all.js"></script>
    <script src="./js/aluno.js"></script>
    <script>
      $(document).foundation();
    </script>
  </body>
</html>

<?php
  // mysqli_free_result($universidades);
  // mysqli_free_result($mapas);
  // mysqli_free_result($turmas);
  mysqli_close($connection);
?>
