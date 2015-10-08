<?php
// array for JSON response
$response = array();
 
// include db connect class
require_once __DIR__ . '/db_connect.php';
 
// check for post data
if (isset($_POST["id_facebook"])) {
    $id_facebook = $_POST["id_facebook"];

    $query  = "SELECT * FROM usuario WHERE id_facebook = '{$id_facebook}' LIMIT 1";
    $result = mysqli_query($connection, $query);

  
    if ($result) {
        while($user = mysqli_fetch_assoc($result)) {
          $response["id"] = $user["id"];
          $response["tipo"] = $user["tipo"];
        }
        if (isset($response["tipo"]) && $response["tipo"] == "aluno") {
            $response["success"] = 1;
        }
        else {
            $response["success"] = 0;
            $response["message"] = "Usuário é um professor";   
        }
        echo json_encode($response);
    } else {
        $response["success"] = 0;
        $response["message"] = "Aluno não cadastrado";
        echo json_encode($response);
    }
} else {
    // required field is missing
    $response["success"] = 0;
    $response["message"] = "Campo obrigatório não enviado";
    echo json_encode($response);
}
?>