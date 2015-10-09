<?php

$response = array();

// check for required fields
if (isset($_POST['user']) && isset($_POST['turma'])) {
    $user = $_POST['user'];
    $turma = $_POST['turma'];

    // include db connect class
    require_once __DIR__ . '/db_connect.php';

    $query = "DELETE FROM usuario_turma WHERE id_turma = {$turma} AND id_usuario = {$user}";
    // mysql update row with matched pid
    $result = mysqli_query($connection, $query);

    // check if row deleted or not
    if (mysqli_affected_rows() > 0) {
        // successfully updated
        $response["success"] = 1;
        $response["message"] = "Turma removida";
        echo json_encode($response);
    } else {
        // no product found
        $response["success"] = 0;
        $response["message"] = "Erro ao remover turma";
        echo json_encode($response);
    }
} else {
    // required field is missing
    $response["success"] = 0;
    $response["message"] = "Parâmetros obrigatórios não recebidos";
    echo json_encode($response);
}
?>
