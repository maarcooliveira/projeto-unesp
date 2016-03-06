<?php
  /* NextEx - Ferramenta de Avaliação
   * api/salvar_resolucao.php
   *
   * Salva no servidor a resolução de uma atividade enviada por um estudante, além
   * de atualizar a tabela resolucao no banco de dados
  */
  
  $dados_mapa = isset($_POST['dados_mapa']) ? $_POST['dados_mapa'] : "";
  $id_atividade = isset($_POST['id_atividade']) ? $_POST['id_atividade'] : "";
  $id_aluno = isset($_POST['id_aluno']) ? $_POST['id_aluno'] : "";
  $concluido_var = isset($_POST['concluido']) ? $_POST['concluido'] : "";
  $concluido = $concluido_var == 'true' ? 1 : 0;
  $nome_arquivo = $id_aluno . "_mapa.json";

  require_once __DIR__ . '/db_connect.php';

  if (json_decode($dados_mapa) != null) {
    // Cria o diretório de resoluções caso ele não exista
    $path = dirname( dirname(__FILE__) ) . "/atividades/" . $id_atividade . "/resolucoes";
    if (!file_exists($path)) {
      mkdir($path, 0777, true);
    }
    chmod($path, 0777);

    // Salva a resolução do aluno
    $file = fopen($path . "/" . $nome_arquivo,'w+');
    fwrite($file, $dados_mapa);
    fclose($file);
    chmod($path . "/" . $nome_arquivo, 0777);
  }

  // Cria ou atualiza uma entrada na tabela resolucao, indicando se a atividade
  // foi concluída ou não
  $date = new DateTime("NOW");
  $formattedDate = $date->format('Y-m-d H:i:s');
  $query  = "INSERT INTO resolucao (id_atividade, id_usuario, concluido) VALUES ({$id_atividade}, {$id_aluno}, {$concluido}) ON DUPLICATE KEY UPDATE concluido = {$concluido}, data_entrega = '{$formattedDate}'";
  $result = mysqli_query($connection, $query);

  if (!$result) { die("Database query failed. " . mysqli_error ($connection)); }

  if ($concluido == 1) {
    echo "enviado";
  }
  else {
    echo "salvo";
  }
?>
