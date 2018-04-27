<?php

$temModulo = true;
session_start();
include $_SESSION['raiz'] . "funcoes.php";
$escolha = $post['escolha'];

if ($escolha == 'copiar') {

    $mes = $post['mes'];
    $ano = $post['ano'];
    $orgao = $post['orgao'];
    $unidade = $post['unidade'];

    $resposta = pegarGruposEscala($orgao, $unidade);

    if (sizeof($resposta) > 0) {
        $grupo = $resposta[0]["id"];
        $resposta = copiarParametros($grupo, $mes, $ano, $orgao);
        $resposta = array("existe" => true, "grupo" => $resposta[0]["id"]);
    } else {
        $resposta = array("existe" => false);
    }

    echo json_encode($resposta);
}

if ($escolha == 'branco') {
    $mes = $post['mes'];
    $ano = $post['ano'];
    $orgao = $post['orgao'];
    $unidade = $post['unidade'];

    $resposta = criarParametrosBranco($mes, $ano, $orgao, $unidade);

    $resposta = array("existe" => true, 'grupo' => $resposta['id']);
    echo json_encode($resposta);
}

////////////////////////////////////////////////////////////////////////////////////////////
include $sessao['raiz'] . 'closeConn.php';
///////////////////////////////////////////////////////////////////////////////////////////////
?>