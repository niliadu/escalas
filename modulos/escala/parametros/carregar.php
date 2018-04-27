<?php

$temModulo = true;
session_start();
include $_SESSION['raiz'] . "funcoes.php";
$item = $post['item'];

/* * *****************************************************************************
 *                              TURNOS
 * **************************************************************************** */
if ($item == 'lista_turnos') {
    $grupo = $post['grupo'];
    $turnos = listaTurnos($grupo);
    include 'htmls/lista_turnos.php';
} else if ($item == 'add_turno') {
    include 'htmls/add_turno.php';
} else if ($item == 'editar_turno') {
    $grupo = $post['grupo'];
    $id = $post['id'];
    $resposta = buscarTurno('id', $id, $grupo);
    include 'htmls/editar_turno.php';

    /*     * *****************************************************************************
     *                              ESCALAS
     * **************************************************************************** */
} else if ($item == 'lista_escalas') {
    $grupo = $post['grupo'];
    $escalas = listaEscalas($grupo);
    $respostaEscala = $escalas;

    foreach ($escalas as $i => $e) {
        $tur = buscarTurnosEscala($e['id']);
        $turnos = array();
        $turnosId = array();
        foreach ($tur as $t) {
            $turnos[] = $t['legenda'];
            $turnosId[] = $t['id'];
        }
        $escalas[$i]['turnos'] = $turnos;
        $escalas[$i]['turnosId'] = $turnosId;
    }
    if ($item == 'lista_escalas') {
        include 'htmls/lista_escalas.php';
    } else if ($item == 'pegarEscalaId') {
        $respostaEscala = $escalas;
        echo json_encode($respostaEscala);
    }
} else if ($item == 'add_escala') {

    $grupo = $post['grupo'];
    $turnos = listaTurnos($grupo);
    include 'htmls/add_escala.php';
} else if ($item == "editar_escala") {
    $grupo = $post['grupo'];

    $turnos = listaTurnos($grupo);

    $id = $post['id'];
    $resposta = buscarEscala('id', $id, $grupo);

    $escala = $resposta[0];
    $tur = buscarTurnosEscala($id);

    foreach ($tur as $t) {
        $turnosLeg[] = $t['legenda'];
        $turnosId[] = $t['id'];
    }
    $escala['turnos'] = $turnosLeg;
    $escala['turnosId'] = $turnosId;

    include 'htmls/editar_escala.php';
    /*     * *****************************************************************************
     *                              EFETIVO
     * **************************************************************************** */
} else if ($item == 'lista_efetivo') {
    $grupo = $post['grupo'];

    $usuariosSistema = pegarListaUsuariosHabilitados();

    $efetivo = listaEfetivo($grupo);
    $dadosG = pegarDadosGrupo($grupo);
    $usuariosHabilitados = verificaHabilitacaoValidaUsuarios(array_column($efetivo, 'operador'), $sessao['orgao_usu_id'], $dadosG['ano'], $dadosG['mes'],$sessao['unidade_usu_id']);
    include 'htmls/lista_efetivo.php';
} else if ($item == 'add_efetivo' || $item == 'editar_efetivo') {
    $grupo = $post['grupo'];
    $escalas = listaEscalasGrupo($grupo);
    $funcoes = listaFuncoesEscala();
    if ($item == 'add_efetivo') {

        $ano = $post['ano'];
        $mes = $post['mes'];
        $resposta = pegarListaUsuariosHabilitacaoValida($sessao['orgao_usu_id'], $ano, $mes, $sessao['unidade_usu_id']);
        $usuariosSistema = array();
        foreach ($resposta as $r) {
            $primeiro = explode(" ", $r['ng'])[0];
            $r['ng'] = $r['pg'] == 'CV' ? $primeiro : $r['ng'];
            $usuariosSistema[$r['ordem']][$r['usuario_id']] = $r;
        }
        ksort($usuariosSistema);
        include 'htmls/add_efetivo.php';
    } else if ($item == 'editar_efetivo') {
        $usuariosSistema = pegarListaUsuariosHabilitados();
        $id = $post['id'];
        $operador = pegarOperEfetivo($id);
        include 'htmls/editar_efetivo.php';
    }

    /*     * *****************************************************************************
     *                              RESTRIÇÕES
     * **************************************************************************** */
} else if ($item == 'lista_restricoes' || $item == "editar_restricoes") {
    $grupo = $post['grupo'];
    $restricoes = pegarListaRestricoes($grupo);
    if ($item == 'lista_restricoes') {
        include 'htmls/lista_restricoes.php';
    } else if ($item == "editar_restricoes") {
        include 'htmls/editar_consecutivos.php';
    }
} else if ($item == "add_combinacao") {
    $grupo = $post['grupo'];
    $turnos = listaTurnos($grupo);
    include 'htmls/add_combinacao.php';
} else if ($item == "editar_combinacao") {
    $grupo = $post['grupo'];
    $id = $post['id'];
    $turnos = listaTurnos($grupo);
    $resposta = pegarCombinacao($id);
    $combinacao = $resposta[0];
    include 'htmls/editar_combinacao.php';
    /*     * *****************************************************************************
     *                              RISAER
     * **************************************************************************** */
} else if ($item == 'lista_risaer') {
    $grupo = $post['grupo'];
    $risaer = pegarListaRISAER($grupo);
    include 'htmls/lista_risaer.php';
} else if ($item == 'add_risaer') {
    include 'htmls/add_risaer.php';
} else if ($item == 'editar_risaer') {
    $grupo = $post['grupo'];
    $id = $post['id'];
    $resposta = buscarRISAER('id', $id, $grupo);
    include 'htmls/editar_risaer.php';
}
////////////////////////////////////////////////////////////////////////////////////////////
include $sessao['raiz'] . 'closeConn.php';
///////////////////////////////////////////////////////////////////////////////////////////////
?>