<?php

  function getAtividadesProfessor($idProfessor) {
    include (__DIR__ . '/db_connect.php');
    $query = "SELECT atividade.*, turma.nome AS turma FROM atividade
              INNER JOIN turma ON atividade.id_turma = turma.id
              WHERE id_turma IN (SELECT id FROM turma WHERE id_professor = {$idProfessor})";

    $atividades = mysqli_query($connection, $query);
    $response = Array();
    while($atividade = mysqli_fetch_assoc($atividades)) {
      array_push($response, $atividade);
    }
    mysqli_free_result($atividades);
    return $response;
  }

  function getAtividadesAluno($idAluno) {
    include (__DIR__ . '/db_connect.php');
    $query = "SELECT atividade.*, turma.nome AS turma, resolucao.concluido AS concluido FROM atividade
             INNER JOIN turma ON atividade.id_turma = turma.id
             INNER JOIN resolucao ON (atividade.id = resolucao.id_atividade AND resolucao.id_usuario = {$idAluno})
             WHERE id_turma IN (SELECT id_turma FROM usuario_turma WHERE id_usuario = {$idAluno})
             AND liberado = true";

    $atividades = mysqli_query($connection, $query);
    $response = Array();
    while($atividade = mysqli_fetch_assoc($atividades)) {
      array_push($response, $atividade);
    }
    mysqli_free_result($atividades);
    return $response;
  }

  function getAtividade($id) {
    include (__DIR__ . '/db_connect.php');
    $query = "SELECT * FROM atividade WHERE id = {$id}";
    $atividade = mysqli_query($connection, $query);
    $response = mysqli_fetch_assoc($atividade);
    mysqli_free_result($atividade);
    return $response;
  }

  function insertResolucoesAluno($idAluno) {
    include (__DIR__ . '/db_connect.php');
    $query = "INSERT IGNORE INTO resolucao (id_atividade, id_usuario, concluido)
              SELECT id, {$idAluno}, false FROM  atividade WHERE id_turma IN
              (SELECT id_turma FROM usuario_turma WHERE id_usuario = {$idAluno})";
    mysqli_query($connection, $query);
  }

?>
