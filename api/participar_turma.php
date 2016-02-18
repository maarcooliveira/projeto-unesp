<?php
  require_once __DIR__ . '/db_connect.php';

  if (isset($_POST["usuario"]) && isset($_POST["turma"])) {
      $usuario = $_POST['usuario'];
      $turma = $_POST["turma"];

      $query  = "INSERT INTO usuario_turma (id_usuario, id_turma) VALUES ({$usuario}, {$turma})";
      $result = mysqli_query($connection, $query);

      if ($result) {
        echo True;
      } else {
        echo False;
      }
  } else {
    echo False;
  }
?>
