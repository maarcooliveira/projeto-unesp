<?php
  session_start();
  include("check_login.php");
  if ($_SESSION["tipo"] != "aluno") {
    header("Location: index.php");
  }

  // include db connect class
  require_once __DIR__ . '/db_connect.php';

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
              <li><a href="logout.php">Sair</a></li>
            </ul>
          </li>
        </ul>
      </section>
    </nav>

    <main class="container">
      <br>
      <div class="row">
        <h3>Meus mapas</h3>

        <table>
          <thead>
            <tr>
              <th>Título</th>
              <th>Turma</th>
              <th>Prazo para entrega</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php while($mapa = mysqli_fetch_assoc($mapas)) { ?>
              <tr>
                <td><a href="atividade_aluno.php?id=<?php echo $mapa['id'] ?>"><?php echo $mapa['titulo'] ?></a></td>
                <td><?php echo $mapa['turma'] ?></td>
                <td><?php echo date("d/m/Y", strtotime($mapa['data_entrega'])) ?></td>
                <td><?php if ($mapa['concluido'] == 1) echo "Entregue"; else echo "Não entregue"; ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>

        <hr><h3>Minhas Turmas</h3>
        <table>
          <thead>
            <tr>
              <th>Turma</th>
              <th>Professor</th>
              <th>Opções</th>
            </tr>
          </thead>
          <tbody>
            <?php while($turma = mysqli_fetch_assoc($turmas)) { ?>
              <tr>
                <td><?php echo $turma['nome'] ?></td>
                <td><?php echo $turma['professor'] ?></td>
                <td><a onclick="remove('usuario_turma', '<?php echo $turma['id'] ?>');">Sair desta turma</a></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
        <br><a id="botaoAddTurma" class="button radius" onclick="mostrarAddTurma();">Adicionar turma</a>
      </div>

      <form action="dashboard_aluno.php" method="post" id="formAddTurma">
        <div class="row">
          <h4>Participar de nova turma</h4>
        </div>
        <div class="row">
          <div class="small-10 small-offset-1 large-6 large-offset-0 columns">
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
          <a onclick="esconderAddTurma();" class="button radius small-5 small-offset-1 large-3 large-offset-0">Cancelar</a>
          <input type="submit" name="submit" class="button radius small-5 large-3" value="Confirmar">
        </div>
      </form>
    </main>

    <script src="./js/jquery-2.1.4.min.js"></script>
    <script src="./js/foundation.min.js"></script>
    <script src="http://connect.facebook.net/en_US/all.js"></script>
    <script src="./js/dashboard_aluno.js"></script>
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
