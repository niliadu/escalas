
<?php

$temModulo = true;
session_start();
include $_SESSION['raiz'] . "funcoes.php";
////////////////////////////////////////////////////////////////////////////////////////
setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
$item = $post['item'];

if ($item == 'tipos_escala') {
    $tipos = $post['tipos'];
//escala definitiva
    if (is_array($tipos) && in_array(4, $tipos)) {
        $tiposNomes = array(1 => "RASCUNHO", 2 => "PREVISTA", 4 => "DEFINITIVA");
        $itemSelected = 4;
    }
//escala corrente
    else if (is_array($tipos) && in_array(3, $tipos)) {
        $tiposNomes = array(1 => "RASCUNHO", 2 => "PREVISTA", 3 => "CORRENTE", 7 => "CRIAR DEFINITIVA");
        $itemSelected = 3;
    }
//escala prevista
    else if (is_array($tipos) && in_array(2, $tipos)) {
        $tiposNomes = array(1 => "RASCUNHO", 2 => "PREVISTA", 6 => "CRIAR CORRENTE");
        $itemSelected = 2;
    }
//escala rascunho
    else {
        $tiposNomes = array(1 => "RASCUNHO", 5 => "CRIAR PREVISTA");
        $itemSelected = 1;
    }
    include 'htmls/lista_tipos.php';
} elseif ($item == 'turnos_soma_geral') {
    $grupo = $post['grupo'];
    $turnoPrinc = $post['turnoPrinc'];
    $geral = true;
//pegar todos os turnos
    $turnos = listaTurnos($grupo);
    $tSec = turnosSomaQtdGeral($turnoPrinc);
    $turnosSec = array();
    foreach ($tSec as $ts) {
        $turnosSec[] = $ts['id'];
    }
    include 'htmls/turnos_soma.php';
} elseif ($item == 'turnos_soma_escala') {
    $grupo = $post['grupo'];
    $turnoPrinc = $post['turnoPrinc'];
    $escalaId = $post['escalaId'];
    $geral = false;
//pegar todos os turnos
    $turnos = listaTurnosEscala($escalaId);
    $tSec = turnosSomaQtd($turnoPrinc);
    $turnosSec = array();
    foreach ($tSec as $ts) {
        $turnosSec[] = $ts['id'];
    }
    include 'htmls/turnos_soma.php';
} else if ($item == 'abas_escalas') {
    $grupo = $post['grupo'];
    $escalas = listaEscalas($grupo);
    include 'htmls/lista_escalas.php';
} else if ($item == 'escalas') {
    $tipo = $post['tipo'];
    $grupo = $post['grupo'];
    $escalaId = $post['id'];

    $editavel = (($tipo == 1 && $post['definitiva'] == 'false') || ($tipo == 2 && $post['definitiva'] == 'false')) ? true : false;
    $ChMinimaQuadrimestre = 120;
    $qtdMaxEtapas = 10;
    $saindoNoturnoFolga = false;
    $contarCargaHorariaRisaer = true;


    $efetivoMesdaEscala = pegarEfetivoMesDaEscala($escalaId);

    $usuariosEscala = array_column($efetivoMesdaEscala, 'usuario');
    /////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////SERVICOS DOS OPERADORES NO MES, NO MES ANTERIOR E NO MES SEGUINTE /////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////
    $dadosGrupo = pegarDadosGrupo($grupo);
    $mesAnoArray[] = array($dadosGrupo['mes'], $dadosGrupo['ano']);

    $mesAnterior = $dadosGrupo['mes'] - 1 > 0 ? $dadosGrupo['mes'] - 1 : 12;
    $anoAnterior = $mesAnterior == 12 ? $dadosGrupo['ano'] - 1 : $dadosGrupo['ano'];
    $mesAnoArray[] = array($mesAnterior, $anoAnterior);

    $doisMesesAnterior = $mesAnterior - 1 > 0 ? $mesAnterior - 1 : 12;
    $doisAnoAnterior = $doisMesesAnterior == 12 ? $anoAnterior - 1 : $anoAnterior;
    $mesAnoArray[] = array($doisMesesAnterior, $doisAnoAnterior);

    $tresMesAnterior = $doisMesesAnterior - 1 > 0 ? $doisMesesAnterior - 1 : 12;
    $tresAnoAnterior = $tresMesAnterior == 12 ? $doisAnoAnterior - 1 : $doisAnoAnterior;
    $mesAnoArray[] = array($tresMesAnterior, $tresAnoAnterior);

    $mesPosterior = $dadosGrupo['mes'] + 1 > 12 ? 1 : $dadosGrupo['mes'] + 1;
    $anoPosterior = $mesPosterior == 1 ? $dadosGrupo['ano'] + 1 : $dadosGrupo['ano'];
    $mesAnoArray[] = array($mesPosterior, $anoPosterior);

    $dadosGrupos = pegarGruposAtualAnteriorPosteriorEfetivoEscala($usuariosEscala, $mesAnoArray);
    $grupos = array_column($dadosGrupos, 'id');

    $resp = pegarDadosTipoEscala($grupo, $tipo);
    $tipoEscala = $resp[0]['id'];
    $statusTrocas = $resp[0]['trocas_liberadas'];

    $servicosOperacionais = servicoTotaisDasEscalas($grupos);
    $servicosRISAER = servicoTotaisRISAER($usuariosEscala, $grupos);
    $servicosInfo = servicoTotaisINFO($usuariosEscala, $grupos);


    //MAIOR TIPO DE ESCALA DE CADA ORGAO
    $outroOrgaoTipo = array();
    foreach ($servicosOperacionais as $s) {
        if ($s['orgao'] != $dadosGrupo['orgao'] || $s['unidade'] != $dadosGrupo['unidade']) {
            $outroOrgaoTipo[$s['unidade']][$s['orgao']][] = $s['tipo_escala'];
        } else {
            $esteOrgaoTipo[$s['mes']][] = $s['tipo_escala'];
        }
    }
    foreach ($servicosRISAER as $s) {
        if ($s['orgao'] != $dadosGrupo['orgao'] || $s['unidade'] != $dadosGrupo['unidade']) {
            $outroOrgaoTipo[$s['unidade']][$s['orgao']][] = $s['tipo_escala'];
        } else {
            $esteOrgaoTipo[$s['mes']][] = $s['tipo_escala'];
        }
    }
    foreach ($servicosInfo as $s) {
        if ($s['orgao'] != $dadosGrupo['orgao'] || $s['unidade'] != $dadosGrupo['unidade']) {
            $outroOrgaoTipo[$s['unidade']][$s['orgao']][] = $s['tipo_escala'];
        } else {
            $esteOrgaoTipo[$s['mes']][] = $s['tipo_escala'];
        }
    }
    foreach ($outroOrgaoTipo as $uID => $orgaos) {
        foreach ($orgaos as $id => $o) {
            $outroOrgaoTipo[$uID][$id] = max($o);
        }
    }
    foreach ($esteOrgaoTipo as $i => $o) {
        $esteOrgaoTipo[$i] = max($o);
    }
    //////////////////////////////////////////////////////////////////////////////
    ///AFASTAMENTOS DOS OPERADORES  /////////////////////////////////////////////
    $resp = pegarAfastamentosNoMes($usuariosEscala, $dadosGrupo['mes'], $dadosGrupo['ano']);
    foreach ($resp as $r) {
        $afastamentosNoMes[$r['usuario']][] = array('tipo' => $r['tipo'], 'inicio' => $r['inicio'], 'termino' => $r['termino']);
    }

    $r = informacoesEscala($escalaId, $tipo, $grupo);
    $dadosEscala = $r[0];

    $escalaLeg = $dadosEscala['legenda'];

    $mes = $dadosGrupo['mes'];
    $ano = $dadosGrupo['ano'];
    $qtdDiasMes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
    $maxFolgasConsecutivas = $dadosGrupo['qtd_folgas'];
    $orgaoID = $dadosGrupo['orgao'];
    $unidadeID = $dadosGrupo['unidade'];

    $combinacoesTurnos = array();
    foreach ($r as $c) {
        $combinacoesTurnos[] = array($c['turno1'], $c['turno2']);
    }

    $nomeGuerraOperadores = pegarNomeGuerraOperadores($usuariosEscala);

    $comentarios = pegarComentariosUsuarios($usuariosEscala, $grupo);

    
    $validadeHabilitacoesUsuarios = verificaHabilitacaoValidaUsuarios($usuariosEscala, $orgaoID, $ano, $mes, $unidadeID);
    /////////////////////////////////////////////////////////////////////////////////////////////
    //////                 QUANTIDADES GERAL            ///////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////
    $colunas = array();
    $colunas['1'] = array('valor' => "QTD", 'editavel' => false, 'classes' => "azul-primary");
    $colunas['2'] = array('valor' => "TURNOS", 'editavel' => false, 'classes' => "azul-primary");

    for ($i = 1; $i <= $qtdDiasMes; $i++) {
        $dia = "d$i";
        $colunas[$dia] = array('valor' => $i, 'editavel' => false, 'classes' => "azul-primary");
    }
    $colunas['qtd_svc'] = array('valor' => "GERAL", 'editavel' => false, 'tipo' => "texto", 'classes' => "azul-primary");
    $linhas[] = $colunas;
    //pegar as escalas que serão somadas 
    $escalas = listaEscalas($grupo);
    $escalasSoma = array();
    foreach ($escalas as $e) {//pega apenas as escalas que deve-se fazer a soma da quatindade de serviços
        if ($e['soma_geral']) {
            $escalasSoma[] = $e['id'];
        }
    }
    if (empty($escalasSoma)) {
        $escalasSoma[] = 0; //garante que algum valor esta sendo passado para a busca de somatorio, como nao existe id = 0 na tabela ela não irá retornar nada
    }

    $resp = listaTurnosGrupoComTurnosSecundariosDaSoma($grupo);
    $turnos = array();
    foreach ($resp as $r) {

        $turnos[$r['id']]['id'] = $r['id'];
        $turnos[$r['id']]['legenda'] = $r['legenda'];
        $turnos[$r['id']]['nome'] = $r['nome'];
        $turnos[$r['id']]['ref_soma'] = $r['ref_soma_geral'];

        $r['ts_id'] != null ? $turnos[$r['id']]['turnos_sec'][] = array('id' => $r['ts_id'], 'legenda' => $r['ts_leg'], 'nome' => $r['ts_nome']) : null;
    }

    $qtdTurnosGeral = sizeof($turnos);

    $j = 0;
    foreach ($turnos as $t) {
        $qtdRef = $t['ref_soma'];

        $turnosSec = array_key_exists('turnos_sec', $t) ? $t['turnos_sec'] : array();
        $turnosSecId = array_column($turnosSec, 'id');
        $turnosSecLeg = array_column($turnosSec, 'legenda');

        $tunosContagemId = array_merge(array($t['id']), $turnosSecId);
        $tunosContagemLeg = array_merge(array($t['legenda']), $turnosSecLeg);


        $colunas = array();
        $colunas['1'] = array("valor" => "$qtdRef", "valorEd" => "$qtdRef", "editavel" => true, "classes" => "cinza-fraco soNumero", "turno_id" => $t['id'], "idEscala" => $escalaId, "legEscala" => $escalaLeg);
        $colunas['2'] = array('valor' => implode("-", $tunosContagemLeg), 'editavel' => false, 'classes' => "cinza-medio", "atr" => array("onclick" => "turnosSoma(true," . $t['id'] . ");"));

        $qtdSvcPorDia = qtdOprTurnoDiasGeral($tunosContagemId, $servicosOperacionais, $qtdDiasMes, $escalasSoma, $tipo);
        for ($i = 1; $i <= $qtdDiasMes; $i++) {
            $qtd = $qtdSvcPorDia[$i];

            if ($qtd > $qtdRef) {
                $cor = "azul";
            } else if ($qtd == $qtdRef) {
                $cor = "verde";
            } else if ($qtd < $qtdRef) {
                $cor = "vermelho";
            }
            $dia = ("d$i");
            $colunas[$dia] = array("valor" => "$qtd", "editavel" => false, "classes" => $cor);
        }

        if ($j == 0) {
            $select = "<div style='height:80;'>"//necessario para garantir a vizualizacao correta do nomes com o cabecalho congelado
                    . "<select id='select_esc_soma_geral' data-width='200' title='Selecione as escalas' multiple escid='$escalaId' escleg='$escalaLeg'>";
            foreach ($escalas as $e) {
                $selected = (in_array($e['id'], $escalasSoma)) ? "selected" : "";
                $select .= "<option value='" . $e['id'] . "' title='" . $e['legenda'] . "' $selected>" . $e['nome'] . "</option>";
            }
            $select .= "</select>";
            if ($tipo != 1 && $tipo != 4) {
                $select .= "<h6>";
                if ($statusTrocas == 0) {
                    $select .= "<div class='btn-xs btn-danger botao-trocas' "
                            . "onclick='modificarStatusTroca($grupo,$tipo,1,$escalaId,\"$escalaLeg\",this);'>TROCAS NÃO LIBERADAS</div></h6></div>";
                } else {
                    $select .= "<div class='btn-xs btn-success  botao-trocas' "
                            . "onclick='modificarStatusTroca($grupo,$tipo,0,$escalaId,\"$escalaLeg\",this);'>TROCAS LIBERADAS</div></h6></div>";
                }
            }
            $colunas['qtd_svc'] = array("valor" => "", "editavel" => false, "tipo" => "", "classes" => "y-overflow", "html" => $select);
        }
        $linhas[] = $colunas;
        $j++;
    }
    /////////////////////////////////////////////////////////////////////////////////////////////
    //////                 FIM DAS QUANTIDADES GERAL           ///////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////
    //////           LINHA SEPARACAO       ///////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////

    $colunas = array();
    $colunas['1'] = array('valor' => "", 'editavel' => false);
    $linhas[] = $colunas;

    $colunas = array();
    $colunas['1'] = array('valor' => "QTD", 'editavel' => false, 'classes' => "azul-primary");
    $colunas['2'] = array('valor' => "TURNOS", 'editavel' => false, 'classes' => "azul-primary");

    for ($i = 1; $i <= $qtdDiasMes; $i++) {
        $dia = "d$i";
        $colunas[$dia] = array('valor' => $i, 'editavel' => false, 'classes' => "azul-primary");
    }

    switch ($tipo) {
        case '1':
            $nomeTipo = 'RASCUNHO';
            break;
        case '2':
            $nomeTipo = 'PREVISTA';
            break;
        case '3':
            $nomeTipo = 'CORRENTE';
            break;
        case '4':
            $nomeTipo = 'DEFINITIVA';
            break;
    }
    $dataNome = mb_strtoupper(date("M/Y", strtotime("$ano-$mes-01")), "UTF-8");
    $colunas['qtd_svc'] = array('valor' => $escalaLeg . " - $nomeTipo de $dataNome", 'editavel' => false, 'tipo' => "texto", 'classes' => "azul-primary");
    $linhas[] = $colunas;
    /////////////////////////////////////////////////////////////////////////////////////////////
    //////          FIM LINHA SEPARACAO      ///////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////
    //////                 QUANTIDADES            ///////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////
    $resp = listaTurnosEscalaComTurnosSecundariosDaSoma($escalaId);
    $turnos = array();
    foreach ($resp as $r) {

        $turnos[$r['te_id']]['te_id'] = $r['te_id'];
        $turnos[$r['te_id']]['escala'] = $r['escala'];
        $turnos[$r['te_id']]['id'] = $r['id'];
        $turnos[$r['te_id']]['legenda'] = $r['legenda'];
        $turnos[$r['te_id']]['nome'] = $r['nome'];
        $turnos[$r['te_id']]['ref_soma'] = $r['ref_soma'];

        $r['ts_id'] != null ? $turnos[$r['te_id']]['turnos_sec'][] = array('id' => $r['ts_id'], 'legenda' => $r['ts_leg'], 'nome' => $r['ts_nome']) : null;
    }
    $k = 0;
    foreach ($turnos as $j => $t) {
        $qtdRef = $t['ref_soma'];

        $turnosSec = array_key_exists('turnos_sec', $t) ? $t['turnos_sec'] : array();
        $turnosSecId = array_column($turnosSec, 'id');
        $turnosSecLeg = array_column($turnosSec, 'legenda');

        $tunosContagemId = array_merge(array($t['id']), $turnosSecId);
        $tunosContagemLeg = array_merge(array($t['legenda']), $turnosSecLeg);

        ////////////////////////////////////////////////
        $turnos[$j]['contagemId'] = $tunosContagemId; //serao utilizados mais à frente
        $turnos[$j]['contagemLeg'] = $tunosContagemLeg;
        ///////////////////////////////////////////////////

        $colunas = array();
        $colunas['1'] = array("valor" => "$qtdRef", "valorEd" => "$qtdRef", "editavel" => true, "classes" => "cinza-fraco", "te_id" => $t['te_id'], "idEscala" => $escalaId, "legEscala" => $escalaLeg);
        $colunas['2'] = array('valor' => implode("-", $tunosContagemLeg), 'editavel' => false, 'classes' => "cinza-medio", "atr" => array("onclick" => "turnosSoma(false," . $t['te_id'] . "," . $escalaId . ");"));

        $qtdSvcPorDia = qtdOprTurnoDias($tunosContagemId, $servicosOperacionais, $qtdDiasMes, $escalaId, $tipo);
        for ($i = 1; $i <= $qtdDiasMes; $i++) {
            $qtd = $qtdSvcPorDia[$i];

            if ($qtd > $qtdRef) {
                $cor = "azul";
            } else if ($qtd == $qtdRef) {
                $cor = "verde";
            } else if ($qtd < $qtdRef) {
                $cor = "vermelho";
            }
            $dia = ("d$i");
            $colunas[$dia] = array("valor" => "$qtd", "editavel" => false, "classes" => $cor);
        }
        if ($k == 0) {

            $idDiv = $dadosEscala['id'] . "_" . $dadosEscala['legenda'] . "_" . $tipo;
            if ($dadosEscala['publicada'] == 2) {
                $html = "<div id='" . $idDiv . "' class='btn-xs btn-success' "
                        . "onclick='modificarPublicadaEscala($escalaId,\"$escalaLeg\",$tipo," . $dadosEscala['publicada'] . ",1,this);'>PUBLICADA</div>";
            } else if ($dadosEscala['publicada'] == 1) {
                $html = "<div id='" . $idDiv . "' class='btn-xs btn-danger' "
                        . "onclick='modificarPublicadaEscala($escalaId,\"$escalaLeg\",$tipo," . $dadosEscala['publicada'] . ",2,this);'>NÃO PUBLICADA</div>";
            } else {
                $html = "<div id='" . $idDiv . "' class='btn-xs btn-danger' "
                        . "onclick='modificarPublicadaEscala($escalaId,\"$escalaLeg\",$tipo,-1,2,this);'>NÃO PUBLICADA</div>";
            }
            $colunas['qtd_svc'] = array('valor' => "", 'editavel' => false, 'tipo' => "btn", 'classes' => "", "html" => $html, "idDiv" => $idDiv);
        }
        $k++;
        $linhas[] = $colunas;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////
    //////           FIM DAS QUANTIDADES          ///////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////
    //////           CABEÇALHO                    ///////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////
    $colunas = array();
    $colunas['1'] = array('valor' => "LEG", 'editavel' => false, 'classes' => "azul-primary");
    $colunas['2'] = array('valor' => "NOME", 'editavel' => false, 'classes' => "azul-primary");
    for ($i = 1; $i <= $qtdDiasMes; $i++) {
        $dia = "d$i";
        $diaSemanaExtenso = mb_strtoupper(strftime("%a", strtotime("$mes/$i/$ano")), "UTF-8");

        $diaAbrev = substr($diaSemanaExtenso, 0, 1);

        switch ($diaSemanaExtenso) {
            case "SÁB":
                $intensidade = true;
                break;
            case "DOM":
                $intensidade = true;
                break;
            default :
                $intensidade = false;
                break;
        }
        $intensidadeDiaMes[$i] = $intensidade; //servirá para determinar se o dia na linha do operador é um fds ou não

        $colunas[$dia] = array('valor' => $diaAbrev, 'editavel' => false, 'classes' => "cinza-" . ($intensidade ? "forte" : "fraco"));
    }
    $colunas['qtd_svc'] = array('valor' => "QTD", 'editavel' => false, 'tipo' => "texto", 'classes' => "azul-primary");
    $colunas['qt1'] = array('valor' => "TURNOS", 'editavel' => false, 'tipo' => "texto", 'classes' => "azul-info");
    $colunas['ch_mes'] = array('valor' => "CARGA HORÁRIA", 'editavel' => false, 'tipo' => "texto", 'classes' => "azul-info");
    $linhas[] = $colunas;

    /////////////////////////////////////////////////////////////////////////////////////////////
    //////           FIM DO CABEÇALHO             ///////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////
    //////           EQUIPES OPERACIONAIS         ///////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////
    $legendasDaEscala = array_column($efetivoMesdaEscala, 'legenda');
    $equipes = array();
    foreach ($legendasDaEscala as $le) {
        $equipes[] = substr($le, 0, 1);
    }
    $equipes = sort(array_unique($equipes));

    //cabeçalho das equipes
    $colunas = array();
    $colunas['1'] = array('valor' => "LEG", 'editavel' => false, 'classes' => "azul-primary");
    $colunas['2'] = array('valor' => "NOME", 'editavel' => false, 'classes' => "azul-primary");

    for ($i = 1; $i <= $qtdDiasMes; $i++) {
        $dia = "d$i";
        $colunas[$dia] = array('valor' => $i, 'editavel' => false, 'classes' => "azul-primary");
    }
    $colunas['qtd_svc'] = array('valor' => "SVC", 'editavel' => false, 'tipo' => "texto", 'classes' => "azul-primary");
    $i = 1;
    foreach ($turnos as $t) {
        $turnosC = "qt$i";
        $i++;

        $colunas[$turnosC] = array('valor' => implode("-", $t['contagemLeg']), 'editavel' => true, 'classes' => "azul-primary rotulo");
    }

    $colunas['ch_mes'] = array('valor' => "MENSAL", 'editavel' => false, 'classes' => "azul-primary");
    $colunas['ch_cht'] = array('valor' => "CHT", 'editavel' => false, 'classes' => "azul-primary");

    $cabEquipe = $colunas;
    /////////////////////////////////////////////////////////////////////////////////////////////
    //////          FIM EQUIPES OPERACIONAIS      ///////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////
    //////           SERVICOS OPERADORES          ///////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////

    $operadores = array();
    $operAnoMesDia = array();
    foreach ($efetivoMesdaEscala as $e) {
        $qtdChm = 0; //guarda a quantidade de carga horária mensal
        $qtdCHT = 0; //guarda a carga horária do quadrimestre
        $operadores[$e['em_id']]['usuario'] = $e['usuario'];
        $operadores[$e['em_id']]['legenda'] = $e['legenda'];
        $operadores[$e['em_id']]['manutencao'] = $e['manutencao'];
        $operadores[$e['em_id']]['ee_id'] = $e['ee_id'];
        foreach ($servicosOperacionais as $s) {
            if ($s['usuario'] == $e['usuario']) {
                if (
                        (
                        $s['orgao'] == $dadosGrupo['orgao'] &&
                        $s['unidade'] == $dadosGrupo['unidade'] &&
                        $s['mes'] == $dadosGrupo['mes'] &&
                        $s['ano'] == $dadosGrupo['ano'] &&
                        $s['tipo_escala'] == $tipo
                        ) ||
                        (
                        ($s['orgao'] != $dadosGrupo['orgao'] || $s['unidade'] != $dadosGrupo['unidade']) &&
                        $s['mes'] == $dadosGrupo['mes'] &&
                        $s['ano'] == $dadosGrupo['ano'] &&
                        $s['tipo_escala'] == $outroOrgaoTipo[$s['unidade']][$s['orgao']]
                        )
                ) {
                    //calculando a carga horária mensal
                    $midnight = strtotime("00:00:00");
                    $turno_inicio = strtotime($s['inicio']) - $midnight;
                    $turno_termino = strtotime($s['termino']) - $midnight;
                    $total = $turno_termino - $turno_inicio;
                    $qtdChm = $total > 0 ? $qtdChm + $total : $qtdChm + $total + (86400);
                    $qtdCHT = $total > 0 ? $qtdCHT + $total : $qtdCHT + $total + (86400);
                    //estruturando os serviços
                    $operadores[$e['em_id']]['servicos'][$s['dia']]['operacionais'][] = array(
                        'svc_id' => $s['svc_id'],
                        'turno_id' => $s['turno_id'],
                        'turno_leg' => $s['turnos_leg'],
                        'ee_id' => $s['ee_id'],
                        'esc_id' => $s['esc_id'],
                        'orgao' => $s['orgao'],
                        'unidade' => $s['unidade'],
                        'etapa' => $s['etapa'],
                        'pos_noturno' => $s['pos_noturno'],
                        'periodo' => $s['periodo']
                    );
                } else if (
                        $s['orgao'] == $dadosGrupo['orgao'] &&
                        $s['unidade'] == $dadosGrupo['unidade'] &&
                        ehXMesesAnteriores($dadosGrupo['mes'], $dadosGrupo['ano'], $s['mes'], $s['ano']) &&
                        $s['tipo_escala'] == $esteOrgaoTipo[$s['mes']]
                ) {
                    $midnight = strtotime("00:00:00");
                    $turno_inicio = strtotime($s['inicio']) - $midnight;
                    $turno_termino = strtotime($s['termino']) - $midnight;
                    $total = $turno_termino - $turno_inicio;
                    $qtdCHT = $total > 0 ? $qtdCHT + $total : $qtdCHT + $total + (86400);
                }
                $operAnoMesDia[$e['usuario']][$s['ano']][$s['mes']][$s['dia']][] = $s;
            }
        }
        foreach ($servicosRISAER as $s) {
            if ($s['usuario'] == $e['usuario']) {
                if (
                        (
                        $s['orgao'] == $dadosGrupo['orgao'] &&
                        $s['unidade'] == $dadosGrupo['unidade'] &&
                        $s['mes'] == $dadosGrupo['mes'] &&
                        $s['ano'] == $dadosGrupo['ano'] &&
                        $s['tipo_escala'] == $tipo
                        ) ||
                        (
                        ($s['orgao'] != $dadosGrupo['orgao'] || $s['unidade'] != $dadosGrupo['unidade']) &&
                        $s['mes'] == $dadosGrupo['mes'] &&
                        $s['ano'] == $dadosGrupo['ano'] &&
                        $s['tipo_escala'] == $outroOrgaoTipo[$s['unidade']][$s['orgao']]
                        )
                ) {
                    //inserindo a carga horária do RISAER no mensal
                    if ($contarCargaHorariaRisaer) {
                        $midnight = strtotime("00:00:00");
                        $risaer_inicio = strtotime($s['inicio']) - $midnight;
                        $risaer_termino = strtotime($s['termino']) - $midnight;
                        $total = $risaer_termino - $risaer_inicio;
                        $qtdChm = $total > 0 ? $qtdChm + $total : $qtdChm + $total + (86400);
                        $qtdChm = $s['mais_q_24h'] ? $qtdChm + (86400) : $qtdChm;
                    }
                    //estruturando os serviços RISAER
                    $operadores[$e['em_id']]['servicos'][$s['dia']]['risaer'][] = array(
                        'svcr_id' => $s['svcr_id'],
                        'risaer_id' => $s['risaer_id'],
                        'risaer_leg' => $s['risaer_leg'],
                        'em_id' => $s['em_id'],
                        'orgao' => $s['orgao'],
                        'unidade' => $s['unidade'],
                        'tipo_etapa' => $s['tipo_etapa']
                    );
                }
            }
        }
        foreach ($servicosInfo as $s) {
            if ($s['usuario'] == $e['usuario']) {
                if (
                        (
                        $s['orgao'] == $dadosGrupo['orgao'] &&
                        $s['unidade'] == $dadosGrupo['unidade'] &&
                        $s['mes'] == $dadosGrupo['mes'] &&
                        $s['ano'] == $dadosGrupo['ano'] &&
                        $s['tipo_escala'] == $tipo
                        ) ||
                        (
                        ($s['orgao'] != $dadosGrupo['orgao'] || $s['unidade'] != $dadosGrupo['unidade']) &&
                        $s['mes'] == $dadosGrupo['mes'] &&
                        $s['ano'] == $dadosGrupo['ano'] &&
                        $s['tipo_escala'] == $outroOrgaoTipo[$s['unidade']][$s['orgao']]
                        )
                ) {
                    //estruturando os turnos
                    $operadores[$e['em_id']]['servicos'][$s['dia']]['info'][] = array(
                        'info_id' => $s['info_id'],
                        'texto' => $s['texto'],
                        'em_id' => $s['em_id'],
                        'orgao' => $s['orgao'],
                        'unidade' => $s['unidade']
                    );
                }
            }
        }
        $operadores[$e['em_id']]['carga_mensal'] = floor($qtdChm / 3600) . ":" . floor(($qtdChm / 60) % 60);
        $operadores[$e['em_id']]['CHT'] = floor($qtdCHT / 3600) . ":" . floor(($qtdCHT / 60) % 60);
    }

    /////////////////////////////////////////////////////////////////////////////////////////////
    //////          FIM SERVICOS OPERADORES       ///////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////
    //////           LINHA DE SERVICOS OPERADOREs          //////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////
    $j = -1;
    $equipeAtual = '';
    foreach ($operadores as $emId => $opr) {
        $j++;

        $afastOpr = $afastamentosNoMes[$opr['usuario']];

        $nomeGuerra = $nomeGuerraOperadores[$opr['usuario']];

        $corLinha = (($j % 2 == 0) ? "cinza" : "amarelo");

        //VERIFICA SE MODIFICOU A EQUIPE E ADICIONA O CABEÇALHO
        if (substr($opr['legenda'], 0, 1) != $equipeAtual) {
            $equipeAtual = substr($opr['legenda'], 0, 1);
            $linhas[] = $cabEquipe;
        }
        //////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////
        ////QTD DE ETAPAS//////////////////////////////////////////
        $colunas = array();
        $qtdEtapas = qtdEtapasPorServicos($opr['servicos']);
        if ($qtdEtapas > $qtdMaxEtapas) {
            $celCor = "vermelho";
        } else {
            $celCor = $corLinha . "-medio";
        }
        $colunas['1'] = array('valor' => $opr['legenda'], 'editavel' => false, 'classes' => $celCor);
        ///////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////
        // COMENTARIOS ///////////////////////////////////////////////////////
        $comentario = $comentarios[$opr['usuario']];

        $showComments = $comentario != null ? "show-comments" : "";
        $mostrarBtnAfastamentos = $afastOpr != null ? 'sim' : 'nao';
        $html = "<div "
                . "id='" . $emId . "_" . $tipo . "_" . $escalaLeg . "' "
                . "class='comments $showComments' "
                . "data-type='textarea' "
                . "data-title='Inserir Comentário' "
                . "data-placement='top' "
                . "data-em_id='$emId' "
                . "data-tipo='$tipo' "
                . "data-escala='$escalaLeg' "
                . "data-mostrar_afast='$mostrarBtnAfastamentos'>"// feita modificacao direto no script do plugin para mostar este botao
                . "$nomeGuerra"
                . "</div>";
        $colunas['2'] = array(
            'valor' => "",
            'editavel' => false,
            'tipo' => "btn",
            'classes' => $corLinha . "-medio",
            'html' => $html,
            "atr" => array(
                "onclick" => "chamarComentario($emId,$tipo,\"" . $escalaLeg . "\");",
                "valor" => $nomeGuerra)
        );

        ////////////verifica se exista habilitação válida no órgão
        $erroHab = checaValidadeHabilitacaoUsuario($validadeHabilitacoesUsuarios[$opr['usuario']], $mes, $ano);


        ////verifica quantidade de folgas consecultivas///////////////////////////////

        $afastMes = pegarDiasAfastamentosMes($afastOpr, $ano, $mes);

        if (!$opr['manutencao']) {
            $errosfolgas = checaFolgasConsecultivasGeral($opr['servicos'], $mes, $ano, $maxFolgasConsecutivas, $afastMes, $erroHab);
            $errosfolgas = $errosfolgas['errosfolgas'];
        } else {
            $errosfolgas = array();
        }

        $errosPosNoturno = array();

        foreach ($opr['servicos'] as $i => $s) {
            if (array_key_exists('operacionais', $s)) {
                foreach ($s['operacionais'] as $turno) {
                    //checa se os serviços que não são pós-noturnos tem um serviço noturno antes
                    if ($turno['pos_noturno'] == 0) {
                        $diaMesAnoAnt = diaMesAnoAnterior($i, $mes, $ano);

                        if (array_key_exists($opr['usuario'], $operAnoMesDia)) {
                            $svcAnoMesDia = $operAnoMesDia[$opr['usuario']];

                            if (array_key_exists($diaMesAnoAnt['ano'], $svcAnoMesDia)) {
                                $svcMesDia = $svcAnoMesDia[$diaMesAnoAnt['ano']];

                                if (array_key_exists($diaMesAnoAnt['mes'], $svcMesDia)) {
                                    $svcDia = $svcMesDia[$diaMesAnoAnt['mes']];

                                    if (array_key_exists($diaMesAnoAnt['dia'], $svcDia)) {
                                        $svc = $svcDia[$diaMesAnoAnt['dia']];
                                        foreach ($svc as $turno) {
                                            if ($turno['periodo'] == 2) {
                                                array_splice($errosPosNoturno, sizeof($errosPosNoturno), 0, $i);
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //Checa se os serviços que são noturnos tem um serviço que não é pós-noturno depois
                    if ($turno['periodo'] == 2) {
                        $diaMesAnoDep = diaMesAnoDepois($i, $mes, $ano);

                        if (array_key_exists($opr['usuario'], $operAnoMesDia)) {
                            $svcAnoMesDia = $operAnoMesDia[$opr['usuario']];

                            if (array_key_exists($diaMesAnoDep['ano'], $svcAnoMesDia)) {
                                $svcMesDia = $svcAnoMesDia[$diaMesAnoDep['ano']];

                                if (array_key_exists($diaMesAnoDep['mes'], $svcMesDia)) {
                                    $svcDia = $svcMesDia[$diaMesAnoDep['mes']];

                                    if (array_key_exists($diaMesAnoDep['dia'], $svcDia)) {
                                        $svc = $svcDia[$diaMesAnoDep['dia']];
                                        foreach ($svc as $turno) {
                                            if ($turno['pos_noturno'] == 0) {
                                                array_splice($errosPosNoturno, sizeof($errosPosNoturno), 0, $i);
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $qtd = 0; //guarda a quantidade de serviços geral do operador        

        for ($i = 1; $i <= $qtdDiasMes; $i++) {

            //$editavel = ($tipo == 1 || $tipo == 2) ? true : false;
            $editavel = (($tipo == 1 && $post['definitiva'] == 'false') || ($tipo == 2 && $post['definitiva'] == 'false')) ? true : false;

            //            //verifica se o dia é um dia de afastamento
            $diaS = $i < 10 ? "0$i" : $i;
            $anoMesDia = "$ano$mes$diaS";

            $diaAfastamento = false;
            foreach ($afastOpr as $af) {
                if ($af['inicio'] <= $anoMesDia && $af['termino'] >= $anoMesDia) {
                    $diaAfastamento = true;
                    $editavel = false;
                    break;
                }
            }
            //checar existência de turnos, risaer e info em outras escalas e órgãos
            $servicosDia = array_key_exists($i, $opr['servicos']) ? $opr['servicos'][$i] : array();

            $existe_turno_outro_orgao = false;
            $existe_turno_outra_escala = false;
            $existe_turno_nesta_escala = false;
            $existe_risaer_outro_orgao = false;
            $existe_risaer_neste_orgao = false;
            $existe_info_outro_orgao = false;
            $existe_info_neste_orgao = false;

            if (array_key_exists('operacionais', $servicosDia)) {
                foreach ($servicosDia['operacionais'] as $svc_op) {
                    if ($svc_op['orgao'] != $orgaoID || $svc_op['unidade'] != $unidadeID) {
                        $existe_turno_outro_orgao = true;
                    } else {
                        if ($svc_op['esc_id'] != $escalaId) {
                            $existe_turno_outra_escala = true;
                        } else {
                            $existe_turno_nesta_escala = true;
                        }
                    }
                }
            }

            if (array_key_exists('risaer', $servicosDia)) {
                foreach ($sd['risaer'] as $svc_risaer) {
                    if ($svc_risaer['orgao'] != $orgaoID || $svc_risaer['unidade'] != $unidadeID) {
                        $existe_risaer_outro_orgao = true;
                    } else {
                        $existe_risaer_neste_orgao = true;
                    }
                }
            }

            if (array_key_exists('info', $servicosDia)) {
                foreach ($sd['info'] as $info) {
                    if ($info['orgao'] != $orgaoID || $info['unidade'] != $unidadeID) {
                        $existe_info_outro_orgao = true;
                    } else {
                        $existe_info_neste_orgao = true;
                    }
                }
            }

            //verifica se a informacao que estara na celular pertence a esta escala ou não
            if (
                    (
                    !$existe_info_neste_orgao &&
                    !$existe_turno_nesta_escala &&
                    !$existe_turno_outra_escala
                    ) &&
                    ($existe_info_outro_orgao || $existe_turno_outro_orgao)
            ) {//as informações sao todas provenientes de outra escala
                $celCor = "outro-orgao";
            } else if (
                    (
                    !$existe_info_neste_orgao &&
                    !$existe_turno_nesta_escala &&
                    !$existe_turno_outro_orgao &&
                    !$existe_info_outro_orgao
                    ) &&
                    ($existe_turno_outra_escala)
            ) {
                $celCor = "outra";
            } else if (
                    (
                    $existe_info_neste_orgao ||
                    $existe_turno_nesta_escala ||
                    $existe_turno_outra_escala
                    ) &&
                    ($existe_info_outro_orgao || $existe_turno_outro_orgao)
            ) {//as informacoes sao provenientes deste e de outra escala
                $celCor = "outro-orgao-misto";
            } else if (
                    ($existe_info_neste_orgao || $existe_turno_nesta_escala) &&
                    ($existe_turno_outra_escala) &&
                    !$existe_info_outro_orgao &&
                    !$existe_turno_outro_orgao
            ) {
                $celCor = "misto";
            } else {//as informacoes sao provenientes apenas desta escala
                $celCor = $corLinha;
            }

            //verifica se tem risaer

            if ($existe_risaer_neste_orgao) {//as informações sao todas provenientes de outra escala
                $celCor = "risaer";
            } else if ($existe_risaer_outro_orgao) {//as informacoes sao provenientes deste e de outra escala
                $celCor = "orgao-risaer";
            }


            $valorEd = array_key_exists('operacionais', $servicosDia) ? implode("-", array_column($servicosDia['operacionais'], 'turno_leg')) : "";

            $display = "";
            $display = "";
            if (array_key_exists('operacionais', $servicosDia)) {
                $display = $display . implode("", array_column($servicosDia['operacionais'], 'turno_leg'));
            }
            if (array_key_exists('risaer', $servicosDia)) {
                $display = $display != "" ? $display . "-" . implode("", array_column($servicosDia['risaer'], 'risaer_leg')) : $display . implode("", array_column($servicosDia['risaer'], 'risaer_leg'));
            }
            if (array_key_exists('info', $servicosDia)) {
                $display = $display != "" ? $display . "-" . implode("", array_column($servicosDia['info'], 'texto')) : $display . implode("", array_column($servicosDia['info'], 'texto'));
            }

            $idsServicosDia = array_key_exists('operacionais', $servicosDia) ? array_column($servicosDia['operacionais'], 'svc_id') : array();

            $verificarCombinacaoTurnos = array_key_exists('operacionais', $servicosDia) ? array_column($servicosDia['operacionais'], 'turno_id') : array();
            $qtd = $qtd + sizeof($verificarCombinacaoTurnos);
            $erroCombinacaoTurnos = checaCombinacoesTurnos($verificarCombinacaoTurnos, $combinacoesTurnos);

            $atr = "";
            $idsTextoADeletar = array_key_exists('info', $servicosDia) ? implode('|', array_column($servicosDia['info'], 'info_id')) : "";
            $tirarMenu = false;
            if ($diaAfastamento) {
                $celCor = 'afastamento'; //se tiver alguma informação na celula mostra uma cor mais escura
                if ($idsTextoADeletar != "") {
                    $atr = array("onclick" => "retirarInformacaoDoAfastamento(this,'$idsTextoADeletar','$escalaLeg',$escalaId);");
                    $celCor .= (!empty($display) ? "-medio" : ""); //marca os valores que podem ser retirados atraves desta escala
                } else if (!empty($display)) {
                    $celCor .= (!empty($display) ? "-forte" : ""); //marca os valores que só podem ser retirados em outra escala
                }

                empty($display) ? $display = "--" : null;
                $valorEd = "--";
                $tirarMenu = true;
            } else if (in_array($i, $erroHab)) {
                $celCor = 'habilitacao_invalida';
                $editavel = false;
                if ($idsTextoADeletar != "") {
                    $atr = array("onclick" => "retirarInformacaoDaHabInvalida(this,'$idsTextoADeletar','$escalaLeg',$escalaId);");
                    $celCor .= (!empty($display) ? "-medio" : ""); //marca os valores que podem ser retirados atraves desta escala
                } else if (!empty($display)) {
                    $celCor .= (!empty($display) ? "-forte" : ""); //marca os valores que só podem ser retirados em outra escala
                }
                empty($display) ? $display = "-" : null;
                $valorEd = "-";
                $tirarMenu = true;
            } else {
                if (in_array($i, $errosfolgas) || in_array($i, $errosPosNoturno) || $erroCombinacaoTurnos) {
                    $celCor = "vermelho";
                    if ($existe_turno_outra_escala) {
                        $celCor .= "-outra";
                    } else if ($existe_info_outro_orgao || $existe_turno_outro_orgao || $existe_risaer_outro_orgao) {
                        $celCor .= "-misto";
                    }
                } else {
                    $celCor = $celCor . "-" . ($intensidadeDiaMes[$i] ? "medio" : "fraco");
                }
            }

            $dia = "d$i";

            $checaMenu = false;
            if (!$tirarMenu) {
                if (!$editavel) {
                    $checaMenu = true;
                }
            }

            $colunas[$dia] = array(
                'valor' => $display,
                'valorEd' => $valorEd,
                'svc_id' => "" . implode(",", $idsServicosDia),
                'editavel' => $editavel,
                'classes' => "turno_svc $celCor rotulo",
                'idEscala' => $escalaId,
                'dia' => $i,
                'operador' => $opr['ee_id'],
                'tipo_escala' => $tipoEscala,
                'legEscala' => $escalaLeg,
                'atr' => $atr,
                $tirarMenu ? null : 'menu' => $checaMenu
            );
        }

        //////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($dadosEscala['qtd_svc'] == $qtd) {
            $celCor = "verde";
        } else if ($dadosEscala['qtd_svc'] < $qtd) {
            $celCor = "vermelho";
        } else {
            $celCor = $corLinha . "-medio";
        }

        $colunas['qtd_svc'] = array('valor' => "$qtd", 'editavel' => false, 'classes' => $celCor);
//////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////////////
        $servicosDia = array_key_exists($i, $opr['servicos']) ? $opr['servicos'][$i] : array();
        $i = 1;
        foreach ($turnos as $t) {
            $qtd = 0;
            foreach ($opr['servicos'] as $svcs) {
                if (array_key_exists('operacionais', $svcs)) {
                    foreach ($svcs['operacionais'] as $svc) {
                        if (in_array($svc['turno_id'], $t['contagemId'])) {
                            $qtd++;
                        }
                    }
                }
            }

            $celCor = $corLinha . "-medio";

            $turnosC = "qt$i";
            $i++;
            $colunas[$turnosC] = array('valor' => "$qtd", 'editavel' => false, 'classes' => $celCor);
        }

        $qtdDiasAfastamento = sizeof($afastMes);

        $qtdChm = $opr['carga_mensal'];
        $chMaxProporcional = ($qtdDiasMes - $qtdDiasAfastamento) * ($dadosEscala['ch_maxima']) / $qtdDiasMes;
        $chMinProporcional = ($qtdDiasMes - $qtdDiasAfastamento) * ($dadosEscala['ch_minima']) / $qtdDiasMes;

        $chm = explode(":", $qtdChm);
        $chmEmHoras = $chm[0] + ($chm[1] / 60);

        if (!$opr['manutencao'] && ($chmEmHoras < $chMinProporcional || $chmEmHoras > $chMaxProporcional)) {
            $celCor = "vermelho";
        } else {
            $celCor = $corLinha . "-medio";
        }

        $colunas['ch_mes'] = array('valor' => "$qtdChm", 'editavel' => false, 'tipo' => "qtdChm", 'classes' => $celCor);

        $qtdCHT = $opr['CHT'];

        if ($qtdCHT < $ChMinimaQuadrimestre) {
            $celCor = "vermelho";
        } else {
            $celCor = $corLinha . "-medio";
        }
        $colunas['ch_cht'] = array('valor' => "$qtdCHT", 'editavel' => false, 'classes' => $celCor);
        $linhas[] = $colunas;
    }
    /////////////////////////////////////////////////////////////////////////////////////////////
    //////          FIM LINHA DE SERVICOS OPERADOREs          //////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////
    $resposta = array();
    $resposta['valores'] = $linhas;
    $resposta['diasMes'] = $qtdDiasMes;
    $resposta['qtdTurnos'] = sizeof($turnos);
    $resposta['qtdTurnosGeral'] = $qtdTurnosGeral;
    $resposta['qtdOpr'] = sizeof($operadores);
    $resposta['qtdEquipes'] = sizeof($equipes) - 1;
    echo json_encode($resposta);
} else if ($item == 'branco_preenchida') {
    $grupo = $post['grupo'];
    include 'htmls/branco_preenchida.php';
} else if ($item == 'pegar_escalas') {
    $grupo = $post['grupo'];
    $resposta = listaEscalas($grupo);
    echo json_encode($resposta);
} else if ($item == 'escala_preenchida') {
    $grupo = $post['grupo'];
    $turnos = listaTurnos($grupo);
    $turnosComb = pegarlistaCombinacoes($grupo);
    $escalas = listaEscalas($grupo);
    include 'htmls/sequencia_preencher.php';
} else if ($item == 'select_turno') {
    $grupo = $post['grupo'];
    $prox = $post['prox'];
    $div = $post['div'];
    $turnos = listaTurnosEscalas($grupo);
    $turnosComb = pegarlistaCombinacoes($grupo);

    $turnosEscalas = array();
    if ($div == 'geral') {
        $turnosEscalas['geral']['turnos'] = array();
        foreach ($turnos as $t) {
            if (!in_array($t['id'], $turnosEscalas['geral']['turnos'])) {
                $turnosEscalas['geral'][] = $t;
                $turnosEscalas['geral']['turnos'][] = $t['id'];
            }
        }
    } else {
        foreach ($turnos as $t) {
            $turnosEscalas[$t['escala']][] = $t;
            $turnosEscalas[$t['escala']]['turnos'][] = $t['id'];
        }
    }
    foreach ($turnosComb as $tc) {
        foreach ($turnosEscalas as $te => $t) {
            if (in_array($tc['turno1'], $t['turnos']) && in_array($tc['turno2'], $t['turnos'])) {
                $turnosEscalas[$te]['combinacoes'][] = array('legendas' => $tc['leg_turno1'] . "-" . $tc['leg_turno2'], 'ids' => $tc['turno1'] . "-" . $tc['turno2']);
            }
        }
    }
    include 'htmls/select_turno.php';
} else if ($item == 'remanejamento') {
    $dados = $post['dados'];
    $operador = $post['operador'];
    $legenda = $post['legenda'];
    $diasMes = $post['diasMes'];
    $chMensal = $post['chMensal'];
    $svc_id = explode(",", $dados['svc_id']);

    $turnos = pegarTurnoPeloServico($svc_id[0]);

    if (sizeof($svc_id) > 1) {
        $resposta = pegarTurnoPeloServico($svc_id[1]);
        $turnos[] = $resposta[0];
    }
    $turnosEscala = listaTurnosEscala($dados['idEscala']);
    include 'htmls/add_remanejamento.php';
} else if ($item == 'escalacao') {
    $dados = $post['dados'];
    $operador = $post['operador'];
    $legenda = $post['legenda'];
    $chMensal = $post['chMensal'];
    $svc_id = explode(",", $dados['svc_id']);
    $turnos = pegarTurnoPeloServico($svc_id[0]);
    if (sizeof($svc_id) > 1) {
        $resposta = pegarTurnoPeloServico($svc_id[1]);
        $turnos[] = $resposta[0];
    }

    $turnosEscala = listaTurnosEscala($dados['idEscala']);
    foreach ($turnosEscala as $i => $te) {
        foreach ($turnos as $t) {
            if ($te['id'] == $t['id']) {
                unset($turnosEscala[$i]);
            }
        }
    }
    include 'htmls/add_escalacao.php';
} else if ($item == 'dispensa') {
    $dados = $post['dados'];
    $operador = $post['operador'];
    $legenda = $post['legenda'];
    $chMensal = $post['chMensal'];
    $svc_id = explode(",", $dados['svc_id']);
    $turnos = pegarTurnoPeloServico($svc_id[0]);

    if (sizeof($svc_id) > 1) {
        $resposta = pegarTurnoPeloServico($svc_id[1]);
        $turnos[] = $resposta[0];
    }
    include 'htmls/add_dispensa.php';
} elseif ($item == 'texto') {
    $dados = $post['dados'];
    $operador = $post['operador'];
    $legenda = $post['legenda'];
    $infoTextos = pegarTexto($dados['dia'], $dados['efetivo_mes'], $dados['tipo_escala']);
    $textosMostrar = implode("-", array_column($infoTextos, 'texto'));
    include 'htmls/add_texto.php';
} elseif ($item == 'risaer_escalar') {
    $dados = $post['dados'];
    $operador = $post['operador'];
    $grupo = $post['grupo'];
    $risaer = pegarlistaRISAER($grupo);
    include 'htmls/add_risaer.php';
} elseif ($item == 'risaer_dispensar') {
    $dados = $post['dados'];
    $operador = $post['operador'];
    $risaer = pegarServicosRisaer($dados['dia'], $dados['efetivo_mes'], $dados['tipo_escala']);
    include 'htmls/remover_risaer.php';
} else if ($item == 'lista_afastastamentos') {
    $em_id = $post['em_id'];
    $grupo = $post['grupo'];

    $resposta = mesAnoGrupo($grupo);
    $mes = $resposta[0]['mes'];
    $ano = $resposta[0]['ano'];

    $anoMes = "$ano$mes";
    $resposta = pegarUsuarioPeloEfetivoMes($em_id);
    $usuario = $resposta[0]['usuario'];

    $r = pegarNomeGuerraOperador($usuario);
    $nome = $r[0]['pg'] . " " . $r[0]['ng'];
    $afastamentos = pegarAfastamentosNoMes(array($usuario), $mes, $ano);
    include 'htmls/lista_afastamentos.php';
}
////////////////////////////////////////////////////////////////////////////////////////////
include $raiz . 'closeConn.php';
///////////////////////////////////////////////////////////////////////////////////////////////
?>