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
            $re['tipo'] != 1 ? $tipos[] = $re['tipo'] : null;
        }
        $resposta = array("existe" => !empty($tipos), 'tipos' => $tipos);
    } else {
        $resposta = array("existe" => false);
    }
    echo json_encode($resposta);
} else if ($item == 'escala') {
    $grupo = $post['grupo'];
    $tipo = $post['tipo'];
    $usuario = $post['usuario'];
    $nomeOp = $post['nomeOperador'];

    $erro = false;
    $textoErro = "";
    $aviso = false;
    $textoAviso = "";


    //pegar as escalas que este operador faz parte
    $escalas = pegarEscalasDoOperador($grupo, $usuario);
    if (sizeof($escalas) == 0) {
        $erro = true;
        $textoErro = "$nomeOp não está alocado(a) em nenhuma escala neste mês.";
    } else {
        //checa se alguma escala que ele faz parte foi publicada
        $resposta = checarPublicacaoEscala($escalas, $tipo);
        $qtd = $resposta[0]['qtd'];
        if ($qtd == 0) {
            $erro = true;
            $textoErro = "As escalas que o(a) $nomeOp participa ainda não foram publicadas.";
        } else if ($qtd < sizeof($escalas)) {
            $aviso = true;
            $textoAviso = "Uma ou mais escalas do(a) $nomeOp ainda não foram publicadas.";
        }
    }
    $resposta['existe'] = $erro;
    $resposta['texto'] = $textoErro;
    $resposta['aviso'] = $aviso;
    $resposta['textoAviso'] = $textoAviso;
    echo json_encode($resposta);
}
////////////////////////////////////////////////////////////////////////////////////////////
include $sessao['raiz'] . 'closeConn.php';
///////////////////////////////////////////////////////////////////////////////////////////////
?>