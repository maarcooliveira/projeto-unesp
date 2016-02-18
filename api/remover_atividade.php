<?php
  session_start();
  $atividade = isset($_POST['atividade']) ? $_POST['atividade'] : null;

  if (!$atividade) {
    echo False;
    return;
  }

  require_once __DIR__ . '/db_connect.php';

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

  $queryRes  = "DELETE FROM resolucao WHERE id_atividade = {$atividade}";
  $resultRes = mysqli_query($connection, $queryRes);

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
