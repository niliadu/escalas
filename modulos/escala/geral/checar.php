<?php

$temModulo = true;
session_start();
include $_SESSION['raiz'] . "funcoes.php";
////////////////////////////////////////////////////////////////////////////////////////
$item = $post['item'];

if ($item == 'tipos_escala_param') {
    $mes = $post['mes'];
    $ano = $post['ano'];
    $orgao = $post['orgao'];
    $unidade = $post['unidade'];

    $resposta = pegarGrupo($mes, $ano, $orgao, $unidade);
    if (sizeof($resposta) > 0) {
        $resposta = array("existe" => true, 'grupo' => $resposta[0]['id']);
    } else {
        $resposta = array("existe" => false);
    }
    echo json_encode($resposta);
} else if ($item == 'tipos_escala') {
    $grupo = $post['grupo'];

    $resposta = pegarTiposEscala($grupo);
    if (sizeof($resposta) > 0) {
        foreach ($resposta as $re) {
            $tipos[] = $re['tipo'];
        }
        $resposta = array("existe" => true, 'tipos' => $tipos);
    } else {
        $resposta = array("existe" => false);
    }
    echo json_encode($resposta);
} else if ($item == 'add_remanejamento_restricoes') {
    $turnoDisp = $post['turnoDisp'];
    $turnoEsc = $post['turnoEsc'];
    $idEscala = $post['idEscala'];
    $chMensal = explode(":", $post['chMensal']);
    $chMensal = ($chMensal[0] + ($chMensal[1] / 60));
    $operador = $post['operador'];
    $diaDisp = $post['diaDisp'];
    $grupo = $post['grupo'];
    $diaEsc = $post['diaEsc'];
    $tipoEscala = $post['tipoEscala'];


    //verifica se existe afastamento para o dia a ser escalado no remanejamento
    $verifica = verificarAfastamentoRemanejEscalacao($operador, $grupo, $diaEsc, "O Remanejamento não pode ser realizado");

    $afastado = $verifica['afastado'];
    $erro = $verifica['erro'];
    $textoErro = $verifica['textoErro'];

    if (!$afastado) {
        $resposta = pegarDadosGrupo($grupo);
        
        $mes = $resposta[0]['mes'];
        $ano = $resposta[0]['ano'];
        $qtdDiasMes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
        
        $orgaoID = $resposta[0]['orgao'];
        $unidadeID = $resposta[0]['unidade'];
        $usu = pegarUsuarioPeloEfetivoEscala($operador);
        $verHabilitacao = checaValidadeHabilitacao($usu[0]['usuario'], $orgaoID, $mes, $ano, $unidade);
        if (in_array($diaEsc, $verHabilitacao)) {
            $erro = true;
            $textoErro = "O Remanejamento não pode ser realizado pois o operador está com a Habilitação Vencida no dia $diaEsc/$mes/$ano.";
        } else {
            $textoErro = "O remanejamento infrigirá as seguintes regras:<br>";


            $efetivoMesID = pegarEfetivoMesPorEfetivoEscala($operador);
            ////verificando a carga horária

            $ch = checaCargaHoraria($turnoDisp, $turnoEsc, $chMensal, $efetivoMesID[0]['efetivo'], 'remanejamento', $usu[0]['usuario'], $mes, $ano);

            $erro = $ch['erro'];
            foreach ($ch['textoErro'] as $tx) {
                $textoErro .= "&nbsp;&nbsp;$tx<br>";
            }

            //verifica se a combinação de turnos é permitida
            $resposta = checaCombinacaoTurnosREDT($operador, $diaEsc, $turnoEsc, $grupo, $mes, $ano, $tipoEscala);
            if ($resposta['erro']) {
                $erro = true;
                $textoErro .= $resposta['textoErro'];
            }


            //verifica se dia tem mais de 2 turnos
            $turnosDiaEsc = pegarServicosOperadorDia($operador, $mes, $ano, $diaEsc, $tipoEscala);
            if (sizeof($turnosDiaEsc) >= 2) {//já tem 2 turnos no dia
                $erro = true;
                $textoErro .= "&nbsp;&nbsp;- Já existem 2 ou mais turnos escalados para o dia desejado;<br>";
            }

            //verificando folgas consecultivas
            $resposta = pegarDadosGrupo($grupo);
            $maxFolgasConsecutivas = $resposta[0]['qtd_folgas'];

            $resposta = pegarTipoTurno($turnoEsc);

            $anoMes = "$ano$mes";

            $usuario = pegarUsuarioPeloEfetivoEscala($operador);
            $usuario = $usuario[0]['usuario'];

            //verificando se ultrapassa o limite de folgas consecultivas
            $afastamentos = pegarAfastamentosNoMes(array($usuario), $mes, $ano);

            $afastMes = array();
            foreach ($afastamentos as $af) {
                $inicio = 1;
                $fim = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
                if (substr($af['inicio'], 0, 6) <= $anoMes && $anoMes >= substr($af['termino'], 0, 6)) {
                    if ($mes == substr($af['inicio'], 4, 2)) {
                        $inicio = substr($af['inicio'], 6, 2);
                    }
                    if ($mes == substr($af['termino'], 4, 2)) {
                        $fim = substr($af['termino'], 6, 2);
                    }
                }
                array_splice($afastMes, sizeof($afastMes), 0, range($inicio, $fim));
            }


            $verFolgas = array();
            $verFolgas = checaFolgasConsecultivas($operador, $mes, $ano, $tipoEscala, $maxFolgasConsecutivas, false, $diaDisp, $afastMes, $diaEsc);
            $text = "";
            if (sizeof($verFolgas['errosfolgas']) > 0) {
                $ini = $verFolgas['errosfolgas'][0];
                $final = $verFolgas['errosfolgas'][sizeof($verFolgas['errosfolgas']) - 1];
                $erro = true;
//                while ($ini - 1 > 0 && !in_array($ini - 1, $verFolgas['servicosOrdenados'])) {
//                    $ini--;
//                }
//                while ($final + 1 <= $qtdDiasMes && !in_array($final + 1, $verFolgas['servicosOrdenados'])) {
//                    $final++;
//                }
                $textoErro .= "&nbsp;&nbsp;- Quantidade de folgas consecultivas acima do permitido no período de $ini a $final;<br>";
            }
            $resposta['existe'] = $erro;
            $resposta['texto'] = $textoErro;

            //verificando influência do turno em relação ao parâmetro de pós-noturno
            $resposta = checaServicosNaoPosNoturno($operador, $turnoEsc, $diaEsc, $tipoEscala, $mes, $ano);
            if (!empty($resposta)) {
                $erro = true;
                $textoErro .= "&nbsp;&nbsp;- O dia " . $resposta['dia_anterior'] . " contém serviço noturno e o turno remanejado não é pós-noturno;<br>";
            }
            //verificando no caso do turno ser noturno
            $resposta = checaTurnoNoturno($operador, $turnoEsc, $diaEsc, $tipoEscala, $mes, $ano);
            if (!empty($resposta)) {
                $erro = true;
                $textoErro .= "&nbsp;&nbsp;- O dia " . $resposta['dia_posterior'] . " contém serviço que não é pós-noturno e o turno remanejado é noturno;<br>";
            }
        }
    }
    $resposta['afastamento'] = $afastado;
    $resposta['existe'] = $erro;
    $resposta['texto'] = $textoErro;

    echo json_encode($resposta);
} else if ($item == 'add_escalacao_restricoes') {
    $turnoEsc = $post['turnoEsc'];
    $idEscala = $post['idEscala'];
    $chMensal = explode(":", $post['chMensal']);
    $chMensal = ($chMensal[0] + ($chMensal[1] / 60));
    $operador = $post['operador'];
    $grupo = $post['grupo'];
    $diaEsc = $post['diaEsc'];
    $tipoEscala = $post['tipoEscala'];

    $erro = false;
    $textoErro = "A escalação infrigirá as seguintes regras:<br>";

    $resposta = mesAnoGrupo($grupo);
    $mes = $resposta[0]['mes'];
    $ano = $resposta[0]['ano'];
    $qtdDiasMes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

    $usu = pegarUsuarioPeloEfetivoEscala($operador);
    $efetivoMesID = pegarEfetivoMesPorEfetivoEscala($operador);
    ////verificando a carga horária
    $ch = checaCargaHoraria(null, $turnoEsc, $chMensal, $efetivoMesID[0]['efetivo'], 'escalacao', $usu[0]['usuario'], $mes, $ano);

    $erro = $ch['erro'];
    foreach ($ch['textoErro'] as $tx) {
        $textoErro .= "&nbsp;&nbsp;$tx<br>";
    }

    //verificar se existe mais de dois turnos no dia
    $turnosDiaEsc = pegarServicosOperadorDia($operador, $mes, $ano, $diaEsc, $tipoEscala);

    $turnosVerificar = array();
    foreach ($turnosDiaEsc as $r) {
        $turnosVerificar[] = $r['turno'];
    }
    $turnosVerificar[] = $turnoEsc;
    $resposta = pegarlistaCombinacoes($grupo);
    $combinacoes = array();
    foreach ($resposta as $r) {
        $combinacoes[] = array($r['turno1'], $r['turno2']);
    }
    $erroCombinacaoTurnos = checaCombinacoesTurnos($turnosVerificar, $combinacoes);
    if ($erroCombinacaoTurnos) {
        $erro = true;
        $textoErro .= "&nbsp;&nbsp;- Combinação de turnos não permitido;<br>";
    }

    if (sizeof($turnosDiaEsc) >= 2) {//já tem 2 turnos no dia
        $erro = true;
        $textoErro .= "&nbsp;&nbsp;- Já existem 2 ou mais turnos escalados para o dia desejado;<br>";
    }

    $resposta = pegarTipoTurno($turnoEsc);

    //verificando influência do turno em relação ao parâmetro de pós-noturno
    $resposta = checaServicosNaoPosNoturno($operador, $turnoEsc, $diaEsc, $tipoEscala, $mes, $ano);
    if (!empty($resposta)) {
        $erro = true;
        $textoErro .= "&nbsp;&nbsp;- O dia " . $resposta['dia_anterior'] . " contém serviço noturno e o turno remanejado não é pós-noturno;<br>";
    }
    //verificando no caso do turno ser noturno
    $resposta = checaTurnoNoturno($operador, $turnoEsc, $diaEsc, $tipoEscala, $mes, $ano);
    if (!empty($resposta)) {
        $erro = true;
        $textoErro .= "&nbsp;&nbsp;- O dia " . $resposta['dia_posterior'] . " contém serviço que não é pós-noturno e o turno remanejado é noturno;<br>";
    }

    $resposta['existe'] = $erro;
    $resposta['texto'] = $textoErro;

    echo json_encode($resposta);
} else if ($item == 'add_dispensa_restricoes') {
    $turnoDisp = $post['turnoDisp'];
    $idEscala = $post['idEscala'];
    $chMensal = explode(":", $post['chMensal']);
    $chMensal = ($chMensal[0] + ($chMensal[1] / 60));
    $operador = $post['operador'];
    $diaDisp = $post['diaDisp'];
    $grupo = $post['grupo'];
    $tipoEscala = $post['tipoEscala'];

    $erro = false;
    $textoErro = "A dispensa infrigirá as seguintes regras:<br>";

    $resposta = mesAnoGrupo($grupo);
    $mes = $resposta[0]['mes'];
    $ano = $resposta[0]['ano'];
    $qtdDiasMes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

    $usu = pegarUsuarioPeloEfetivoEscala($operador);
    $efetivoMesID = pegarEfetivoMesPorEfetivoEscala($operador);
    ////verificando a carga horária
    $ch = checaCargaHoraria($turnoDisp, null, $chMensal, $efetivoMesID[0]['efetivo'], 'dispensa', $usu[0]['usuario'], $mes, $ano);
    $erro = $ch['erro'];
    foreach ($ch['textoErro'] as $tx) {
        $textoErro .= "&nbsp;&nbsp;$tx<br>";
    }

    $resposta = pegarDadosGrupo($grupo);
    $maxFolgasConsecutivas = $resposta[0]['qtd_folgas'];

    $usuario = pegarUsuarioPeloEfetivoEscala($operador);
    $usuario = $usuario[0]['usuario'];

    $anoMes = "$ano$mes";
    //verificando se ultrapassa o limite de folgas consecultivas    
    $afastamentos = pegarAfastamentosNoMes(array($usuario), $mes, $ano);

    $afastMes = array();
    foreach ($afastamentos as $af) {
        $inicio = 1;
        $fim = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
        if (substr($af['inicio'], 0, 6) <= $anoMes && $anoMes >= substr($af['termino'], 0, 6)) {
            if ($mes == substr($af['inicio'], 4, 2)) {
                $inicio = substr($af['inicio'], 6, 2);
            }
            if ($mes == substr($af['termino'], 4, 2)) {
                $fim = substr($af['termino'], 6, 2);
            }
        }
        array_splice($afastMes, sizeof($afastMes), 0, range($inicio, $fim));
    }


    $verFolgas = array();
    $verFolgas = checaFolgasConsecultivas($operador, $mes, $ano, $tipoEscala, $maxFolgasConsecutivas, false, $diaDisp, $afastMes, null);
    $text = "";
    if (sizeof($verFolgas['errosfolgas']) > 0) {
        $ini = $verFolgas['errosfolgas'][0];
        $final = $verFolgas['errosfolgas'][sizeof($verFolgas['errosfolgas']) - 1];
        $erro = true;
//        while ($ini - 1 > 0 && !in_array($ini - 1, $verFolgas['servicosOrdenados'])) {
//            $ini--;
//        }
//        while ($final + 1 <= $qtdDiasMes && !in_array($final + 1, $verFolgas['servicosOrdenados'])) {
//            $final++;
//        }
        $textoErro .= "&nbsp;&nbsp;- Quantidade de folgas consecultivas acima do permitido no período de $ini a $final;<br>";
    }
    $resposta['existe'] = $erro;
    $resposta['texto'] = $textoErro;

    echo json_encode($resposta);
} elseif ($item == 'pegar_comentario') {
    $em_id = $post['em_id'];
    $tipo = $post['tipo'];
    $resposta = pegarComentario($em_id);
    if (sizeof($resposta) > 0) {
        $resposta = array("existe" => true, 'comentario' => $resposta[0]['texto']);
    } else {
        $respostax = array("existe" => false);
    }
    echo json_encode($resposta);
} else if ($item == 'add_texto') {
    $texto = mb_strtoupper($post['texto'], "UTF-8");
    $grupo = $post['grupo'];
    $erro = false;
    $textoErro = "O texto contém os seguintes erros:<br>";

    $turnos = array_column(listaTurnos($grupo), 'legenda');
    $risaer = array_column(pegarlistaRISAER($grupo), 'legenda');
    //verificando se tem alguma parte do texto igual ao turno ou servico risaer
    $verifica = explode("-", $texto);
    foreach ($verifica as $ver) {
        if (in_array($ver, $turnos)) {
            $erro = true;
            $textoErro .= "&nbsp;&nbsp;- O texto $ver já é uma legenda do turno da escala;<br>";
        }
        if (in_array($ver, $risaer)) {
            $erro = true;
            $textoErro .= "&nbsp;&nbsp;- O texto $ver já é uma legenda do serviço RISAER;<br>";
        }
    }

    $resposta['existe'] = $erro;
    $resposta['texto'] = $textoErro;
    echo json_encode($resposta);
} else if ($item == 'add_risaer') {
    $usuario = $post['usuario'];
    $dia = $post['dia'] < 10 ? "0" . $post['dia'] : $post['dia'];
    $servico = $post['servico'];
    $grupo = $post['grupo'];
    $erro = false;
    $textoErro = "O serviço RISAER a ser inserido contém os seguintes erros:<br>";

    ///////////////PREPARANDO OS SERVIÇOS DOS USUÁRIOS//////////////////////////
    $dadosGrupo = pegarDadosGrupo($grupo);
    $dadosGrupo = $dadosGrupo[0];
    $mesAnoArray[] = array($dadosGrupo['mes'], $dadosGrupo['ano']);

    $mesAnterior = $dadosGrupo['mes'] - 1 > 0 ? $dadosGrupo['mes'] - 1 : 12;
    $anoAnterior = $mesAnterior == 12 ? $dadosGrupo['ano'] - 1 : $dadosGrupo['ano'];
    $mesAnoArray[] = array($mesAnterior, $anoAnterior);

    $mesPosterior = $dadosGrupo['mes'] + 1 > 12 ? 1 : $dadosGrupo['mes'] + 1;
    $anoPosterior = $mesPosterior == 1 ? $dadosGrupo['ano'] + 1 : $dadosGrupo['ano'];
    $mesAnoArray[] = array($mesPosterior, $anoPosterior);

    $dadosGrupos = pegarGruposAtualAnteriorPosteriorEfetivoEscala(array($usuario), $mesAnoArray);
    $grupos = array_column($dadosGrupos, 'id');

    $svcs = servicoTotaisDasEscalas(array($usuario), $grupos);
    $svcs_risaer = servicoTotaisRISAER(array($usuario), $grupos);

    $svcs_ord = array();
    foreach ($svcs as $s) {
        $svcs_ord[$s['ano']][$s['mes']][$s['dia']][] = $s;
    }

    foreach ($svcs_risaer as $s) {
        $svcs_ord[$s['ano']][$s['mes']][$s['dia']][] = $s;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////
    $risaer = pegarRISAERid($servico);
    $risaer = $risaer[0];

    //verificar se o serviço RISAER adicionado infrige a regra de dias anteriores e posteriores     
    $risaer_inicio = strtotime($risaer['ano'] . "-" . $risaer['mes'] . "-$dia " . $risaer['inicio']);
    $risaer_fim = strtotime($risaer['ano'] . "-" . $risaer['mes'] . "-$dia " . $risaer['termino']);

    $inicio_livre = strtotime("-" . $risaer['intervalo_antes'] . " hours", $risaer_inicio);
    $fim_livre = strtotime("+" . $risaer['intervalo_depois'] . " hours", $risaer_fim);

    $inicio = date("Y-m-d H:i:s", $inicio_livre);
    $fim = date("Y-m-d H:i:s", $fim_livre);

    $risaer_inicio = date("Y-m-d H:i:s", $risaer_inicio);
    $risaer_fim = date("Y-m-d H:i:s", $risaer_fim);

    //pegar os serviços operacionais do operador
    $dia_inicio = (int) substr($inicio, 8, 2);
    $mes_inicio = (int) substr($inicio, 5, 2);
    $ano_inicio = (int) substr($inicio, 0, 4);

    $dia_fim = (int) substr($fim, 8, 2);
    $mes_fim = (int) substr($fim, 5, 2);
    $ano_fim = (int) substr($fim, 0, 4);

    //verificando se no dia limite inicial do risaer o serviço, caso tenha, é depois do horário limite

    if (array_key_exists($ano_inicio, $svcs_ord)) {
        if (array_key_exists($mes_inicio, $svcs_ord[$ano_inicio])) {
            if (array_key_exists($dia_inicio, $svcs_ord[$ano_inicio][$mes_inicio])) {
                $turnos = array();
                $erro_inicio = false;
                foreach ($svcs_ord[$ano_inicio][$mes_inicio][$dia_inicio] as $svc) {
                    $ver = date("Y-m-d H:i:s", strtotime($svc['ano'] . "-" . $svc['mes'] . "-" . $svc['dia'] . " " . $svc['termino']));
                    if ($ver >= $inicio) {
                        $erro = true;
                        $turnos[] = array_key_exists('turnos_leg', $svc) ? $svc['turnos_leg'] : $svc['risaer_leg'];
                        $erro_inicio = true;
                    }
                }
                if ($erro_inicio) {
                    $turnos = implode(", ", $turnos);
                    $svc = $svcs_ord[$ano_inicio][$mes_inicio][$dia_inicio][0];
                    $textoErro .= "&nbsp;&nbsp;- O(s) serviço(s) $turnos do dia " . $svc['dia'] . "/" . $svc['mes'] . "/" . $svc['ano'] . " infringe o descanso para o serviço RISAER;<br>";
                }
            }
        }
    }
    //verificando o restante dos dias
    $inicio_livre = strtotime("+1 days", $inicio_livre);
    $inicio = date("Y-m-d H:i:s", $inicio_livre);
    while ($inicio <= $risaer_inicio) {
        $dia_inicio = (int) substr($inicio, 8, 2);
        $mes_inicio = (int) substr($inicio, 5, 2);
        $ano_inicio = (int) substr($inicio, 0, 4);
        if (array_key_exists($ano_inicio, $svcs_ord)) {
            if (array_key_exists($mes_inicio, $svcs_ord[$ano_inicio])) {
                if (array_key_exists($dia_inicio, $svcs_ord[$ano_inicio][$mes_inicio])) {
                    $turnos = array();
                    foreach ($svcs_ord[$ano_inicio][$mes_inicio][$dia_inicio] as $svc) {
                        $turnos[] = array_key_exists('turnos_leg', $svc) ? $svc['turnos_leg'] : $svc['risaer_leg'];
                    }
                    $turnos = implode(", ", $turnos);
                    $erro = true;
                    $textoErro .= "&nbsp;&nbsp;- O(s) serviço(s) $turnos do dia " . $svc['dia'] . "/" . $svc['mes'] . "/" . $svc['ano'] . " infringe o descanso para o serviço RISAER;<br>";
                }
            }
        }
        $inicio_livre = strtotime("+1 days", $inicio_livre);
        $inicio = date("Y-m-d H:i:s", $inicio_livre);
    }

    //verificando se no dia limite final do risaer o serviço, caso tenha, é antes do horário limite
    if (array_key_exists($ano_fim, $svcs_ord)) {
        if (array_key_exists($mes_fim, $svcs_ord[$ano_fim])) {
            if (array_key_exists($dia_fim, $svcs_ord[$ano_fim][$mes_fim])) {
                $turnos = array();
                $erro_fim = false;
                foreach ($svcs_ord[$ano_fim][$mes_fim][$dia_fim] as $svc) {
                    $ver = date("Y-m-d H:i:s", strtotime($svc['ano'] . "-" . $svc['mes'] . "-" . $svc['dia'] . " " . $svc['inicio']));
                    if ($ver <= $fim) {
                        $erro = true;
                        $turnos[] = array_key_exists('turnos_leg', $svc) ? $svc['turnos_leg'] : $svc['risaer_leg'];
                        $erro_fim = true;
                    }
                }
                if ($erro_fim) {
                    $turnos = implode(", ", $turnos);
                    $svc = $svcs_ord[$ano_fim][$mes_fim][$dia_fim][0];
                    $textoErro .= "&nbsp;&nbsp;- O(s) serviço(s) $turnos do dia " . $svc['dia'] . "/" . $svc['mes'] . "/" . $svc['ano'] . " infringe o descanso para o serviço RISAER;<br>";
                }
            }
        }
    }

//    //verificando o restante dos dias
    $fim_livre = strtotime("-1 days", $fim_livre);
    $fim = date("Y-m-d H:i:s", $fim_livre);
    while ($fim > $risaer_fim) {
        $dia_fim = (int) substr($fim, 8, 2);
        $mes_fim = (int) substr($fim, 5, 2);
        $ano_fim = (int) substr($fim, 0, 4);
        if (array_key_exists($ano_fim, $svcs_ord)) {
            if (array_key_exists($mes_fim, $svcs_ord[$ano_fim])) {
                if (array_key_exists($dia_fim, $svcs_ord[$ano_fim][$mes_fim])) {
                    $turnos = array();
                    foreach ($svcs_ord[$ano_inicio][$mes_inicio][$dia_fim] as $svc) {
                        $turnos[] = array_key_exists('turnos_leg', $svc) ? $svc['turnos_leg'] : $svc['risaer_leg'];
                    }
                    $turnos = implode(", ", $turnos);
                    $erro = true;
                    $textoErro .= "&nbsp;&nbsp;- O(s) serviço(s) $turnos do dia " . $svc['dia'] . "/" . $svc['mes'] . "/" . $svc['ano'] . " infringe o descanso para o serviço RISAER;<br>";
                }
            }
        }
        $fim_livre = strtotime("-1 days", $fim_livre);
        $fim = date("Y-m-d H:i:s", $fim_livre);
    }

    $resposta['existe'] = $erro;
    $resposta['texto'] = $textoErro;
    echo json_encode($resposta);
}
////////////////////////////////////////////////////////////////////////////////////////////
include $sessao['raiz'] . 'closeConn.php';
///////////////////////////////////////////////////////////////////////////////////////////////
?>