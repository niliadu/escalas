<?php

$temModulo = false;
$servico = true;
/////////////////////////////////////////////////
session_start();
include $_SESSION['raiz'] . "funcoes.php";

$item = $get['item'];
if ($item == 'checar') {
    $usuario = $get['cadastro'];

    $inicio = substr(str_replace("-", "", $get['inicio']), 0, 8);
    $termino = substr(str_replace("-", "", $get['termino']), 0, 8);

    $anoMesIni = substr(str_replace("-", "", $get['inicio']), 0, 6);
    $anoMesTer = substr(str_replace("-", "", $get['termino']), 0, 6);

    $r = checarSeExisteServicoNoAfastamento($usuario, $inicio, $termino, $anoMesIni, $anoMesTer);
    $qtdSvc = $r[0]['qtd'];

    $existe = $qtdSvc > 0 ? true : false;
    $resposta['existe'] = $existe;

    echo json_encode($resposta);
} else if ($item == 'lancado') {
    $usuario = $get['cadastro'];

    $inicio = substr(str_replace("-", "", $get['inicio']), 0, 8);
    $termino = substr(str_replace("-", "", $get['termino']), 0, 8);

    $anoMesIni = substr(str_replace("-", "", $get['inicio']), 0, 6);
    $anoMesTer = substr(str_replace("-", "", $get['termino']), 0, 6);

    $resposta['sucesso'] = removerOsSerivicosOperacionaisDevidoAfastamento($usuario, $inicio, $termino, $anoMesIni, $anoMesTer);
    
    echo json_encode($resposta);
} else if ($item == 'retirar') {
    $usuario = $get['cadastro'];

    $inicio = substr(str_replace("-", "", $get['inicio']), 0, 8);
    $termino = substr(str_replace("-", "", $get['termino']), 0, 8);

    $anoMesIni = substr(str_replace("-", "", $get['inicio']), 0, 6);
    $anoMesTer = substr(str_replace("-", "", $get['termino']), 0, 6);

    $resposta['sucesso'] = reinserirOsSerivicosOperacionaisDevidoAfastamento($usuario, $inicio, $termino, $anoMesIni, $anoMesTer);
    echo "<pre>";
    print_r($resposta);
    echo "</pre>";
//    echo json_encode($resposta);
}

