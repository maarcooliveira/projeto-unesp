<?php
  include("api/check_login.php");
  redirectIfLoggedIn();
?>

<!doctype html>
<html lang="pt">
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

  <body>

    <main>

      <br>
      <div class="row">
        <img src="./images/logo.png" alt="NextEx Logo" class="small-10 small-offset-1 medium-6 medium-offset-3 columns"/>
      </div>
      <h4 class="text-center">Examination Tool</h4>
      <br><br><br>
      <!-- <div class="row">
        <a onClick="statusFacebook();" class="medium-4 medium-offset-4 columns button radius">Login</a>
      </div> -->

      <!-- TEMP: login sem facebook -->
      <form action="welcome.php" method="post" id="formLogin">
        <div class="row">
            <div class="row">
              <div class="small-10 small-offset-1 medium-6 medium-offset-3 columns">
                <label>Usuário
                  <input type="text" name="usuario" placeholder="RA" autofocus="true"/>
                </label>
              </div>
            </div>

            <div class="row">
              <div class="small-10 small-offset-1 medium-6 medium-offset-3 columns">
                <label>Senha
                  <input type="password" name="senha" placeholder="••••••••" />
                </label>
              </div>
          </div>
        </div>

        <div class="row">
          <br><input type="submit" class="button radius small-10 small-offset-1 medium-6 medium-offset-3 columns" value="Login">
        </div>
      </form>
    </main>

    <script src="./js/jquery-2.1.4.min.js"></script>
    <script src="./js/foundation.min.js"></script>
    <script src="http://connect.facebook.net/en_US/all.js"></script>
    <script src="./js/login.js"></script>
    <script>
      $(document).foundation();
    </script>
  </body>
</html>
