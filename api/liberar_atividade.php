<?php
   $tabela = isset($_POST['tabela']) ? $_POST['tabela'] : "";
   $id = isset($_POST['id']) ? $_POST['id'] : "";

   // include db connect class
   require_once __DIR__ . '/db_connect.php';

   $query  = "UPDATE atividade SET liberado = true WHERE id = {$id}";
   $result = mysqli_query($connection, $query);
   if (!$result) { die("Database query failed. " . mysqli_error ($connection)); }
?>