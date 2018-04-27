<?php
$temModulo = true;
session_start();
include $_SESSION['raiz'] .  "funcoes.php";
////////////////////////////////////////////////////////////////////////////////////////
$item = $post['item'];

if ($item == 'escalas') {
    $grupo = $post['grupo'];
    $tipo = $post['tipo'];    
    $resposta = copiarEscalas($grupo, $tipo-3);//subtraio 3 do tipo pra enviar o proximo tipo das escalas para serem geradas        
    //$resposta['novoTipo'] = $tipo-3;
    echo json_encode($resposta);
}

////////////////////////////////////////////////////////////////////////////////////////////
include $sessao['raiz'] . 'closeConn.php';
///////////////////////////////////////////////////////////////////////////////////////////////
?>