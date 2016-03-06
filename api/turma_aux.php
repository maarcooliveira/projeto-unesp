<?php
  /* NextEx - Ferramenta de Avaliação
   * api/turma_aux.php
   *
   * Contém funções da API relacionadas aos dados de turmas no banco de dados
  */
  
  // Retorna as turmas criadas por um professor
  function getTurmasProfessor($idProfessor) {
    include (__DIR__ . '/db_connect.php');
    $query = "SELECT turma.*, universidade.nome AS universidade FROM turma
              INNER JOIN universidade ON turma.id_universidade = universidade.id
              WHERE id_professor = {$idProfessor}";

    $turmas = mysqli_query($connection, $query);
    $response = Array();
    while($turma = mysqli_fetch_assoc($turmas)) {
      array_push($response, $turma);
    }
    mysqli_free_result($turmas);
    return $response;
  }

  // Retorna dados das turmas em que o aluno participa
  function getTurmasAluno($idAluno) {
    include (__DIR__ . '/db_connect.php');
    $query = "SELECT turma.*, usuario.nome AS professor FROM turma
              INNER JOIN usuario ON turma.id_professor = usuario.id
              WHERE turma.id IN (SELECT id_turma FROM usuario_turma WHERE id_usuario = {$idAluno})";

    $turmas = mysqli_query($connection, $query);
    $response = Array();
    while($turma = mysqli_fetch_assoc($turmas)) {
      array_push($response, $turma);
    }
    mysqli_free_result($turmas);
    return $response;
  }

  // Retorna turmas da universidade de um aluno nas quais ele ainda não se inscreveu
  function getTurmasAlunoNaoInscrito($idAluno) {
    include (__DIR__ . '/db_connect.php');
    $query = "SELECT turma.*, usuario.nome AS professor FROM turma
              INNER JOIN usuario ON turma.id_professor = usuario.id
              WHERE turma.id_universidade IN (SELECT id_universidade FROM usuario WHERE id = {$idAluno}) AND turma.id NOT IN (SELECT id_turma FROM usuario_turma WHERE id_usuario = {$idAluno})";

    $turmas = mysqli_query($connection, $query);
    $response = Array();
    while($turma = mysqli_fetch_assoc($turmas)) {
      array_push($response, $turma);
    }
    mysqli_free_result($turmas);
    return $response;
  }
?>
