<?php

$temModulo = true;
session_start();
include $_SESSION['raiz'] .  "funcoes.php";
////////////////////////////////////////////////////////////////////////////////////////

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
    $grupo = $post['grupo'];

    $resposta = inserirEscala($nome, $legenda, $grupo, $ch[0], $ch[1]);
    $id_escala = $resposta['id'];
    foreach ($turnos as $t) {
        inserirTurnoEscala($id_escala, $t);
    }
    echo json_encode($resposta);
} else if ($item == 'add_efetivo') {
    $usuario = $post['usuario'];
    $legenda = mb_strtoupper($post['legenda'], "UTF-8");
    $grupo = $post['grupo'];
    $escalas = $post['escalas'];
    $funcao = $post['funcao'];

    $resposta = inserirEfetivo($usuario, $legenda, $grupo, $funcao);
    $id_efetivo = $resposta['id'];
    foreach ($escalas as $e) {
        inserirEscEfetivo($e, $id_efetivo);
    }
    echo json_encode($resposta);
} else if ($item == 'add_combinacao') {
    $grupo = $post['grupo'];
    $turno1 = $post['turno1'];
    $turno2 = $post['turno2'];

    echo json_encode(inserirCombinação($grupo, $turno1, $turno2));
} else if ($item == 'escala_branco') {
    $grupo = $post['grupo'];
    $resposta = inserirEscalaBranco($grupo);
    echo json_encode($resposta);
} else if ($item == 'preencher_escalas') {
    $grupo = $post['grupo'];
    $turnos = $post['turnos'];
    $resposta = mesAnoGrupo($grupo);
    $mes = $resposta[0]['mes'];
    $ano = $resposta[0]['ano'];
    $qtdDiasMes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
    $equipesSQL = pegarEquipes($grupo);
    $operInserido = array();
    $equipes = array();    
    //$turnosEsc = buscarTurnoEscala($eqp['escala']);
    //$turEsc = array();
    //foreach ($turnosEsc as $te){
    //    $turEsc[] = $te['turno'];
    //}
    foreach ($equipesSQL as $eqp) {
        if (!in_array($eqp['usuario'], $operInserido)) {
            $equipes[$eqp['escala']][] = ['equipe' => $eqp['equipe'], 'operador' => $eqp['operador'], 'usuario' => $eqp['usuario'], 'turnos' => buscarTurnoEscala($eqp['escala'])];            
            //$qtdEquipes[$eqp['escala']][] = $eqp['equipe'];
            $operInserido[] = $eqp['usuario'];
        }
    }    
    
    $resposta = inserirSequencia($grupo, $turnos, $equipes, $qtdDiasMes);
    echo json_encode($resposta);
} else if ($item == 'comentario') {
    $em_id = $post['em_id'];
    $tipo = $post['tipo'];
    $texto = $post['texto'];

    $resposta = pegarComentario($em_id);
    if (sizeof($resposta) > 0) {
        if ($texto == "") {
            $resposta = removerComentario($resposta[0]['id']);
            $resposta['removeu'] = true;
        } else {
            $resposta = atualizarComentario($resposta[0]['id'], $texto);
            $resposta['removeu'] = false;
        }
    } else {
        if ($texto != "") {
            $resposta = inserirComentario($em_id, $tipo, $texto);
            $resposta['removeu'] = false;
        }
    }
    echo json_encode($resposta);
}
else if($item == 'inserir_risaer'){
    $servico = $post['servico'];
    $efetivo_mes= $post['efetivo_mes'];
    $tipo_escala= $post['tipo_escala'];
    $dia= $post['dia'];
    echo json_encode(inserirServicoRisaer($servico, $efetivo_mes, $tipo_escala, $dia));
}
////////////////////////////////////////////////////////////////////////////////////////////
include $raiz . 'closeConn.php';
///////////////////////////////////////////////////////////////////////////////////////////////
?>