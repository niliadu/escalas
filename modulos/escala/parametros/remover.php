<?php
$temModulo = true;
session_start();
include $_SESSION['raiz'] .  "funcoes.php";
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
}else if ($item == 'risaer') {
    $id = $post['id'];
    echo json_encode(removerRISAER($id));
}else if ($item == 'grupo') {
    $id = $post['id'];
    echo json_encode(removerGrupo($id));
}
////////////////////////////////////////////////////////////////////////////////////////////
include $sessao['raiz'] . 'closeConn.php';
///////////////////////////////////////////////////////////////////////////////////////////////
?>