<?php
  session_start();
  $turma = isset($_POST['turma']) ? $_POST['turma'] : "";

  require_once __DIR__ . '/db_connect.php';

  $queryTurma  = "DELETE FROM turma WHERE id = {$turma}";
  $resultTurma = mysqli_query($connection, $queryTurma);

  if ($resultTurma) {
    echo True;
  }
  else {
    echo False;
  }
?>
