<?php
  /* NextEx - Ferramenta de Avaliação
   * api/atividade_aux.php
   *
   * Contém funções da API relacionadas a requisições no banco de dados de
   * atividades e resoluções.
  */
  
  // Retorna todas as atividades de um professor
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

  // Retorna todas as atividades de um aluno
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

  // Retorna dados de uma atividade específica
  function getAtividade($id) {
    include (__DIR__ . '/db_connect.php');
    $query = "SELECT atividade.*, turma.nome AS turma FROM atividade
              INNER JOIN turma ON atividade.id_turma = turma.id
              WHERE atividade.id = {$id}";
    $atividade = mysqli_query($connection, $query);
    $response = mysqli_fetch_assoc($atividade);
    mysqli_free_result($atividade);
    return $response;
  }

  // Insere dados na tabela de resoluções quando uma nova atividade é criada
  function insertResolucoesAluno($idAluno) {
    include (__DIR__ . '/db_connect.php');
    $query = "INSERT IGNORE INTO resolucao (id_atividade, id_usuario, concluido)
              SELECT id, {$idAluno}, false FROM  atividade WHERE id_turma IN
              (SELECT id_turma FROM usuario_turma WHERE id_usuario = {$idAluno})";
    mysqli_query($connection, $query);
  }

  // Retorna dados da resolução de uma atividade e aluno específicos
  function getResolucao($idAtividade, $idAluno) {
    include (__DIR__ . '/db_connect.php');
    $query = "SELECT * FROM resolucao WHERE id_atividade = {$idAtividade}
              AND id_usuario = {$idAluno}";
    $resolucao = mysqli_query($connection, $query);
    $response = mysqli_fetch_assoc($resolucao);
    mysqli_free_result($resolucao);
    return $response;
  }

  // Retorna dados de todas as resoluções de uma atividade
  function getResolucoes($idAtividade) {
    include (__DIR__ . '/db_connect.php');
    $query = "SELECT resolucao.*, usuario.nome AS aluno FROM resolucao
              INNER JOIN usuario ON usuario.id = resolucao.id_usuario
              WHERE id_atividade = {$idAtividade} ORDER BY usuario.nome";
    $resolucoes = mysqli_query($connection, $query);

    $response = Array();
    while($resolucao = mysqli_fetch_assoc($resolucoes)) {
      array_push($response, $resolucao);
    }
    mysqli_free_result($resolucoes);
    return $response;
  }

  // Retorna a quantidade de resoluções finalizadas de uma dada atividade
  function getCountAtividadesEntregues($idAtividade) {
    include (__DIR__ . '/db_connect.php');
    $query = "SELECT COUNT(*) as qtd FROM resolucao r
              WHERE r.id_atividade = {$idAtividade} AND r.concluido = true";
    $qtd = mysqli_query($connection, $query);
    $response = mysqli_fetch_assoc($qtd);
    mysqli_free_result($qtd);
    return $response;
  }
?>
