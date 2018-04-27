<?php

$temModulo = true;
session_start();
include $_SESSION['raiz'] . "funcoes.php";
$item = $post['item'];


if ($item == 'add_turno') {
    $nome = mb_strtoupper($post['nome'], "UTF-8");
    $legenda = mb_strtoupper($post['legenda'], "UTF-8");
    $inicio = $post['inicio'];
    $termino = $post['termino'];
    $etapa = $post['etapa'];
    $grupo = $post['grupo'];
    $periodo = $post['periodo'];
    $posNoturno = $post['posNoturno'];

    $resposta = inserirTurno($nome, $legenda, $inicio, $termino, $etapa, $periodo, $posNoturno, $grupo);

    echo json_encode($resposta);
} else if ($item == 'add_escala') {
    $nome = mb_strtoupper($post['nome'], "UTF-8");
    $legenda = mb_strtoupper($post['legenda'], "UTF-8");
    $turnos = $post['turnos'];
    $ch = $post['ch'];
    $svc = $post['svc'];
    $grupo = $post['grupo'];

    $resposta = inserirEscala($nome, $legenda, $grupo, $ch[0], $ch[1], $svc);

    $id_escala = $resposta['id'];
    foreach ($turnos as $t) {
        inserirTurnoEscala($id_escala, $t, $grupo);
    }
    echo json_encode($resposta);
} else if ($item == 'add_efetivo') {
    $usuario = $post['usuario'];
    $legenda = mb_strtoupper($post['legenda'], "UTF-8");
    $grupo = $post['grupo'];
    $escalas = $post['escalas'];
    $funcao = $post['funcao'];
    $manutencao = $post['manutencao'];

    $resposta = inserirEfetivo($usuario, $legenda, $grupo, $funcao, $manutencao);
    $id_efetivo = $resposta['id'];
    foreach ($escalas as $e) {
        inserirEscEfetivo($e, $id_efetivo, $grupo);
    }
    echo json_encode($resposta);
} else if ($item == 'add_combinacao') {
    $grupo = $post['grupo'];
    $turno1 = $post['turno1'];
    $turno2 = $post['turno2'];

    $resposta = inserirCombinação($grupo, $turno1, $turno2);

    echo json_encode($resposta);
} else if ($item == 'add_risaer') {
    $nome = mb_strtoupper($post['nome'], "UTF-8");
    $legenda = mb_strtoupper($post['legenda'], "UTF-8");
    $inicio = $post['inicio'];
    $termino = $post['termino'];
    $maior24 = $post['maior24'];
    $etapa = $post['etapa'];
    $grupo = $post['grupo'];
    $dant = $post['dant'] == "" || $post['dant'] == 0 ? null : $post['dant']; //problema com a inserção de valor 0
    $dpost = $post['dpost'] == "" || $post['dpost'] == 0 ? null : $post['dpost']; //a query nao dava erro mas tb nao inseria o valor, entao todo 0 deve virar NULL


    $resposta = inserirRISAER($nome, $legenda, $inicio, $termino, $maior24, $etapa, $dant, $dpost, $grupo);

    echo json_encode($resposta);
}
////////////////////////////////////////////////////////////////////////////////////////////
include $sessao['raiz'] . 'closeConn.php';
///////////////////////////////////////////////////////////////////////////////////////////////
?>