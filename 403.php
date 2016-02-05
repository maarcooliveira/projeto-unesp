<?php
  include("api/check_login.php");
  isLoggedIn();
?>

<!doctype html>
<html lang="pt">
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

    <nav class="top-bar" data-topbar role="navigation">
      <ul class="title-area">
        <li class="name">
          <h1><a href="index.php"><i class="fa fa-bars"></i> NextEx</a></h1>
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
        <br><br>
        <h2>Oooops...</h2>
        <br>
        <h4>Parece que você não tem permissão para acessar esta página.</h4>
      </div>

    </main>

    <script src="./js/jquery-2.1.4.min.js"></script>
    <script src="./js/foundation.min.js"></script>
    <script src="http://connect.facebook.net/en_US/all.js"></script>
    <script>
      $(document).foundation();
    </script>
  </body>
</html>
