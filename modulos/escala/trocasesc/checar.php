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
        foreach ($resposta as $re) {
            $tipos[] = $re['tipo'];
        }
        $resposta = array("existe" => true, 'tipos' => $tipos);
    } else {
        $resposta = array("existe" => false);
    }
    echo json_encode($resposta);
} else if ($item == 'trocas_liberadas') {
    $grupo = $post['grupo'];
    $tipo = $post['tipo'];

    $resposta = pegarStatusTrocas($grupo, $tipo);
    if (empty($resposta) || !$resposta[0]['trocas_liberadas']) {
        $resposta = array("existe" => false);
    } else {
        $resposta = array("existe" => true);
    }
    echo json_encode($resposta);
} else if ($item == 'alocacao_efetivo') {
    $grupo = $post['grupo'];
    $usuario = $post['usuario'];
    $usuarioNome = $post['usuarioNome'];

    $existe = true;
    $textoErro = "";

    $resposta = pegarIdEfetivoMes($grupo, $usuario);
    if (empty($resposta)) {
        $existe = false;
        $textoErro .= "O(A) $usuarioNome não está alocado no efetivo deste mês.";
    }
    $resposta['existe'] = $existe;
    $resposta['texto'] = $textoErro;
    echo json_encode($resposta);
} else if ($item == 'publicacao') {
    $grupo = $post['grupo'];
    $tipo = $post['tipo'];
    $usuario = $post['usuario'];
    $usuarioNome = $post['usuarioNome'];

    $erro = true;
    $textoErro = "";
    $aviso = false;
    $textoAviso = "";

    $resposta = pegarIdEfetivoMes($grupo, $usuario);
    $efetivoMesID = empty($resposta) ? 0 : $resposta[0]['id'];

    $escalas = pegarEscalasDoOperadorPeloEfetivo($efetivoMesID);
    $resposta = checarPublicacaoEscala($escalas, $tipo);
    $qtd = $resposta[0]['qtd'];
    if ($qtd == 0) {
        $erro = false;
        $textoErro = "As escalas que o(a) $usuarioNome participa ainda não foram publicadas.";
    } else if ($qtd < sizeof($escalas)) {
        $aviso = true;
        $textoAviso = "Uma ou mais escalas do(a) $usuarioNome ainda não foram publicadas.";
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