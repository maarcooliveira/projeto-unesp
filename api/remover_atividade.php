<?php
  /* NextEx - Ferramenta de Avaliação
   * api/remover_atividade.php
   *
   * Deleta uma atividade de um professor. Os dados da atividade, o gabarito, as
   * resoluções enviadas pelos alunos e os seus resultados também são removidos
   * permanentemente
  */

  session_start();
  $atividade = isset($_POST['atividade']) ? $_POST['atividade'] : null;

  if (!$atividade) {
    echo False;
    return;
  }

  require_once __DIR__ . '/db_connect.php';

  // Deleta recursivamente os arquivos relacionados à atividade
  function deleteDirectory($dirPath) {
    if (is_dir($dirPath)) {
      $objects = scandir($dirPath);
      foreach ($objects as $object) {
        if ($object != "." && $object !="..") {
          if (filetype($dirPath . DIRECTORY_SEPARATOR . $object) == "dir") {
            deleteDirectory($dirPath . DIRECTORY_SEPARATOR . $object);
          } else {
            unlink($dirPath . DIRECTORY_SEPARATOR . $object);
          }
        }
      }
      reset($objects);
      rmdir($dirPath);
    }
  }

  // Remove dados das resoluções enviadas para esta atividade
  $queryRes  = "DELETE FROM resolucao WHERE id_atividade = {$atividade}";
  $resultRes = mysqli_query($connection, $queryRes);

  // Remove dados da atividade do banco de dados
  $queryAtv  = "DELETE FROM atividade WHERE id = {$atividade}";
  $resultAtv = mysqli_query($connection, $queryAtv);

  $path = dirname( dirname(__FILE__) ) . "/atividades/" . $atividade;
  deleteDirectory($path);

  if ($queryRes && $queryAtv) {
    echo True;
  }
  else {
    echo False;
  }
?>
