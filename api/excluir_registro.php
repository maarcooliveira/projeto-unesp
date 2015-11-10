<?php
   session_start();
   $tabela = isset($_POST['tabela']) ? $_POST['tabela'] : "";
   $id = isset($_POST['id']) ? $_POST['id'] : "";

   // include db connect class
   require_once __DIR__ . '/db_connect.php';


   if ($tabela == "usuario_turma") {
      $query  = "DELETE FROM {$tabela} WHERE id_turma = {$id} AND id_usuario = {$_SESSION['id']}";
   }
   else {
      $query  = "DELETE FROM {$tabela} WHERE id = {$id}";
   }
   $result = mysqli_query($connection, $query);
   if (!$result) { die("Database query failed. " . mysqli_error ($connection)); }
?>
