<?php
// array for JSON response
$response = array();
 
// include db connect class
require_once __DIR__ . '/db_connect.php';
 
// check for post data
if (isset($_POST["user"]) && isset($_POST["turma"])) {
    $user = $_POST['user'];
    $turma = $_POST["turma"];

    $query  = "INSERT INTO usuario_turma (id_usuario, id_turma) VALUES ({$user}, {$turma})";
    $result = mysqli_query($connection, $query);
  
    if ($result) {
      $response["success"] = 1;
      $response["message"] = "Turma cadastrada na lista do aluno";
      echo json_encode($response);
    } else {
        // no product found
        $response["success"] = 0;
        $response["message"] = "Erro ao cadastrar aluno na turma";
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