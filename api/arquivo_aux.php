<?php
  /* NextEx - Ferramenta de Avaliação
   * api/arquivo_aux.php
   *
   * Contém funções da API relacionadas à recuperação de arquivos de atividade,
   * gabarito e resolução.
  */

  // Retorna o .json de uma atividade
  function getJsonAtividade($idAtividade) {
    return file_get_contents(getcwd() . "/atividades/" . $idAtividade . "/mapa.json");
  }

  // Retorna o gabarito em .json de uma atividade
  function getJsonGabarito($idAtividade) {
    return file_get_contents(getcwd() . "/atividades/" . $idAtividade . "/gabarito.json");
  }

  // Retorna a resolução em .json de um aluno para uma dada atividade
  function getJsonResolucao($idAtividade, $idAluno) {
    $path = "/atividades/" . $idAtividade ."/resolucoes/" . $idAluno . "_mapa.json";

    if (file_exists(getcwd() . $path)) {
      return file_get_contents(getcwd() . $path);
    }
    else {
      return "";
    }
  }

  // Retorna todas as resoluções de uma atividade como um array em .json
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
