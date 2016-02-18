<?php
  session_start();
  $turma = isset($_POST['turma']) ? $_POST['turma'] : "";

  require_once __DIR__ . '/db_connect.php';

  $query  = "DELETE FROM usuario_turma WHERE id_turma = {$turma} AND id_usuario = {$_SESSION['id']}";
  $result = mysqli_query($connection, $query);

  if ($result) {
    echo True;
  }
  else {
    echo False;
  }
?>
