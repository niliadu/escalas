
<?php

$temModulo = true;
session_start();
include $_SESSION['raiz'] . "funcoes.php";

$post = filter_input_array(INPUT_POST, FILTER_DEFAULT);
$item = $post['item'];

if ($item == 'tipos_escala') {
    $tipos = $post['tipos'];
//escala definitiva
    if (is_array($tipos) && in_array(4, $tipos)) {
        $tiposNomes = array(2 => "PREVISTA", 4 => "DEFINITIVA");
    }
//escala corrente
    else if (is_array($tipos) && in_array(3, $tipos)) {
        $tiposNomes = array(2 => "PREVISTA", 3 => "CORRENTE");
    }
//escala prevista
    else {
        $tiposNomes = array(2 => "PREVISTA");
    }
    include 'htmls/lista_tipos.php';
} else if ($item == 'lancadas') {
    $tipo = $post['tipo'];
    $grupo = $post['grupo'];

    $usuarios = pegarListaUsuariosHabilitados();

    $trocas = pegarTrocasLancadas($grupo, $tipo, array(1));

    include './htmls/lista_trocas.php';
} else if ($item == 'autorizadas') {
    $tipo = $post['tipo'];
    $grupo = $post['grupo'];

    $usuarios = pegarListaUsuariosHabilitados();

    $trocas = pegarTrocasLancadas($grupo, $tipo, array(2));

    include './htmls/lista_trocas_aut.php';
} else if ($item == 'concluidas') {
    $tipo = $post['tipo'];
    $grupo = $post['grupo'];

    $usuarios = pegarListaUsuariosHabilitados();

    $trocasEfetivadas = pegarTrocasLancadas($grupo, $tipo, array(3, 4));
    $trocasExcluidas = pegarTrocasLancadas($grupo, $tipo, array(6));

    include './htmls/lista_trocas_conc.php';
} else if ($item == 'justificativa') {
    $trocaID = $post['trocaID'];
    $obrigatorio = $post['obrigatorio'];
    $novoStatus = $post['novoStatus'];

    $cor = $novoStatus == 3 ? "success" : 'danger';
    include './htmls/modal_justificativa.php';
}

////////////////////////////////////////////////////////////////////////////////////////////
include $sessao['raiz'] . 'closeConn.php';
///////////////////////////////////////////////////////////////////////////////////////////////
?>