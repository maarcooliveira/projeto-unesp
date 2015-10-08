<?php
 
// array for JSON response
$response = array();
 
// include db connect class
require_once __DIR__ . '/db_connect.php';
 
if (isset($_POST["user"])) {
    $user = $_POST['user'];

    $queryInsertResolucao = "INSERT IGNORE INTO resolucao (id_atividade, id_usuario, concluido) SELECT id, {$user}, false FROM atividade WHERE id_turma IN (SELECT id_turma FROM usuario_turma WHERE id_usuario = {$user})";

    $queryMapas = "SELECT atividade.*, turma.nome AS turma, resolucao.concluido AS concluido FROM atividade 
                 INNER JOIN turma ON atividade.id_turma = turma.id
                 INNER JOIN resolucao ON (atividade.id = resolucao.id_atividade AND resolucao.id_usuario = {$user})
                 WHERE id_turma IN (SELECT id_turma FROM usuario_turma WHERE id_usuario = {$user})
                 AND liberado = true";

    $insertResolucao = mysqli_query($connection, $queryInsertResolucao);
    $mapas = mysqli_query($connection, $queryMapas);
    
    if (!($insertResolucao && $mapas)) { 
        $response["success"] = 0;
        $response["message"] = "Erro de conexão ao banco de dados";
        echo json_encode($response);
    }

 
    if (!empty($mapas)) {
        // check for empty result
        if (mysqli_num_rows($mapas) > 0) {

            $response["mapas"] = array();
            $response["success"] = 1;

            while($mp = mysqli_fetch_assoc($mapas)) { 
                $mapa = array();
                $mapa["turma"] = $mp["turma"];
                $mapa["concluido"] = $mp["concluido"];
                $mapa["titulo"] = $mp["titulo"];
                $mapa["id"] = $mp["id"];
                $mapa["data_entrega"] = $mp["data_entrega"];
                array_push($response["mapas"], $mapa);
            } 
            echo json_encode($response);
        } else {
            $response["success"] = 0;
            $response["message"] = "Sem atividades no momento";
            echo json_encode($response);
        }
    } else {
        $response["success"] = 0;
        $response["message"] = "Sem atividades no momento";
        echo json_encode($response);
    }
} else {
    $response["success"] = 0;
    $response["message"] = "Parâmetros obrigatórios não recebidos";
    echo json_encode($response);
}
?>