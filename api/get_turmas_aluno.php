<?php
// array for JSON response
$response = array();
 
// include db connect class
require_once __DIR__ . '/db_connect.php';
 
// check for post data
if (isset($_POST["user"])) {
    $user = $_POST['user'];

  $queryTurmas = "SELECT turma.*, usuario.nome AS professor FROM turma 
                  INNER JOIN usuario ON turma.id_professor = usuario.id
                  WHERE turma.id IN (SELECT id_turma FROM usuario_turma WHERE id_usuario = {$user})";

  $turmas = mysqli_query($connection, $queryTurmas);
  
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
              $turma["professor"] = $tm["professor"];
              $turma["id_universidade"] = $tm["id_universidade"];
              array_push($response["turmas"], $turma);
          } 
          echo json_encode($response);

        } else {
            // no product found
            $response["success"] = 0;
            $response["message"] = "Aluno sem turmas cadastradas";
 
            // echo no users JSON
            echo json_encode($response);
        }
    } else {
        // no product found
        $response["success"] = 0;
        $response["message"] = "Aluno sem turmas cadastradas";
 
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