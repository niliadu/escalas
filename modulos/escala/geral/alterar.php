<?php

$temModulo = true;
session_start();
include $_SESSION['raiz'] . "funcoes.php";

////////////////////////////////////////////////////////////////////////////////////////
$item = $post['item'];
if ($item == "select_soma") {
    $escalasSelAtual = $post['valor'] == null ? array() : $post['valor'];
    $grupo = $post['grupo'];

    $escalas = listaEscalas($grupo);
    foreach ($escalas as $e) {
        if ($e['soma_geral'] != in_array($e['id'], $escalasSelAtual)) {
            $mudar = !$e['soma_geral'];
            $resposta = atualizarEscSomaGeral($e['id'], $mudar);
        }
    }
    echo json_encode($resposta);
} else if ($item == "turnos_soma") {
    $turnoPrinc = $post['idTurnoPrinc'];
    $turnosSec = $post['turnosSec'] != "vazio" ? $post['turnosSec'] : array();
    $geral = $post['geral'];

    if ($geral) {
        $turnosSoma = turnosSomaQtdGeral($turnoPrinc);
    } else {
        $turnosSoma = turnosSomaQtd($turnoPrinc);
    }
    $turnosSomaTurnoId = array();
    foreach ($turnosSoma as $ts) {
        $turnosSomaTurnoId[] = $ts['id'];
    }
    //retira deleta os turnos que foram retirados da soma
    foreach ($turnosSoma as $ts) {
        if (!in_array($ts['id'], $turnosSec)) {
            $resposta = removerTurnoSoma($geral, $ts['ts_id']);
        }
    }
    //insere os turnos que foram adicionados à soma
    foreach ($turnosSec as $ts) {
        if (!in_array($ts, $turnosSomaTurnoId)) {
            $resposta = inserirTurnoSoma($geral, $turnoPrinc, $ts);
        }
    }
    echo json_encode($resposta);
} else if ($item == "atualizar_celula_qtd_esc") {
    $novosValoresCel = mb_strtoupper($post['novoValorCel'], "UTF-8");
    $turn_esc_id = $post['turn_esc_id'];
    $resposta = atualizarQtdEsc($novosValoresCel, $turn_esc_id);
    echo json_encode($resposta);
} else if ($item == "atualizar_celula_qtd") {
    $novosValoresCel = mb_strtoupper($post['novoValorCel'], "UTF-8");
    $turnoId = $post['turnoId'];
    $resposta = atualizarQtd($novosValoresCel, $turnoId);
    echo json_encode($resposta);
} else if ($item == "atualizar_celula") {
    $idsAnteriores = explode(",", $post['idsAnteriores']);
    $novosValoresCel = explode("-", mb_strtoupper($post['novoValorCel'], "UTF-8"));
    $svcAnt = pegarValoresSvcs($idsAnteriores);

    //pegar as legendas de turnos possiveis
    $escalaId = $post['escalaId'];
    $turnos = listaTurnosEscala($escalaId);
    $turnosLeg = array();
    foreach ($turnos as $t) {
        $turnosLeg[$t['id']] = $t['legenda'];
    }    

    //Verifica se todos os turnos inseridos são válidos ou é vazio
    $valido = array();

    if (sizeof($novosValoresCel) == 1 && $novosValoresCel[0] == "") {
        $valido[] = true;
    } else {
        foreach ($novosValoresCel as $i => $nvc) {
            if (!in_array($nvc, $turnosLeg)) {
                $valido[] = false;
            } else {
                $valido[] = true;
            }
        }
    }

    $resposta = "";
    if (!in_array(false, $valido)) {
        $remover = array();
        $adicionar = array();
        $svcAntComparacao = array();
        $operador = $post['operador'];
        $tipo = $post['tipo'];
        $dia = $post['dia'];        
        foreach ($svcAnt as $sa) {
            if ($sa['turno'] != null) {
                if (!in_array($sa['turno'], $novosValoresCel)) {
                    $remover[] = $sa['id'];
                }
                $svcAntComparacao[] = $sa['turno'];
            } else {
                $remover[] = $sa['id'];
            }
        }        
        foreach ($novosValoresCel as $nv) {
            if ($nv != "" && $nv != null && !in_array($nv, $svcAntComparacao)) {
                $adicionar[] = $nv;
            }
        }

        $infoAdicionar = array();
        foreach ($adicionar as $ad) {
            if (in_array($ad, $turnosLeg)) {
                $idTurno = array_search($ad, $turnosLeg);
                $infoAdicionar[] = array('dia' => $dia, 'turno' => $idTurno, 'operador' => $operador, "tipo_escala" => $tipo);
            }
        }        
        $resposta = atualizarDiaSvc($remover, $infoAdicionar);
    }
    echo json_encode($resposta);
} else if ($item == "remanejar") {
    $idSvc = $post['idSvc'];
    $diaEsc = $post['diaEsc'];
    $turnoEsc = $post['turnoEsc'];

    $resposta = remanejar($idSvc, $diaEsc, $turnoEsc);
    echo json_encode($resposta);
} else if ($item == "escalar") {
    $operador = $post['operador'];
    $tipoEscala = $post['tipoEscala'];
    $diaEsc = $post['diaEsc'];
    $turnoEsc = $post['turnoEsc'];

    $resposta = escalar($operador, $tipoEscala, $diaEsc, $turnoEsc);
    echo json_encode($resposta);
} else if ($item == "dispensar") {
    $idSvc = $post['idSvc'];
    $resposta = dispensar($idSvc);
    echo json_encode($resposta);
} else if ($item == "status_troca") {
    $grupo = $post['grupo'];
    $tipo = $post['tipo'];
    $valor = $post['valor'];
    $resposta = alterarStatusTroca($grupo, $tipo, $valor);
    echo json_encode($resposta);
} else if ($item == "status_publicada") {
    $id = $post['id'];
    $valor = $post['valor'];
    $tipo = $post['tipo'];
    $publicada = $post['publicada'];
    $resposta = alterarStatusPublicada($id, $valor, $tipo, $publicada);
    echo json_encode($resposta);
} else if ($item == "texto") {
    $dia = $post['dia'];
    $efetivo_mes = $post['efetivo_mes'];
    $texto = explode("-", mb_strtoupper($post['texto'], "UTF-8"));
    $textosAntes = explode("-", $post['textosAntes']);
    $textosIds = explode("-", $post['textosIds']);
    $tipoEscala = $post['tipoEscala'];

    $remover = array();
    $inserir = array();

    //verificando os textos que serão removidos
    foreach ($textosAntes as $i => $ver) {
        if (!in_array($ver, $texto)) {
            $remover[] = $textosIds[$i];
        }
    }
    //verificando os textos que serão inseridos
    foreach ($texto as $ver) {
        if (!in_array($ver, $textosAntes)) {
            $inserir[] = $ver;
        }
    }
    $resposta = alterarTexto($dia, $efetivo_mes, $tipoEscala, $remover, $inserir);
    echo json_encode($resposta);
}
////////////////////////////////////////////////////////////////////////////////////////////
include $sessao['raiz'] . 'closeConn.php';
///////////////////////////////////////////////////////////////////////////////////////////////
?>