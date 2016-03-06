<?php
  /* NextEx - Ferramenta de Avaliação
   * 403.php
   *
   * Página de erro exibida para usuários sem permissão para acessar certo recurso
  */

  include("api/check_login.php");
  isLoggedIn();
?>

<!doctype html>
<html lang="pt" ng-app="nextex" ng-controller="langController">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Construtor de Mapa Mental">
    <title>NextEx - oops...</title>
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
          <h1><a href="index.php">NextEx <i class="fa fa-angle-right pad-l-r"></i> 403</a></h1>
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
        <br><br>
        <h2>{{str.ops}}</h2>
        <br>
        <h4>{{str.sem_permissao_acesso}}</h4>
      </div>

    </main>

    <script src="./js/jquery-2.1.4.min.js"></script>
    <script src="./js/foundation.min.js"></script>
    <script src="./js/angular.min.js"></script>
    <script src="./js/lang-controller.js"></script>
    <script>
      $(document).foundation();
    </script>
  </body>
</html>
