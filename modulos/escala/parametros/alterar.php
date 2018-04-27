<?php

$temModulo = true;
session_start();
include $_SESSION['raiz'] . "funcoes.php";
$item = $post['item'];

if ($item == 'alterar_turno') {
    $nome = mb_strtoupper($post['nome'], "UTF-8");
    $legenda = mb_strtoupper($post['legenda'], "UTF-8");
    $inicio = $post['inicio'];
    $termino = $post['termino'];
    $etapa = $post['etapa'];
    $periodo = $post['periodo'];
    $posNoturno = $post['posNoturno'];
    $id = $post['id'];

    echo json_encode(alterarTurno($nome, $legenda, $inicio, $termino, $etapa, $periodo, $posNoturno, $id));
} else if ($item == 'alterar_escala') {
    $nome = mb_strtoupper($post['nome'], "UTF-8");
    $legenda = mb_strtoupper($post['legenda'], "UTF-8");
    $turnos = $post['turnos'];
    $ch = $post['ch'];
    $id = $post['id'];
    $grupo = $post['grupo'];
    $svc = $post['svc'];

    alterarEscala($nome, $legenda, $ch[0], $ch[1], $id, $svc);


    $resposta2 = buscarTurnoEscala($id);
    $turnosAntes = array();

    foreach ($resposta2 as $r) {
        $turnosAntes[] = $r['turno'];
    }
    foreach ($turnos as $t) {
        if (!in_array($t, $turnosAntes)) {
            inserirTurnoEscala($id, $t, $grupo);
        }
    }
    foreach ($turnosAntes as $ta) {
        if (!in_array($ta, $turnos)) {
            deletarTurnoEscala($ta, $id);
        }
    }
    echo json_encode(array());
} else if ($item == 'alterar_efetivo') {

    $legenda = mb_strtoupper($post['legenda'], "UTF-8");
    $escalas = $post['escalas'];
    $funcao = $post['funcao'];
    $id = $post['id'];
    $grupo = $post['grupo'];
    $manutencao = $post['manutencao'];

    atualizarefetivoMes($legenda, $funcao, $id, $manutencao);


    $resposta2 = buscarEscalaDoEfetivo($id);
    $escalasAntes = array();

    foreach ($resposta2 as $r) {
        $escalasAntes[] = $r['escala'];
    }
    foreach ($escalas as $e) {
        if (!in_array($e, $escalasAntes)) {
            inserirEscEfetivo($e, $id, $grupo);
        }
    }
    foreach ($escalasAntes as $ea) {
        if (!in_array($ea, $escalas)) {
            deletarEfetivoEscala($ea, $id);
        }
    }
    echo json_encode(array());
} else if ($item == 'alterar_consecutivos') {
    $grupo = $post['grupo'];
    $folgas = $post['folgas'];
    $trocas = $post['trocas'];

    echo json_encode(alterarConsecutivos($grupo, $folgas, $trocas));
} else if ($item == 'alterar_combinacao') {
    $id = $post['id'];
    $turno1 = $post['turno1'];
    $turno2 = $post['turno2'];

    $resposta = alterarCombinacao($id, $turno1, $turno2);
    echo json_encode($resposta);
} else if ($item == 'alterar_risaer') {
    $id = $post['id'];
    $nome = mb_strtoupper($post['nome'], "UTF-8");
    $legenda = mb_strtoupper($post['legenda'], "UTF-8");
    $inicio = $post['inicio'];
    $termino = $post['termino'];
    $maior24 = $post['maior24'];
    $etapa = $post['etapa'];
    $dant = $post['dant'] == "" || $post['dant'] == 0 ? null : $post['dant']; //problema com a inserção de valor 0
    $dpost = $post['dpost'] == "" || $post['dpost'] == 0 ? null : $post['dpost']; //a query nao dava erro mas tb nao inseria o valor, entao todo 0 deve virar NULL


    $resposta = alterarRISAER($id, $nome, $legenda, $inicio, $termino, $maior24, $etapa, $dant, $dpost);

    echo json_encode($resposta);
}
////////////////////////////////////////////////////////////////////////////////////////////
include $sessao['raiz'] . 'closeConn.php';
///////////////////////////////////////////////////////////////////////////////////////////////
?>