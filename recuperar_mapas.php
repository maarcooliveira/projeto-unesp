<?php
   $id = isset($_POST['id']) ? $_POST['id'] : "";
   $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : "";

   // $dbhost = "localhost";
   // $dbuser = "root";
   // $dbpass = "";
   // $dbname = "mind_map";
   // $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

   // if(mysqli_connect_errno()) {
   //   die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
   // }

   // $query  = "DELETE FROM {$tabela} WHERE id = {$id}";
   // $result = mysqli_query($connection, $query);
   // if (!$result) { die("Database query failed. " . mysqli_error ($connection)); }
   // if (json_decode($dados_mapa) != null) { 
   //     $path = getcwd() . "/atividades/" . $id_atividade;
   //     if (!file_exists($path)) {
   //         mkdir($path, 0755, true);
   //     }
   //   $file = fopen($path . "/" . $nome_mapa,'w+');
   //   fwrite($file, $dados_mapa);
   //   fclose($file);
   // } else {
   // }
   // return "ola" file_get_contents(getcwd() . "/atividades/" . $id . "/" . $tipo . ".json");

?>