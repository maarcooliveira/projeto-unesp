<?php
  session_start();
  $turma = isset($_POST['turma']) ? $_POST['turma'] : "";

  require_once __DIR__ . '/db_connect.php';

  $queryAtividade  = "SELECT COUNT(*) as qtd FROM atividade WHERE id_turma = {$turma}";
  $resultAtividade = mysqli_query($connection, $queryAtividade);

  $qtd = mysqli_fetch_assoc($resultAtividade);

  if ($qtd['qtd'] == 0) {
    $queryUsuarioTurma  = "DELETE FROM usuario_turma WHERE id_turma = {$turma}";
    $resultUsuarioTurma = mysqli_query($connection, $queryUsuarioTurma);

    $queryTurma  = "DELETE FROM turma WHERE id = {$turma}";
    $resultTurma = mysqli_query($connection, $queryTurma);

    if ($resultUsuarioTurma && $resultTurma) {
      echo True;
    }
    else {
      echo False;
    }
  }
  else {
    echo False;
  }
?>
