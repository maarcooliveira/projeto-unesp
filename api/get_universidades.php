<?php
// array for JSON response
$response = array();
 
// include db connect class
require_once __DIR__ . '/db_connect.php';
 
    $queryUni  = "SELECT * FROM universidade ORDER BY nome";
    $universidades = mysqli_query($connection, $queryUni);
    
  
    if (!empty($universidades)) {
        if (mysqli_num_rows($universidades) > 0) {

          $response["universidades"] = array();
          $response["success"] = 1;

          while($uni = mysqli_fetch_assoc($universidades)) { 
              $universidade = array();
              $universidade["id"] = $uni["id"];
              $universidade["nome"] = $uni["nome"];
              array_push($response["universidades"], $universidade);
          } 
          echo json_encode($response);

        } else {
            $response["success"] = 0;
            $response["message"] = "Não há universidades cadastradas";
            echo json_encode($response);
        }
    } else {
        $response["success"] = 0;
        $response["message"] = "Não há universidades cadastradas";
        echo json_encode($response);
    }
?>