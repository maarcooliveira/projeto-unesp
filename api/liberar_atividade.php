<?php
  /* NextEx - Ferramenta de Avaliação
   * api/liberar_atividade.php
   *
   * Libera uma atividade para ser exibida na lista de atividades dos alunos. A partir
   * deste momento, o professor não pode mais editar sua atividade, incluindo título,
   * turma e prazo de entrega.
  */

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
