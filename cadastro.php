<?php
  session_start();

  // include db connect class
  require_once __DIR__ . '/api/db_connect.php';

  //TEMP: para login sem facebook
  if (isset($_POST['usuario']) && isset($_POST['senha'])) {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];
    $query  = "SELECT * FROM usuario WHERE usuario = '{$usuario}' AND senha = '{$senha}' LIMIT 1";
    $result = mysqli_query($connection, $query);

    if ($result) {
      while ($u = mysqli_fetch_assoc($result)) {
        $_SESSION["id"] = $u['id'];
        $_SESSION["tipo"] = $u['tipo'];
        $_SESSION["nome"] = $u['nome'];
        if ($_SESSION["tipo"] == "professor")
          header("Location: professor.php");
        else
          header("Location: aluno.php");
      }
    }
  }


  // $queryUni  = "SELECT * FROM universidade ORDER BY nome";
  // $universidades = mysqli_query($connection, $queryUni);
  // if (!$universidades) { die("Database query failed."); }

  // if (isset($_POST['submit'])) {

  //   $email = isset($_POST['email']) ? $_POST['email'] : "";
  //   $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : "";
  //   $nome = isset($_POST['nome']) ? $_POST['nome'] : "";
  //   $id = isset($_POST['id']) ? $_POST['id'] : "";
  //   $universidade = isset($_POST['universidade']) ? $_POST['universidade'] : null;

  //   $query  = "INSERT INTO usuario (nome, id_facebook, tipo, email, id_universidade) VALUES ('{$nome}', '{$id}', '{$tipo}', '{$email}', {$universidade})";
  //   $result = mysqli_query($connection, $query);

  //   if ($result) {
  //     $user_id = mysqli_insert_id($connection);
  //     $_SESSION["id"] = $user_id;
  //     $_SESSION["tipo"] = $tipo;
  //     if ($tipo == "professor")
  //       header("Location: professor.php");
  //     else
  //       header("Location: aluno.php");
  //   }
  // }
  // else {

  //   include("api/check_login.php");

  //   $query  = "SELECT * FROM usuario WHERE id_facebook = '" . $user_profile["id"] . "' LIMIT 1";
  //   $result = mysqli_query($connection, $query);

  //   if (!$result) {die("Database query failed.");}

  //   while($user = mysqli_fetch_assoc($result)) {
  //     $_SESSION["id"] = $user["id"];
  //     $_SESSION["tipo"] = $user["tipo"];
  //     if ($user["tipo"] == "professor")
  //       header("Location: professor.php");
  //     else if($user["tipo"] == "aluno")
  //       header("Location: aluno.php");
  //   }
  // }
?>

<!doctype html>
<html lang="pt">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Construtor de Mapa Mental">
    <title>NextEx - Novo usuário</title>
    <link rel="stylesheet" href="./css/foundation.min.css" />
    <link rel="stylesheet" href="./css/style.css" />
    <link rel="shortcut icon" href="./images/icon.png">
  </head>

  <body>
    <!-- <main>
      <br>
      <h1 class="text-center">Bem vindo!</h1>
      <br>
      <h4 class="text-center">Por favor, confirme alguns dados para continuar.</h4>
      <br><br>
      <div class="row">
        <form action="cadastro.php" method="post">

          <div class="row">
            <div class="large-6 columns">
              <label>E-mail
                <input type="text" name="email" placeholder="Entre um endereço de e-mail válido" value="<?php //echo htmlspecialchars($email); ?>" />
              </label>
            </div>
          </div>

          <div class="row">
            <div class="large-6 columns">
              <label>Eu sou</label>
              <input type="radio" name="tipo" value="aluno" id="aluno"><label for="aluno">Aluno</label>
              <input type="radio" name="tipo" value="professor" id="professor"><label for="professor">Professor</label>
            </div>
          </div>

          <div class="row" id="escolhaUniversidade">
          <div class="small-10 small-offset-1 large-6 large-offset-0 columns">
            <label>Universidade
              <select name="universidade">
                <?php
                  //while($universidade = mysqli_fetch_assoc($universidades)) {
                    //echo "<option value='{$universidade['id']}'>{$universidade['nome']}</option>";
                  //} ?>
              </select>
            </label>
          </div>
        </div>

          <input type="hidden" name="id" value="<?php //echo $id ?>"  />
          <input type="hidden" name="nome" value="<?php //echo htmlspecialchars($nome); ?>" />
          <input  type="submit" name="submit" class="button radius" value="Prosseguir"/>
        </form>
      </div>
    </main>  -->

    <main>
      <br>
      <h4 class="text-center">Usuário/senha inválidos</h4>
      <br><br>
      <a href="index.php" class="button radius small-6 large-4 small-offset-3 large-offset-4 columns">Tentar novamente</a>
    </main>

    <script src="./js/jquery-2.1.4.min.js"></script>
    <script src="./js/welcome.js"></script>
    <script src="./js/foundation.min.js"></script>
    <script src="http://connect.facebook.net/en_US/all.js"></script>
    <script>
      $(document).foundation();
    </script>
  </body>
</html>
