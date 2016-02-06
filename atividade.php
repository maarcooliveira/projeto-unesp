<?php
  include("api/check_login.php");
  isLoggedIn();
  if (isset($_GET['id']))
    hasPermission("atividade", $_GET['id']);
  else
    hasPermission("atividade", NULL);

  // include db connect class
  require_once __DIR__ . '/api/db_connect.php';

  // if (isset($_POST['submit'])) {
  //
  //   $turma = isset($_POST['turma']) ? $_POST['turma'] : "";
  //   $titulo = isset($_POST['titulo']) ? $_POST['titulo'] : "";
  //   $entrega = isset($_POST['entrega']) ? $_POST['entrega'] : "";
  //
  //   $query  = "INSERT INTO atividade (id_turma, titulo, data_entrega) VALUES ({$turma}, '{$titulo}', '{$entrega}')";
  //   $result = mysqli_query($connection, $query);
  //   echo $query;
  //   if (!$result) { die("Database query failed. " . mysqli_error ($connection)); }
  // }

  $id = isset($_GET['id']) ? $_GET['id'] : "undefined";
  $continuacao = 0;
  $mapa_txt_php = "undefined";
  $gabarito_txt_php = "undefined";

  if($id !== "undefined") {
    $mapa_txt_php =  file_get_contents(getcwd() . "/atividades/" . $id . "/mapa.json");
    $gabarito_txt_php =  file_get_contents(getcwd() . "/atividades/" . $id . "/gabarito.json");
    $continuacao = 1;
    $queryAtividade = "SELECT * FROM atividade WHERE id = {$id}";
    $atividade = mysqli_query($connection, $queryAtividade);
    if (!$atividade) { die("Database query failed. " . mysqli_error ($connection)); }
    $a = mysqli_fetch_assoc($atividade);
  }
  else {
    $a['titulo'] = "";
    $a['id_turma'] = "";
    $a['data_entrega'] = "";
  }

  $queryTurmas = "SELECT * FROM turma WHERE id_professor = {$_SESSION['id']}";
  $turmas = mysqli_query($connection, $queryTurmas);
  if (!$turmas) { die("Database query failed."); }
?>

<!doctype html>
<html lang="pt">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Construtor de Mapa Mental">
    <title>NextEx - Nova Atividade</title>
    <link rel="stylesheet" href="./css/foundation.min.css" />
    <link rel="stylesheet" href="./css/style.css" />
    <link rel="stylesheet" href="./css/bootstrap-tokenfield.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="shortcut icon" href="./images/icon.png">
  </head>

  <body>
  <script type="text/javascript">
    var continuacao = <?php echo ($continuacao); ?>;
    if (continuacao) {
      var mapa_txt = <?php echo $mapa_txt_php; ?>;
      var gabarito_txt = <?php echo $gabarito_txt_php; ?>;
      var id_atividade = <?php echo $id; ?>;
    }

  </script>

    <!-- <div class="contain-to-grid sticky"> -->
    <nav class="top-bar" data-topbar role="navigation" id="navbar">
      <ul class="title-area">
        <li class="name">
          <h1><a href="professor.php"><i class="fa fa-arrow-left"></i> NextEx</a></h1>
        </li>
         <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
        <li class="toggle-topbar menu-icon"><a href="#"><span></span></a></li>
      </ul>

      <section class="top-bar-section">
        <!-- Right Nav Section -->
        <ul class="right">
          <li><a id="tb_editar" onclick="editar();"><i class="fa fa-pencil-square-o"></i> Editar</a></li>
          <li><a id="tb_remover" onclick="removeEdge();"><i class="fa fa-chain-broken"></i> Remover</a></li>
          <li><a id="tb_cancelar" onclick="cancelSelect();"><i class="fa fa-times"></i> Cancelar</a></li>
          <li><a id="tb_salvar" onclick="salvar();"><i class="fa fa-check"></i> Concluído</a></li>
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

      <form action="api/salvar_arquivo.php" method="post" id="formAddMapa">

        <div id="form-p1" style="display: none;">
          <br><div class="row">
            <h3 class="text-center"><?php if($id == "undefined") echo "Nova Atividade"; else echo "Editar: {$a['titulo']}"; ?></h3>
          </div>
          <br>
          <div class="row">
            <div class="small-10 small-offset-1 large-8 large-offset-2 columns">
              <label>Turma
                <select name="id_turma">
                  <?php
                    while($turma = mysqli_fetch_assoc($turmas)) {
                      if ($turma['id'] == $a['id_turma'])
                        $selected = "selected";
                      else
                        $selected = "";
                      echo "<option value='{$turma['id']}' " . $selected . ">{$turma['nome']}</option>";
                    } ?>
                </select>
              </label>
            </div>
          </div>

          <div class="row">
            <div class="small-10 small-offset-1 large-8 large-offset-2 columns">
              <label>Título
                <input type="text" id="titulo" name="titulo" placeholder="Insira um nome para a atividade" value="<?php echo $a['titulo'] ?>"/>
              </label>
            </div>
          </div>

          <div class="row">
            <div class="small-5 small-offset-1 large-4 large-offset-2 columns">
              <label>Peso inicial
                <input type="number" id="peso_i" name="peso_i" placeholder="Peso inicial" value="1"/>
              </label>
            </div>

            <div class="small-5 large-4 columns end">
              <label>Peso final
                <input type="number" id="peso_f" name="peso_f" placeholder="Peso final" value="1" />
              </label>
            </div>
          </div>

          <div class="row">
            <div class="small-10 small-offset-1 large-8 large-offset-2 columns">
              <label>Descrição
                <textarea name="descricao" id="descricao" placeholder="Descreva como a atividade deve ser realizada"></textarea>
              </label>
            </div>
          </div>

          <div class="row">
            <div class="small-10 small-offset-1 large-8 large-offset-2 columns">
              <label>Data de entrega
                <input type="date" name="data_entrega" <?php if (!$a['data_entrega'] == "") echo "value='" . date("Y-m-d", strtotime($a['data_entrega'])) . "'" ?>/>
              </label>
            </div>
          </div>

          <div class="row">
            <div class="small-10 small-offset-1 large-8 large-offset-2 columns">
              <label>Termos
                <input name="termos" id="termos" placeholder="Insira os termos separados por vírgula" <?php if($id !== "undefined") echo "disabled" ?>/>
              </label>
            </div>
          </div>

          <div class="row">
            <a href="professor.php" class="button radius secondary small-5 small-offset-1 large-4 large-offset-2" id="btn-cancelar">Cancelar</a>
            <a onclick="continuar();" class="button radius small-5 large-4">Continuar</a>

          </div>
        </div>
        <hr id="full-hr" style="visibility: hidden">
        <input type="hidden" name="dados_mapa" id="dados_mapa">
        <input type="hidden" name="dados_gabarito" id="dados_gabarito">
        <input type="hidden" name="continuacao" id="continuacao">
        <input type="hidden" name="id_atividade" id="id_atividade">


        <div id="canvas"></div>
      </form>

      <div id="myModal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
          <div class="row collapse">
          <h2 id="modalTitle">Peso da ligação</h2>
            <div class="large-12 small-12 columns">
              <input id="tb_peso" type="number" placeholder="Peso" value="0">
            </div>
            <button id="confirma_peso">Confirmar</button>
          </div>
      </div>

    </main>

    <script src="./js/jquery-2.1.4.min.js"></script>
    <script src="./js/foundation.min.js"></script>
    <script src="./js/foundation.reveal.js"></script>
    <script src="./js/raphael-min.js" type="text/javascript" charset="utf-8"></script>
    <script src="./js/dracula_graph.js"></script>
    <script src="./js/dracula_algorithms.js"></script>
    <script src="./js/dracula_graffle.js"></script>
    <script src="./js/noty/packaged/jquery.noty.packaged.min.js"></script>
    <script src="./js/bootstrap-tokenfield.min.js"></script>
    <script src="./js/atividade.js"></script>
    <script>
      $(document).foundation();
    </script>
  </body>
</html>

<?php
  mysqli_free_result($turmas);
  mysqli_close($connection);
?>
