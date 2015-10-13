<?php
  if (session_status() == PHP_SESSION_NONE) {
      session_start();
  }

  if (isset($_SESSION["id"]) && isset($_SESSION["tipo"])) {
    $_SESSION["id"] = null;
    $_SESSION["tipo"] = null;
  }
  header("Location: ../index.php");
?>
