<?php
   $dados_mapa = isset($_POST['dados_mapa']) ? $_POST['dados_mapa'] : "";
   $dados_gabarito = isset($_POST['dados_gabarito']) ? $_POST['dados_gabarito'] : "";
   $id_turma = isset($_POST['id_turma']) ? $_POST['id_turma'] : "";
   $data_entrega = isset($_POST['data_entrega']) ? $_POST['data_entrega'] : "";
   $titulo = isset($_POST['titulo']) ? $_POST['titulo'] : "";
   $continuacao = isset($_POST['continuacao']) ? $_POST['continuacao'] : "";
   $id_atividade = isset($_POST['id_atividade']) ? $_POST['id_atividade'] : "";

  require_once __DIR__ . '/db_connect.php';

   $nome_mapa = "mapa.json";
   $nome_gabarito = "gabarito.json";

   if (!$continuacao) {
    $queryAtividade  = "INSERT INTO atividade (id_turma, titulo, data_entrega, liberado) VALUES ({$id_turma}, '{$titulo}', '{$data_entrega}', false)";
    $resultAtividade = mysqli_query($connection, $queryAtividade);

    if (!$resultAtividade) { die("Database query failed. " . mysqli_error ($connection)); }
    $id_atividade = mysqli_insert_id($connection);

    $queryInsertResolucao = "INSERT INTO resolucao (id_usuario, id_atividade, concluido) SELECT id, {$id_atividade}, false FROM usuario WHERE id IN (SELECT id_usuario FROM usuario_turma WHERE id_turma = {$id_turma})";

    $resultResolucao = mysqli_query($connection, $queryInsertResolucao);

    if (!$resultResolucao) { die("Database query failed. " . mysqli_error ($connection)); }
   }
   else {
     $query  = "UPDATE atividade SET id_turma = {$id_turma}, titulo = '{$titulo}', data_entrega = '{$data_entrega}', liberado = false WHERE id = {$id_atividade}";
     $result = mysqli_query($connection, $query);

     if (!$result) { die("Database query failed. " . mysqli_error ($connection)); }
   }


   if (json_decode($dados_mapa) != null) {
   	 $path = dirname( dirname(__FILE__) ) . "/atividades/" . $id_atividade;
   	 if (!file_exists($path)) {
   	     mkdir($path, 0777, true);
   	 }

     $pathRes = dirname( dirname(__FILE__) ) . "/atividades/" . $id_atividade . "/resolucoes";
     if (!file_exists($pathRes)) {
      mkdir($pathRes, 0777, true);
      chmod($path, 0777);
     }

     $file = fopen($path . "/" . $nome_mapa,'w+');
     fwrite($file, $dados_mapa);
     fclose($file);
     chmod($path . "/" . $nome_mapa, 0777);
   } else {
   }

   if (json_decode($dados_gabarito) != null) {
   	 $path = dirname( dirname(__FILE__) ) . "/atividades/" . $id_atividade;
   	 if (!file_exists($path)) {
   	     mkdir($path, 0777, true);
   	 }
     $file = fopen($path . "/" . $nome_gabarito,'w+');
     fwrite($file, $dados_gabarito);
     fclose($file);
     chmod($path . "/" . $nome_gabarito, 0777);
   } else {
   }
   header("Location: ../professor.php");
?>
