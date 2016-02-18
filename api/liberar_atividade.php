<?php
  $id = isset($_POST['id']) ? $_POST['id'] : "";

  require_once __DIR__ . '/db_connect.php';

  $query  = "UPDATE atividade SET liberado = true WHERE id = {$id}";
  $result = mysqli_query($connection, $query);

  if ($result) {
    return True;
  }
  else {
    return False;
  }
?>
