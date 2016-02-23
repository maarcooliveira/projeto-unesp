<?php
  include("api/check_login.php");
  isLoggedIn();
  if (isset($_GET['id']))
    hasPermission("atividade", $_GET['id']);
  else
    hasPermission("atividade", NULL);

  include("api/turma_aux.php");
  include("api/atividade_aux.php");
  include("api/arquivo_aux.php");

  $turmas = getTurmasProfessor($_SESSION['id']);

  if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $continuacao = 1;
    $atividade_json =  getJsonAtividade($id);
    $gabarito_json =  getJsonGabarito($id);
    $atividade = getAtividade($_GET['id']);
  }
  else {
    $id = "undefined";
    $continuacao = 0;
    $atividade_json = "undefined";
    $gabarito_json = "undefined";
    $atividade = null;
  }
?>

<!doctype html>
<html lang="pt" ng-app="nextex" ng-controller="langController">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Construtor de Mapa Mental">
    <title>NextEx - {{str.nova_atividade}}</title>
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
      var _mapa = <?php echo $atividade_json; ?>;
      var _gabarito = <?php echo $gabarito_json; ?>;
      var _id = <?php echo $id; ?>;
    }
  </script>

    <div class="contain-to-grid sticky">
    <nav class="top-bar" data-topbar role="navigation" id="navbar">
      <ul class="title-area">
        <li class="name">
          <h1><a href="professor.php">NextEx <i class="fa fa-angle-right pad-l-r"></i> {{str.nova_atividade}}</a></h1>
        </li>
         <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
        <li class="toggle-topbar menu-icon"><a href="#"><span></span></a></li>
      </ul>

      <section class="top-bar-section">
        <!-- Right Nav Section -->
        <ul class="right">
          <li><a id="tb_editar" onclick="editar();"><i class="fa fa-pencil-square-o"></i> {{str.editar}}</a></li>
          <li><a id="tb_remover" onclick="removeEdge();"><i class="fa fa-chain-broken"></i> {{str.remover}}</a></li>
          <li><a id="tb_cancelar" onclick="cancelSelect();"><i class="fa fa-times"></i> {{str.cancelar}}</a></li>
          <li><a id="tb_salvar" onclick="salvar();"><i class="fa fa-check"></i> {{str.salvar}}</a></li>
          <li class="divider"></li>
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

      <form action="api/salvar_atividade.php" method="post" id="formAddMapa">

        <div id="form-p1" style="display: none;">
          <br><div class="row">
            <h3 class="text-center"><?php if ($id == "undefined") echo "{{str.nova_atividade}}"; else echo "{{str.editar_atividade}}" ?></h3>
          </div>
          <br>
          <div class="row">
            <div class="small-10 small-offset-1 large-8 large-offset-2 columns">
              <label>{{str.turma}}
                <select name="id_turma" id="id_turma">
                  <?php
                    foreach ($turmas as $turma) {
                      if ($turma['id'] == $atividade['id_turma'])
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
              <label>{{str.nome_atividade}}
                <input type="text" id="titulo" name="titulo" placeholder="{{str.nome_atividade_placeholder}}" value="<?php echo $atividade['titulo'] ?>"/>
              </label>
            </div>
          </div>

          <div class="row">
            <div class="small-5 small-offset-1 large-4 large-offset-2 columns">
              <label>{{str.peso_inicial}}
                <input type="number" id="peso_i" name="peso_i" placeholder="{{str.peso_inicial}}" value="1"/>
              </label>
            </div>

            <div class="small-5 large-4 columns end">
              <label>{{str.peso_final}}
                <input type="number" id="peso_f" name="peso_f" placeholder="{{str.peso_final}}" value="1" />
              </label>
            </div>
          </div>

          <div class="row">
            <div class="small-10 small-offset-1 large-8 large-offset-2 columns">
              <label>{{str.descricao}}
                <textarea name="descricao" id="descricao" placeholder="{{str.descricao_placeholder}}"></textarea>
              </label>
            </div>
          </div>

          <div class="row">
            <div class="small-10 small-offset-1 large-8 large-offset-2 columns">
              <label>{{str.data_limite_entrega}}
                <input type="date" id="data_entrega" name="data_entrega" <?php if (!$atividade['data_entrega'] == "") echo "value='" . date("Y-m-d", strtotime($atividade['data_entrega'])) . "'" ?>/>
              </label>
            </div>
          </div>

          <div class="row">
            <div class="small-10 small-offset-1 large-8 large-offset-2 columns">
              <label>{{str.termos_placeholder}}
                <input name="termos" id="termos" placeholder="" <?php if($id != "undefined") echo "disabled" ?>/>
              </label>
            </div>
          </div>

          <div class="row">
            <a href="professor.php" class="button radius secondary small-5 small-offset-1 large-4 large-offset-2" id="btn-cancelar">{{str.cancelar}}</a>
            <a onclick="continuar();" class="button radius small-5 large-4">{{str.continuar}}</a>
          </div>
        </div>

        <hr id="full-hr" style="visibility: hidden">
        <div id="canvas"></div>
      </form>

      <div id="myModal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
          <div class="row collapse">
          <h2 id="modalTitle">{{str.peso_da_ligacao}}</h2>
            <div class="large-12 small-12 columns">
              <input id="tb_peso" type="number" placeholder="Peso" value="0">
            </div>
            <button id="confirma_peso">{{str.confirmar}}</button>
          </div>
      </div>

    </main>

    <script src="./js/jquery-2.1.4.min.js"></script>
    <script src="./js/foundation.min.js"></script>
    <script src="./js/foundation.reveal.js"></script>
    <script src="./js/angular.min.js"></script>
    <script src="./js/lang-controller.js"></script>
    <script src="./js/dracula/raphael-min.js" type="text/javascript" charset="utf-8"></script>
    <script src="./js/dracula/dracula_graph.js"></script>
    <script src="./js/dracula/dracula_algorithms.js"></script>
    <script src="./js/dracula/dracula_graffle.js"></script>
    <script src="./js/noty/packaged/jquery.noty.packaged.min.js"></script>
    <script src="./js/bootstrap-tokenfield.min.js"></script>
    <script src="./js/variables.js"></script>
    <script src="./js/atividade.js"></script>
    <script>
      $(document).foundation();
    </script>
  </body>
</html>

<?php
  mysqli_close($connection);
?>
