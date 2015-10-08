<?php
// array for JSON response
$response = array();
 
// include db connect class
require_once __DIR__ . '/db_connect.php';
 
// check for post data
if (isset($_POST["id_facebook"]) && isset($_POST['email']) && isset($_POST['tipo']) && isset($_POST['nome']) && isset($_POST['universidade'])) {
    $id = $_POST["id_facebook"];
    $email =  $_POST['email'];
    $tipo = $_POST['tipo'];
    $nome = $_POST['nome'];
    $universidade = $_POST['universidade'];

    $query  = "INSERT INTO usuario (nome, id_facebook, tipo, email, id_universidade) VALUES ('{$nome}', '{$id}', '{$tipo}', '{$email}', {$universidade})";
    $result = mysqli_query($connection, $query);
  
    if ($result) {
            $response["id"] = mysqli_insert_id($connection);
            $response["success"] = 1;
            echo json_encode($response);
    } else {
        $response["success"] = 0;
        $response["message"] = "Erro ao cadastrar aluno";
        echo json_encode($response);
    }
} else {
    $response["success"] = 0;
    $response["message"] = "Campo(s) obrigatorio(s) nao enviado(s)";
    echo json_encode($response);
}
?>