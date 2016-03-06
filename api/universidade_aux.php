<?php
  /* NextEx - Ferramenta de Avaliação
   * api/universidade_aux.php
   *
   * Contém funções da API relacionadas à tabela de universidade no banco de dados
  */
  
  // Retorna as universidades cadastradas no banco
  function getUniversidades() {
    include(__DIR__ . '/db_connect.php');
    $queryUni  = "SELECT * FROM universidade ORDER BY nome";
    $universidades = mysqli_query($connection, $queryUni);
    $response = Array();
    while($uni = mysqli_fetch_assoc($universidades)) {
      array_push($response, $uni);
    }
    mysqli_free_result($universidades);
    return $response;
  }
?>
