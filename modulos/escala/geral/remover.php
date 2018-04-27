<?php

$temModulo = true;
session_start();
include $_SESSION['raiz'] .  "funcoes.php";
////////////////////////////////////////////////////////////////////////////////////////

$item = $post['item'];

if ($item == 'turno') {
    $id = $post['id'];
    echo json_encode(removerTurno($id));
} else if ($item == 'escala') {
    $id = $post['id'];
    echo json_encode(removerEscala($id));
} else if ($item == 'efetivo') {
    $id = $post['id'];
    echo json_encode(removerEfetivo($id));
} else if ($item == 'restricao') {
    $id = $post['id'];
    echo json_encode(removerCombinacao($id));
} else if ($item == 'informacoes_afastamento') {
    $ids = explode("|", $post['ids']);
    echo json_encode(removerInformacoesAfastamentos($ids));
}
else if($item == 'remover_risaer'){
    $servico = $post['servico'];
    echo json_encode(removerServicoRisaer($servico));
}

////////////////////////////////////////////////////////////////////////////////////////////
include $sessao['raiz'] . 'closeConn.php';
///////////////////////////////////////////////////////////////////////////////////////////////
?>