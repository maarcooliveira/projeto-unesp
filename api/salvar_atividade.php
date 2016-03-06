<?php
  /* NextEx - Ferramenta de Avaliação
   * api/salvar_atividade.php
   *
   * Salva no servidor os arquivos da atividade (mapa.json) e do gabarito (gabarito.json).
  */

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

  // Insere uma nova atividade no banco de dados, além de uma entrada na tabela
  // resolucao para cada estudante cadastrado na turma da atividade
  if (!$continuacao) {
    $queryAtividade  = "INSERT INTO atividade (id_turma, titulo, data_entrega, liberado)
                        VALUES ({$id_turma}, '{$titulo}', '{$data_entrega}', false)";
    $resultAtividade = mysqli_query($connection, $queryAtividade);

    if (!$resultAtividade) {
      die("Database query failed. " . mysqli_error ($connection));
    }
    $id_atividade = mysqli_insert_id($connection);

    $queryResolucao = "INSERT INTO resolucao (id_usuario, id_atividade, concluido)
                       SELECT id, {$id_atividade}, false FROM usuario WHERE id IN
                       (SELECT id_usuario FROM usuario_turma WHERE id_turma = {$id_turma})";

    $resultResolucao = mysqli_query($connection, $queryResolucao);

    if (!$resultResolucao) {
      echo "erro";
      return;
    }
  }
  // A atividade foi editada, portando é feito apenas um UPDATE na tabela atividade
  else {
    $query = "UPDATE atividade SET id_turma = {$id_turma}, titulo = '{$titulo}',
              data_entrega = '{$data_entrega}', liberado = false
              WHERE id = {$id_atividade}";

    $result = mysqli_query($connection, $query);

    if (!$result) {
      echo "erro";
      return;
    }
  }


  if (json_decode($dados_mapa) != null) {
    // Cria o diretório para arquivos da atividade
    $path = dirname( dirname(__FILE__) ) . "/atividades/" . $id_atividade;
    if (!file_exists($path)) {
      mkdir($path, 0777, true);
    }

    // Cria o diretório para arquivos de resolução
    $pathRes = dirname( dirname(__FILE__) ) . "/atividades/" . $id_atividade . "/resolucoes";
    if (!file_exists($pathRes)) {
      mkdir($pathRes, 0777, true);
      chmod($path, 0777);
    }

    // Salva a atividade
    $file = fopen($path . "/" . $nome_mapa,'w+');
    fwrite($file, $dados_mapa);
    fclose($file);
    chmod($path . "/" . $nome_mapa, 0777);
  }

  if (json_decode($dados_gabarito) != null) {
    // Cria o diretório para arquivos da atividade
    $path = dirname( dirname(__FILE__) ) . "/atividades/" . $id_atividade;
    if (!file_exists($path)) {
      mkdir($path, 0777, true);
    }

    // Salva o gabarito
    $file = fopen($path . "/" . $nome_gabarito,'w+');
    fwrite($file, $dados_gabarito);
    fclose($file);
    chmod($path . "/" . $nome_gabarito, 0777);
  }

  echo "salvo";
?>
