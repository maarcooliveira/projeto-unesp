<?php
// array for JSON response
$response = array();
 
// include db connect class
require_once __DIR__ . '/db_connect.php';
 
// check for post data
if (isset($_GET["user"])) {
    $user = $_GET['user'];

    $queryOutrasTurmas = "SELECT * FROM turma WHERE id_universidade IN (SELECT id_universidade FROM usuario WHERE id = {$user}) AND id NOT IN (SELECT id_turma FROM usuario_turma WHERE id_usuario = {$user})";

  $turmas = mysqli_query($connection, $queryOutrasTurmas);
  
    if (!empty($turmas)) {
        // check for empty result
        if (mysqli_num_rows($turmas) > 0) {


          $response["turmas"] = array();
          $response["success"] = 1;

          while($tm = mysqli_fetch_assoc($turmas)) { 
              $turma = array();
              $turma["id"] = $tm["id"];
              $turma["nome"] = $tm["nome"];
              $turma["data_criacao"] = $tm["data_criacao"];
              $turma["id_professor"] = $tm["id_professor"];
              $turma["id_universidade"] = $tm["id_universidade"];
              array_push($response["turmas"], $turma);
          } 
          echo json_encode($response);

        } else {
            // no product found
            $response["success"] = 0;
            $response["message"] = "Não há outras turmas";
 
            // echo no users JSON
            echo json_encode($response);
        }
    } else {
        // no product found
        $response["success"] = 0;
        $response["message"] = "Não há outras turmas";
 
        // echo no users JSON
        echo json_encode($response);
    }
} else {
    // required field is missing
    $response["success"] = 0;
    $response["message"] = "Parâmetros obrigatórios não recebidos";
 
    // echoing JSON response
    echo json_encode($response);
}
?>