<?php

$temModulo = true;
session_start();
include $_SESSION['raiz'] . "funcoes.php";
////////////////////////////////////////////////////////////////////////////////////////
$post = filter_input_array(INPUT_POST, FILTER_DEFAULT);
$item = $post['item'];


if ($item == "troca") {
    $proponente = $post['proponente'];
    $escalaPE = $post['escalaPE'];
    $turnoPE = $post['turnoPE'];
    $diaPE = $post['diaPE'];
    $proposto = $post['proposto'];
    $escalaPO = $post['escalaPO'];
    $turnoPO = $post['turnoPO'];
    $diaPO = $post['diaPO'];

    $grupo = $post['grupo'];
    $tipo = $post['tipo'];

    $resposta = inserirTroca($proponente, $escalaPE, $turnoPE, $diaPE, $proposto, $escalaPO, $turnoPO, $diaPO, $grupo, $tipo);

    echo json_encode($resposta);
} else if ($item == 'novo_status') {
    $trocaID = $post['trocaID'];
    $texto = $post['texto'];
    $status = $post['status'];

    if ($status == 3) {
        $grupo = $post['grupo'];
        $tipo = $post['tipo'];

        $resposta = efetivarTroca($trocaId, $grupo, $tipo, $texto, $status);
    } else {
        $resposta = inserirNovoStatusTroca($trocaID, $status, $sessao['usu'], $texto);
    }
    echo json_encode($resposta);
}
////////////////////////////////////////////////////////////////////////////////////////////
include $sessao['raiz'] . 'closeConn.php';
///////////////////////////////////////////////////////////////////////////////////////////////
?>