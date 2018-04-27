<?php

$temModulo = true;
session_start();
include $_SESSION['raiz'] . "funcoes.php";
////////////////////////////////////////////////////////////////////////////////////////
$item = $post['item'];

if ($item == 'tipos_escala_param') {
    $mes = $post['mes'];
    $ano = $post['ano'];
    $orgao = $post['orgao'];

    $resposta = pegarGrupo($mes, $ano, $orgao);
    if (sizeof($resposta) > 0) {
        $resposta = array("existe" => true, 'grupo' => $resposta[0]['id']);
    } else {
        $resposta = array("existe" => false);
    }

    echo json_encode($resposta);
} else if ($item == 'tipos_escala') {
    $grupo = $post['grupo'];

    $resposta = pegarTiposEscala($grupo);

    if (sizeof($resposta) > 0) {
        $tipos = array();
        foreach ($resposta as $re) {
            $tipos[] = $re['tipo'];
        }
        $resposta = array("existe" => true, 'tipos' => $tipos);
    } else {
        $resposta = array("existe" => false);
    }
    echo json_encode($resposta);
} else if ($item == 'tipo_existe') {
    $grupo = $post['grupo'];
    $tipo = $post['tipo'];

    $resposta = pegarTiposEscala($grupo);
    $tipos = array_column($resposta, 'tipo');
    $existe = in_array($tipo, $tipos);
    $resposta = array("existe" => $existe);

    echo json_encode($resposta);
}
////////////////////////////////////////////////////////////////////////////////////////////
include $sessao['raiz'] . 'closeConn.php';
///////////////////////////////////////////////////////////////////////////////////////////////
?>