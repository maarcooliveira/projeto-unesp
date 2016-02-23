<?php

  function getJsonAtividade($idAtividade) {
    return file_get_contents(getcwd() . "/atividades/" . $idAtividade . "/mapa.json");
  }

  function getJsonGabarito($idAtividade) {
    return file_get_contents(getcwd() . "/atividades/" . $idAtividade . "/gabarito.json");
  }

  function getJsonResolucao($idAtividade, $idAluno) {
    $path = "/atividades/" . $idAtividade ."/resolucoes/" . $idAluno . "_mapa.json";

    if (file_exists(getcwd() . $path)) {
      return file_get_contents(getcwd() . $path);
    }
    else {
      return "";
    }
  }

  function getJsonResolucoes($idAtividade) {
    $path = "/atividades/" . $idAtividade ."/resolucoes";

    $resolucoes = preg_grep('/^([^.])/', scandir(getcwd() . $path));
    $response = array();

    foreach ($resolucoes as $res) {
      array_push($response, file_get_contents(getcwd() . $path . "/" . $res));
    }

    return json_encode($response);
  }

?>
