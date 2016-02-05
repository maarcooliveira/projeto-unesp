<?php
  if (session_status() == PHP_SESSION_NONE) {
      session_start();
  }

  function isLoggedIn() {
    if (isset($_SESSION["id"]) && isset($_SESSION["tipo"])) {
      if ($_SESSION["id"] != null && $_SESSION["tipo"] != null) {
        $nome = $_SESSION["nome"];
        return True;
      }
    }
    header("Location: index.php");
  }

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
        if ($userProfile == "professor" && canProfessorEdit($pageId, $userId))
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

  function canProfessorEdit($activityId, $professorId) {
    //TODO: conferir se a atividade é do professor
    return True;
  }

  function canStudentAccess($activityId, $studentId) {
    //TODO: conferir se o estudante está na turma e se a atividade está liberada
    return True;
  }

  function canProfessorAccessResults($activityId, $professorId) {
    //TODO: conferir se a atividade já foi liberada e o professor é o dono da atividade
    return True;
  }

  // Não utilizado;
  function fbLogin() {
    require_once("base_facebook.php");
    require_once("facebook.php");

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
