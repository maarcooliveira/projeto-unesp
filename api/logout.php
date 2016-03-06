<?php
  /* NextEx - Ferramenta de Avaliação
   * api/logout.php
   *
   * Remove os dados de sessão do usuário e redireciona para index.php
  */

  if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }

  if (isset($_SESSION["id"]) && isset($_SESSION["tipo"])) {
    $_SESSION["id"] = null;
    $_SESSION["tipo"] = null;
  }
  header("Location: ../index.php");
?>
