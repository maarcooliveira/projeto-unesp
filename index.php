<?php
  include("api/check_login.php");
  redirectIfLoggedIn();
?>

<!doctype html>
<html lang="pt" ng-app="nextex" ng-controller="langController">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Construtor de Mapa Mental">
    <title>NextEx</title>
    <link rel="stylesheet" href="./css/foundation.min.css" />
    <link rel="stylesheet" href="./css/style.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="shortcut icon" href="./images/icon.png">
  </head>

  <body class="grey-bg">

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
        </ul>
      </section>
    </nav>
    </div>

    <main>
      <br>
      <div class="row">
        <img src="./images/logo.png" alt="NextEx Logo" class="small-10 small-offset-1 medium-6 medium-offset-3 columns"/>
      </div>
      <h4 class="text-center">{{str.ferramenta_de_avaliacao}}</h4>
      <br><br>

      <!-- Login COM facebook -->
      <!-- <div class="row">
        <a onClick="statusFacebook();" class="button radius small-10 small-offset-1 medium-6 medium-offset-3 columns">Login</a>
      </div> -->

      <!-- Login SEM facebook -->
      <form action="cadastro.php" method="post" id="formLogin">
        <div class="row">
          <div class="medium-10 medium-offset-1 columns">

            <div class="row">
              <div class="small-10 small-offset-1 medium-6 medium-offset-3 columns">
                <label>{{str.usuario}}
                  <input type="text" name="usuario" placeholder="{{str.usuario_placeholder}}" autofocus="true"/>
                </label>
              </div>
            </div>

            <div class="row">
              <div class="small-10 small-offset-1 medium-6 medium-offset-3 columns">
                <label>{{str.senha}}
                  <input type="password" name="senha" placeholder="••••••••" />
                </label>
              </div>
            </div>

          </div>
        </div>

        <div class="row">
          <div class="medium-10 medium-offset-1 columns">
            <br><input type="submit" class="button radius small-10 small-offset-1 medium-6 medium-offset-3 columns" value="{{str.login}}">
          </div>
        </div>
      </form>
    </main>

    <script src="./js/jquery-2.1.4.min.js"></script>
    <script src="./js/foundation.min.js"></script>
    <script src="./js/angular.min.js"></script>
    <script src="./js/lang-controller.js"></script>
    <!-- <script src="http://connect.facebook.net/en_US/all.js"></script> -->
    <script src="./js/login.js"></script>
    <script>
      $(document).foundation();
    </script>
  </body>
</html>
