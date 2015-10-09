<?php
  if (session_status() == PHP_SESSION_NONE) {
      session_start();
  }
  // require_once("base_facebook.php");
  // require_once("facebook.php");

  // $config = array();
  // $config['appId'] = '165128130491436';
  // $config['secret'] = 'c8954adb9933d4cfadabbcb9fb9a6d84';
  // $email = "";
  // $tipo = "";
  // $nome = "";
  // $id = "";

  // $facebook = new Facebook($config);
  // $user = $facebook->getUser();
  // if($user) {
  //   try {
  //     $user_profile = $facebook->api('/me?fields=name,email,id');
  //       $email = isset($user_profile['email']) ? $user_profile['email'] : "";
  //       $nome = isset($user_profile['name']) ? $user_profile['name'] : "";
  //       $id = isset($user_profile['id']) ? $user_profile['id'] : "";
  //   } catch (FacebookApiException $e) {
  //       $_SESSION["id"] = null;
  //       $_SESSION["tipo"] = null;
  //       header("Location: index.php");
  //   }
  // }
  // else {
  //   $_SESSION["id"] = null;
  //   $_SESSION["tipo"] = null;
  //   header("Location: index.php");
  // }

  if (isset($_SESSION["id"]) && isset($_SESSION["tipo"])) {
    if ($_SESSION["id"] != null && $_SESSION["tipo"] != null) {
      $nome = $_SESSION["nome"];
    }
    else {
        header("Location: index.php");
    }
  }
  else {
      header("Location: index.php");
  }
?>  