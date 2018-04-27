
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
        $tiposNomes = array(2 => "PREVISTA", 3 => "DEFINITIVA");
    }
//escala prevista
    else {
        $tiposNomes = array(2 => "PREVISTA");
    }
    include 'htmls/lista_tipos.php';
} else if ($item == 'proponente') {

    $grupo = $post['grupo'];
    $tipo = $post['tipo'];
    $usuario = $post['usuario'];
    $usuarioNome = $post['usuarioNome'];

    $resposta = pegarIdEfetivoMes($grupo, $usuario);
    $efetivoMesID = empty($resposta) ? 0 : $resposta[0]['id'];

    $escalas = pegarEscalasDoOperadorPeloEfetivo($efetivoMesID);

    $ep = pegarEscalasPublicadas($grupo, $tipo);
    $escalasPublicadas = array();
    foreach ($ep as $e) {
        $escalasPublicadas[] = $e['escala'];
    }

    include 'htmls/proponente.php';
} else if ($item == 'turnos') {
    $escala = $post['escala'];
    $pp = $post['pp'];

    $turnos = listaTurnosEscala($escala);
    include 'htmls/dia_turnos.php';
} else if ($item == 'proposto') {
    $grupo = $post['grupo'];
    $tipo = $post['tipo'];
    $proponente = $post['proponente'];

    $resposta = pegarListaUsuariosHabilitados();
    $operadores = array();
    foreach ($resposta as $r) {
        $primeiro = explode(" ", $r['ng'])[0];
        $r['ng'] = $r['pg'] == 'CV' ? $primeiro : $r['ng'];
        $operadores[$r['ordem']][$r['usuario_id']] = $r;
    }
    ksort($operadores);

    include './htmls/proposto.php';
} else if ($item == 'proposto_escalas') {
    $grupo = $post['grupo'];
    $tipo = $post['tipo'];
    $usuario = $post['usuario'];

    $resposta = pegarIdEfetivoMes($grupo, $usuario);
    $efetivoMesID = empty($resposta) ? 0 : $resposta[0]['id'];

    $escalas = pegarEscalasDoOperadorPeloEfetivo($efetivoMesID);

    $ep = pegarEscalasPublicadas($grupo, $tipo);
    $escalasPublicadas = array();
    foreach ($ep as $e) {
        $escalasPublicadas[] = $e['escala'];
    }
    include 'htmls/proposto_escalas.php';
} else if ($item == 'analise') {

    $operador = $post['proponente'];
    $nomePE = $post['nomePE'];
    $diaDisp = $post['diaPE'];
    $turnoDisp = $post['turnoPE'];
    $escalaDisp = $post['escalaPE'];
    $legendaEscDisp = $post['legendaEscalaPE'];
    $legendaTurnoDisp = $post['legendaTurnoPE'];

    $diaEsc = $post['diaPO'];
    $turnoEsc = $post['turnoPO'];
    $legendaTurnoEsc = $post['legendaTurnoPO'];
    $escalaEsc = $post['escalaPO'];
    $usuPO = $post['proposto'];

    $grupo = $post['grupo'];
    $tipo = $post['tipo'];

    $resposta = analisarTroca(true, $grupo, $operador, $escalaDisp, $diaEsc, $tipo, $diaDisp, $turnoDisp, $turnoEsc, $legendaTurnoDisp, $legendaEscDisp, $legendaTurnoEsc, $escalaEsc, $usuPO);
    $corErro = $resposta['corErro'];
    $textoErro = $resposta['textoErro'];

    include 'htmls/aviso_de_erro.php';
} else if ($item == 'lancadas') {
    $usuario = $post['proponente'];
    $tipo = $post['tipo'];
    $grupo = $post['grupo'];

    $usuarios = pegarListaUsuariosHabilitados();

    $trocas = pegarTrocasLancadasUsuario($usuario, $grupo, $tipo, array(1));

    include './htmls/lista_trocas.php';
} else if ($item == 'concluidas') {
    $usuario = $post['proponente'];
    $tipo = $post['tipo'];
    $grupo = $post['grupo'];

    $usuarios = pegarListaUsuariosHabilitados();

    $status = array(3, 4, 6);
    $trocasEfetivadas = pegarTrocasLancadasUsuario($usuario, $grupo, $tipo, $status);
    $status = array(5);
    $trocasExcluidas = pegarTrocasLancadasUsuario($usuario, $grupo, $tipo, $status);

    include './htmls/lista_trocas_conc.php';
} else if ($item == "lancar_trocas_possiveis") {
    $grupo = $post['grupo'];
    $tipo = $post['tipo'];

    $resposta = lancarTrocasAutomaticamente($grupo, $tipo);
    echo json_encode(array('qtd' => $resposta));
}


////////////////////////////////////////////////////////////////////////////////////////////
include $sessao['raiz'] . 'closeConn.php';
///////////////////////////////////////////////////////////////////////////////////////////////
?>