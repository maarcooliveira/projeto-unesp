<?php
  session_start();
  include("api/check_login.php");
  if ($_SESSION["tipo"] != "professor") {
    header("Location: index.php");
  }

  // include db connect class
  require_once __DIR__ . '/api/db_connect.php';

  if (isset($_POST['submit'])) {

    $universidade = isset($_POST['universidade']) ? $_POST['universidade'] : "";
    $turma = isset($_POST['turma']) ? $_POST['turma'] : "";
    $professor = isset($_SESSION['id']) ? $_SESSION['id'] : "";

    $query  = "INSERT INTO turma (nome, id_universidade, id_professor) VALUES ('{$turma}', {$universidade}, {$professor})";
    $result = mysqli_query($connection, $query);
    if (!$result) { die("Database query failed. " . mysqli_error ($connection)); }
    // if ($result) {

    // }
  }

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
        <h3>Mapas</h3>

        <table>
          <thead>
            <tr>
              <th>Título</th>
              <th>Turma</th>
              <th>Prazo final</th>
              <th>Status</th>
              <th>Opções</th>
            </tr>
          </thead>
          <tbody>
            <?php while($mapa = mysqli_fetch_assoc($mapas)) {
                $link = "novo_mapa.php?id=" . $mapa['id'];
                if ($mapa['liberado']) {
                  $link = "atividade_professor.php?id=" . $mapa['id'];
                }
            ?>
              <tr>

                <td><a href="<?php echo $link ?>"><?php echo $mapa['titulo'] ?></a></td>
                <td><?php echo $mapa['turma'] ?></td>
                <td><?php echo date("d/m/Y", strtotime($mapa['data_entrega'])) ?></td>
                <td>
                    <?php if ($mapa['liberado']) { echo 'Liberado'; } else {  ?>
                      <a onclick="liberar('<?php echo $mapa['id'] ?>');">Liberar</a>
                    <?php } ?></td>
                <td><a onclick="remove('atividade', '<?php echo $mapa['id'] ?>');">Excluir</a></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
        <br><a class="button radius" href="novo_mapa.php">Novo mapa</a>

        <hr><h3>Minhas Turmas</h3>
        <table>
          <thead>
            <tr>
              <th>Turma</th>
              <th>Universidade</th>
              <!-- <th>Criado em</th> -->
              <th>Opções</th>
            </tr>
          </thead>
          <tbody>
            <?php while($turma = mysqli_fetch_assoc($turmas)) { ?>
              <tr>
                <td><?php echo $turma['nome'] ?></td>
                <td><?php echo $turma['universidade'] ?></td>
                <!-- <td><?php //echo $turma['data_criacao'] ?></td> -->
                <td><a onclick="remove('turma', '<?php echo $turma['id'] ?>');">Excluir</a></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
        <br><a class="button radius" onclick="mostrarAddTurma();">Nova turma</a>
      </div>

      <form action="dashboard_professor.php" method="post" id="formAddTurma">
        <br><br>
        <div class="row">
          <div class="small-10 small-offset-1 large-6 large-offset-0 columns">
            <label>Universidade
              <select name="universidade">
                <?php
                  while($universidade = mysqli_fetch_assoc($universidades)) {
                    echo "<option value='{$universidade['id']}'>{$universidade['nome']}</option>";
                  } ?>
              </select>
            </label>
          </div>
        </div>

        <div class="row">
          <div class="small-10 small-offset-1 large-6 large-offset-0 columns">
            <label>Nome da turma
              <input type="text" name="turma" placeholder="Ex: Algoritmos e Programação I" />
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
    <script src="./js/dashboard_professor.js"></script>
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
