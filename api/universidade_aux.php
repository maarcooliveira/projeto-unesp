<?php

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
