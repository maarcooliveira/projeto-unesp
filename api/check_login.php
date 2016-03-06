<?php
  /* NextEx - Ferramenta de Avaliação
   * api/check_login.php
   *
   * Contém funções que verificam o login de um usuário e conferem se o usuário
   * tem permissão para acessar uma página específica
  */
  
  if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }

  // Redireciona de index.php para professor.php ou aluno.php dependendo do usuário
  function redirectIfLoggedIn() {
    if (isLoggedInHelper()) {
      if ($_SESSION["tipo"] == "professor")
        header("Location: professor.php");
      else if ($_SESSION["tipo"] == "aluno")
        header("Location: aluno.php");
    }
  }

  // Confere se há um usuário logado; redireciona para index.php caso negativo
  function isLoggedIn() {
    if (isLoggedInHelper())
      return True;
    else
      header("Location: index.php");
  }

  // Confere dados de sessão para verificar se há um usuário logado
  function isLoggedInHelper() {
    if (isset($_SESSION["id"]) && isset($_SESSION["tipo"]))
      if ($_SESSION["id"] != null && $_SESSION["tipo"] != null)
        return True;
    return False;
  }

  // Chama funções específicas para conferir permissão de acesso a cada tipo de
  // página. Redireciona para erro 403 caso o usuário não tenha permissão.
  function hasPermission($pageType, $pageId) {
    if (isset($_SESSION["tipo"]) && isset($_SESSION["id"])) {
      $userProfile = $_SESSION["tipo"];
      $userId = $_SESSION["id"];
    }
    else {
      return False;
    }

    switch ($pageType) {
      case 'professor':
        if ($userProfile == "professor")
          return True;
        else
          header("Location: 403.php");
        break;
      case 'aluno':
        if ($userProfile == "aluno")
          return True;
        else
          header("Location: 403.php");
        break;
      case 'atividade':
        if ($userProfile == "professor" && ($pageId == NULL || canProfessorEdit($pageId, $userId)))
          return True;
        else
          header("Location: 403.php");
        break;
      case 'avaliacao':
        if ($userProfile == "aluno" && canStudentAccess($pageId, $userId))
          return True;
        else
          header("Location: 403.php");
        break;
      case 'resultados':
        if ($userProfile == "professor" && canProfessorAccessResults($pageId, $userId))
          return True;
        else
          header("Location: 403.php");
        break;
      default:
        header("Location: 403.php");
        break;
    }
  }

  // Verifica se um professor pode editar uma atividade
  function canProfessorEdit($activityId, $professorId) {
    require_once __DIR__ . '/db_connect.php';
    $queryAtividade = "SELECT * FROM atividade WHERE id = {$activityId}";
    $atividades = mysqli_query($connection, $queryAtividade);
    $atividade = mysqli_fetch_assoc($atividades);

    if ($atividade && !$atividade['liberado']) {
      $idTurma = $atividade['id_turma'];
      $queryTurma = "SELECT * FROM turma WHERE id = {$idTurma}";
      $turmas = mysqli_query($connection, $queryTurma);
      $turma = mysqli_fetch_assoc($turmas);
      if ($turma && $turma['id_professor'] == $professorId) {
        return True;
      }
    }
    return False;
  }

  // Verifica se um aluno pode acessar certa atividade
  function canStudentAccess($activityId, $studentId) {
    require_once __DIR__ . '/db_connect.php';
    $queryAtividade = "SELECT * FROM atividade WHERE id = {$activityId}";
    $atividades = mysqli_query($connection, $queryAtividade);
    $atividade = mysqli_fetch_assoc($atividades);

    if ($atividade && $atividade['liberado']) {
      $idTurma = $atividade['id_turma'];
      $queryUsuarioTurma = "SELECT * FROM usuario_turma WHERE id_usuario = {$studentId} AND id_turma = {$idTurma}";
      $listaUsuarioTurma = mysqli_query($connection, $queryUsuarioTurma);
      $usuarioTurma = mysqli_fetch_assoc($listaUsuarioTurma);
      if ($usuarioTurma) {
        return True;
      }
    }
    return False;
  }

  // Verifica se um professor pode acessar a página de resultados de uma atividade
  function canProfessorAccessResults($activityId, $professorId) {
    require_once __DIR__ . '/db_connect.php';
    $queryAtividade = "SELECT * FROM atividade WHERE id = {$activityId}";
    $atividades = mysqli_query($connection, $queryAtividade);
    $atividade = mysqli_fetch_assoc($atividades);

    if ($atividade && $atividade['liberado']) {
      $idTurma = $atividade['id_turma'];
      $queryTurma = "SELECT * FROM turma WHERE id = {$idTurma}";
      $turmas = mysqli_query($connection, $queryTurma);
      $turma = mysqli_fetch_assoc($turmas);
      if ($turma && $turma['id_professor'] == $professorId) {
        return True;
      }
    }
    return False;
  }

  // Realiza login pelo Facebook; Não é mais utilizado devido a mudanças no escopo
  function fbLogin() {
    require_once("facebook/base_facebook.php");
    require_once("facebook/facebook.php");

    $config = array();
    $config['appId'] = '165128130491436';
    $config['secret'] = 'c8954adb9933d4cfadabbcb9fb9a6d84';
    $email = "";
    $tipo = "";
    $nome = "";
    $id = "";

    $facebook = new Facebook($config);
    $user = $facebook->getUser();
    if($user) {
      try {
        $user_profile = $facebook->api('/me?fields=name,email,id');
          $email = isset($user_profile['email']) ? $user_profile['email'] : "";
          $nome = isset($user_profile['name']) ? $user_profile['name'] : "";
          $id = isset($user_profile['id']) ? $user_profile['id'] : "";
      } catch (FacebookApiException $e) {
          $_SESSION["id"] = null;
          $_SESSION["tipo"] = null;
          header("Location: index.php");
      }
    }
    else {
      $_SESSION["id"] = null;
      $_SESSION["tipo"] = null;
      header("Location: index.php");
    }
  }
?>
