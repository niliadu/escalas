<?php

ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');
setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');

$server = filter_input_array(INPUT_SERVER, FILTER_DEFAULT);

if (isset($_SESSION)) {
    $sessao = filter_var_array($_SESSION, FILTER_DEFAULT);
    $raiz = $sessao['raiz'];
} else {
    $raiz = "";
}
if (isset($_GET)) {
    $get = filter_input_array(INPUT_GET, FILTER_DEFAULT);
}

if (isset($_POST)) {
    $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);
}

include $raiz . "conn.php";
include $raiz . 'seg.php';

/////////////////////////////FUNCOES////////////////////////////////////////////////////

//////////////////TEMPO E DATA ////////////////////////////////////////////

function diaMesAnoDepois($diaEsc, $mes, $ano) {
    $diaPosterior = 0;
    $mesPosterior = 0;
    $anoPosterior = 0;
    //verifica se o turno anterior a ele é noturno
    if ($diaEsc + 1 <= cal_days_in_month(CAL_GREGORIAN, $mes, $ano)) {//verifica se continua no mesmo mês o dia anterior
        $diaPosterior = $diaEsc + 1;
        $mesPosterior = $mes;
        $anoPosterior = $ano;
    } else {
        if ($mes + 1 <= 12) {//verifica se o mês continua o mesmo do mês anterior
            $diaPosterior = 1;
            $mesPosterior = $mes + 1;
            $anoPosterior = $ano;
        } else {
            $diaPosterior = 1;
            $mesPosterior = 1;
            $anoPosterior = $ano + 1;
        }
    }
    return (array('dia' => $diaPosterior,
        'mes' => $mesPosterior,
        'ano' => $anoPosterior));
}

function diaMesAnoAnterior($diaEsc, $mes, $ano) {
    $diaAnterior = $diaEsc;
    $mesAnterior = $mes;
    $anoAnterior = $ano;
    //verifica se o turno anterior a ele é noturno
    if ($diaEsc - 1 > 0) {//verifica se continua no mesmo mês o dia anterior
        $diaAnterior = $diaEsc - 1;
        $mesAnterior = $mes;
        $anoAnterior = $ano;
    } else {
        if ($mes - 1 > 0) {//verifica se o mês continua o mesmo do mês anterior
            $diaAnterior = cal_days_in_month(CAL_GREGORIAN, $mes - 1, $ano);
            $mesAnterior = $mes - 1;
            $anoAnterior = $ano;
        } else {
            $diaAnterior = 31;
            $mesAnterior = 12;
            $anoAnterior = $ano - 1;
        }
    }
    return (array('dia' => $diaAnterior, 'mes' => $mesAnterior, 'ano' => $anoAnterior));
}

//////////////////FUNÇÕES GENÉRICAS/////////////////////////////////////

//////////////////ESCALA INDIVIDUAL/////////////////////////////////////

////////////////////ESCALA GERAL////////////////////////////////////////

////////////////////AINDA NÃO DEFINIDAS////////////////////////////////////////

function checaServicosNaoPosNoturno($operador, $turnoEsc, $diaEsc, $tipoEscala, $mes, $ano) {
    $erro = array();
    $resposta = pegarTurno($turnoEsc);
    //verifica que o turno atual não é pós-noturno
    $turnoCaract = $resposta[0];
    if ($turnoCaract['pos_noturno'] == 0) {
        //verifica se o turno anterior a ele é noturno        
        $diaMesAnoAnt = diaMesAnoAnterior($diaEsc, $mes, $ano);
        $resposta = pegarServicosOperadorDia($operador, $diaMesAnoAnt['mes'], $diaMesAnoAnt['ano'], $diaMesAnoAnt['dia'], $tipoEscala);
        foreach ($resposta as $ta) {
            if ($ta['periodo'] == 2) {
                $erro = array('dia' => $diaEsc, 'dia_anterior' => "$diaAnterior/$mesAnterior/$anoAnterior");
                break;
            }
        }
    }
    return $erro;
}

function inserirServicoRisaer($servico, $efetivo_mes, $tipo_escala, $dia) {
    $sql = "INSERT INTO servico_risaer (dia, tipo_escala, servico, efetivo_mes) "; //deixe sempre  um espaco no final
    $sql .= "VALUES (?, ?, ?, ?)"; //deixe sempre  um espaco no final
    $binding = array('iiii', $dia, $tipo_escala, $servico, $efetivo_mes);
    return sql_ins($sql, $GLOBALS['conn'], $binding);
}

function removerServicoRisaer($id) {
    $sql = "DELETE FROM servico_risaer ";
    $sql .= "WHERE id = ? "; //deixe sempre  um espaco no final    
    $binding = array('i', $id);
    return sql_del($sql, $GLOBALS['conn'], $binding);
}

function pegarServicosRisaer($dia, $efetivo_mes, $tipo_escala) {
    $sql = "SELECT sr.id, ri.legenda "; //deixe sempre  um espaco no final
    $sql .= "FROM servico_risaer as sr "; //deixe sempre  um espaco no final
    $sql .= "JOIN risaer as ri "; //deixe sempre  um espaco no final
    $sql .= "ON ri.id = sr.servico "; //deixe sempre  um espaco no final
    $sql .= "WHERE sr.dia = ? AND sr.efetivo_mes = ? AND sr.tipo_escala = ? "; //deixe sempre  um espaco no final    
    $binding = array('iii', $dia, $efetivo_mes, $tipo_escala);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function atualizarQtdEsc($novosValoresCel, $turn_esc_id) {
    $sql = "UPDATE turno_escala "; //deixe sempre  um espaco no final (nome, legenda, inicio, termino, etapa_full, grupo_escalas) 
    $sql .= "SET ref_soma = ? "; //deixe sempre  um espaco no final
    $sql .= "WHERE id = ? "; //deixe sempre  um espaco no final
    $binding = array('ii', $novosValoresCel, $turn_esc_id);
    return sql_up($sql, $GLOBALS['conn'], $binding);
}

function atualizarQtd($novosValoresCel, $turnoId) {
    $sql = "UPDATE turnos "; //deixe sempre  um espaco no final (nome, legenda, inicio, termino, etapa_full, grupo_escalas) 
    $sql .= "SET ref_soma_geral = ? "; //deixe sempre  um espaco no final
    $sql .= "WHERE id = ? "; //deixe sempre  um espaco no final
    $binding = array('ii', $novosValoresCel, $turnoId);
    return sql_up($sql, $GLOBALS['conn'], $binding);
}

function pegarEfetivoMesPorEfetivoEscala($operador) {
    $sql = "SELECT efetivo "; //deixe sempre  um espaco no final
    $sql .= "FROM efetivo_escala "; //deixe sempre  um espaco no final
    $sql .= "WHERE id = ? "; //deixe sempre  um espaco no final    
    $binding = array('i', $operador);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarTexto($dia, $efetivo_mes, $tipo_escala) {
    $sql = "SELECT id, texto "; //deixe sempre  um espaco no final
    $sql .= "FROM infos "; //deixe sempre  um espaco no final
    $sql .= "WHERE dia = ? AND efetivo_mes_id = ? AND tipo_escala = ? "; //deixe sempre  um espaco no final    
    $binding = array('iii', $dia, $efetivo_mes, $tipo_escala);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function criarParametrosBranco($mes, $ano, $orgao, $unidade) {
    $sql = "INSERT INTO grupos_escalas (mes, ano, orgao) "; //deixe sempre  um espaco no final
    $sql .= "VALUES (?, ?, ?)"; //deixe sempre  um espaco no final
    $binding = array('iiis', $mes, $ano, $orgao, $unidade);
    return sql_ins($sql, $GLOBALS['conn'], $binding);
}

function copiarParametros($grupo, $mes, $ano, $orgao) {
    $sql = "INSERT INTO grupos_escalas (mes, ano, orgao, qtd_folgas, qtd_trocas, unidade) "; //deixe sempre  um espaco no final
    $sql .= "SELECT ?, ?, ?, qtd_folgas, qtd_trocas, unidade FROM grupos_escalas WHERE id = ? "; //deixe sempre  um espaco no final
    $binding = array('iiii', $mes, $ano, $orgao, $grupo);
    $sqls[] = $sql;
    $bindings[] = $binding;

    $sql = "SET @last_id = LAST_INSERT_ID() ";
    $sqls[] = $sql;
    $bindings[] = NULL;

    $sql = "INSERT INTO turnos (grupo_escalas, nome, inicio, termino, etapa_full, legenda, periodo, pos_noturno) "; //deixe sempre  um espaco no final
    $sql .= "SELECT @last_id, nome, inicio, termino, etapa_full, legenda, periodo, pos_noturno FROM turnos WHERE grupo_escalas = ? "; //deixe sempre  um espaco no final
    $binding = array('i', $grupo);
    $sqls[] = $sql;
    $bindings[] = $binding;

    $sql = "INSERT INTO escalas (grupo_escalas, nome, legenda, ch_maxima, ch_minima) "; //deixe sempre  um espaco no final
    $sql .= "SELECT @last_id, nome, legenda, ch_maxima, ch_minima FROM escalas WHERE grupo_escalas = ? "; //deixe sempre  um espaco no final
    $binding = array('i', $grupo);
    $sqls[] = $sql;
    $bindings[] = $binding;

    $sql = "INSERT INTO risaer (grupo_escalas, nome, legenda, inicio, termino, tipo_etapa, intervalo_antes, intervalo_depois) "; //deixe sempre  um espaco no final
    $sql .= "SELECT @last_id, nome, legenda,  inicio, termino, tipo_etapa, intervalo_antes, intervalo_depois FROM risaer WHERE grupo_escalas = ? "; //deixe sempre  um espaco no final
    $binding = array('i', $grupo);
    $sqls[] = $sql;
    $bindings[] = $binding;
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
//esta parte cria uma tabela temporaria para guardar os valores que do grupo anterior que serÃ£o copiados e depois subtituidos 
//pelos ids dos itens do novo grupo
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $sql = "CREATE TEMPORARY TABLE te_temp LIKE turno_escala";
    //$binding = array('i', $grupo);
    $binding = NULL;
    $sqls[] = $sql;
    $bindings[] = $binding;

    //fazendo correlaÃ§Ã£o com turnos e escalas na tabela temporaria
    $sql = "INSERT INTO te_temp (grupo_escalas, escala, turno) "; //deixe sempre  um espaco no final
    $sql .= "SELECT @last_id, te.escala, te.turno FROM turno_escala as te WHERE te.grupo_escalas = ? "; //deixe sempre  um espaco no final
    $binding = array('i', $grupo);
    $sqls[] = $sql;
    $bindings[] = $binding;



    $sql = "INSERT INTO turno_escala (grupo_escalas, escala, turno)"; //, turno) "; //deixe sempre  um espaco no final
    $sql .= "SELECT @last_id, es2.id as escala, t2.id  as turno ";
    $sql .= "FROM te_temp as te ";
    $sql .= "JOIN escalas as es ";
    $sql .= "ON es.id = te.escala ";
    $sql .= "JOIN escalas as es2 ";
    $sql .= "ON es2.legenda = es.legenda AND es2.grupo_escalas = @last_id ";
    $sql .= "JOIN turnos as t ";
    $sql .= "ON t.id = te.turno ";
    $sql .= "JOIN turnos as t2 ";
    $sql .= "ON t2.legenda = t.legenda AND t2.grupo_escalas = @last_id ";
    $sql .= "WHERE te.grupo_escalas = @last_id "; //deixe sempre  um espaco no final
    $binding = null; //array('i', $grupo);
    $sqls[] = $sql;
    $bindings[] = $binding;

    $sql = "DROP TEMPORARY TABLE te_temp";
    //$binding = array('i', $grupo);
    $binding = NULL;
    $sqls[] = $sql;
    $bindings[] = $binding;
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $sql = "INSERT INTO efetivo_mes (grupo_escalas, legenda, usuario, funcao_escala, manutencao) "; //deixe sempre  um espaco no final
    $sql .= "SELECT @last_id, legenda, usuario, funcao_escala, manutencao FROM efetivo_mes WHERE grupo_escalas = ? "; //deixe sempre  um espaco no final
    $binding = array('i', $grupo);
    $sqls[] = $sql;
    $bindings[] = $binding;
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
//esta parte cria uma tabela temporaria para guardar os valores que do grupo anterior que serÃ£o copiados e depois subtituidos 
//pelos ids dos itens do novo grupo
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $sql = "CREATE TEMPORARY TABLE ee_temp LIKE efetivo_escala";
    $binding = NULL;
    $sqls[] = $sql;
    $bindings[] = $binding;

    //fazendo correlaÃ§Ã£o com turnos e escalas na tabela temporaria
    $sql = "INSERT INTO ee_temp (grupo_escalas, escala, efetivo) "; //deixe sempre  um espaco no final
    $sql .= "SELECT @last_id, ee.escala, ee.efetivo FROM efetivo_escala as ee WHERE ee.grupo_escalas = ? "; //deixe sempre  um espaco no final
    $binding = array('i', $grupo);
    $sqls[] = $sql;
    $bindings[] = $binding;



    $sql = "INSERT INTO efetivo_escala (grupo_escalas, escala, efetivo)"; //, turno) "; //deixe sempre  um espaco no final
    $sql .= "SELECT @last_id, es2.id as escala, em2.id  as efetivo ";
    $sql .= "FROM ee_temp as ee ";
    $sql .= "JOIN escalas as es ";
    $sql .= "ON es.id = ee.escala ";
    $sql .= "JOIN escalas as es2 ";
    $sql .= "ON es2.legenda = es.legenda AND es2.grupo_escalas = @last_id ";
    $sql .= "JOIN efetivo_mes as em ";
    $sql .= "ON em.id = ee.efetivo ";
    $sql .= "JOIN efetivo_mes as em2 ";
    $sql .= "ON em2.usuario = em.usuario AND em2.grupo_escalas = @last_id ";
    $sql .= "WHERE ee.grupo_escalas = @last_id "; //deixe sempre  um espaco no final
    $binding = null; //array('i', $grupo);
    $sqls[] = $sql;
    $bindings[] = $binding;

    $sql = "DROP TEMPORARY TABLE ee_temp";
    $binding = NULL;
    $sqls[] = $sql;
    $bindings[] = $binding;
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////
//esta parte cria uma tabela temporaria para guardar os valores que do grupo anterior que serÃ£o copiados e depois subtituidos 
//pelos ids dos itens do novo grupo
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $sql = "CREATE TEMPORARY TABLE rct_temp LIKE restricoes_combinacoes_turno";
    $binding = NULL;
    $sqls[] = $sql;
    $bindings[] = $binding;

    //fazendo correlaÃ§Ã£o com turnos e escalas na tabela temporaria
    $sql = "INSERT INTO rct_temp (grupo_escalas, turno1, turno2) "; //deixe sempre  um espaco no final
    $sql .= "SELECT @last_id, rct.turno1, rct.turno2 FROM restricoes_combinacoes_turno AS rct WHERE rct.grupo_escalas = ? "; //deixe sempre  um espaco no final
    $binding = array('i', $grupo);
    $sqls[] = $sql;
    $bindings[] = $binding;



    $sql = "INSERT INTO restricoes_combinacoes_turno (grupo_escalas, turno1, turno2)"; //, turno) "; //deixe sempre  um espaco no final
    $sql .= "SELECT @last_id, t1.id as turno1, t3.id  as turno2 ";
    $sql .= "FROM rct_temp AS rct ";
    $sql .= "JOIN turnos as t ";
    $sql .= "ON t.id = rct.turno1 ";
    $sql .= "JOIN turnos as t1 ";
    $sql .= "ON t1.legenda = t.legenda AND t1.grupo_escalas = @last_id ";
    $sql .= "JOIN turnos as t2 ";
    $sql .= "ON t2.id = rct.turno2 ";
    $sql .= "JOIN turnos as t3 ";
    $sql .= "ON t3.legenda = t2.legenda AND t3.grupo_escalas = @last_id ";
    $sql .= "WHERE rct.grupo_escalas = @last_id "; //deixe sempre  um espaco no final
    $binding = null; //array('i', $grupo);
    $sqls[] = $sql;
    $bindings[] = $binding;

    $sql = "DROP TEMPORARY TABLE rct_temp";
    $binding = NULL;
    $sqls[] = $sql;
    $bindings[] = $binding;
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////
    return sql_transaction($sqls, $GLOBALS['conn'], $bindings);
}

function pegarGruposEscala($orgao, $unidade) {
    $sql = "SELECT id "; //deixe sempre  um espaco no final
    $sql .= "FROM grupos_escalas "; //deixe sempre  um espaco no final
    $sql .= "WHERE orgao = ? AND unidade = ? "; //deixe sempre  um espaco no final
    $sql .= "ORDER BY id DESC LIMIT 1 "; //deixe sempre  um espaco no final
    $binding = array('is', $orgao, $unidade);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function listaOrgaoUsuarioEfetivo($mes, $ano, $usu) {
    $sql = "SELECT hab.habID, hs.habilitacao_sigla, hab.setor, hab.habilitacaoID "; //deixe sempre  um espaco no final
    $sql .= "FROM habilitacoes as hab "; //deixe sempre  um espaco no final    
    $sql .= "JOIN habilitacoes_select as hs "; //deixe sempre  um espaco no final    
    $sql .= "ON hab.habID = hs.habID "; //deixe sempre  um espaco no final    
    $sql .= "WHERE hab.cadastroID = ? AND hab.deletedat IS NULL AND hab.dt_validade >= NOW() "; //deixe sempre  um espaco no final

    $binding = array('s', $usu);
    return sql_busca($sql, $GLOBALS['connL'], $binding);
}

function pegarTurno($turno) {
    $sql = "SELECT * ";
    $sql .= "FROM turnos ";
    $sql .= "WHERE id = ? ";
    $binding = array('i', $turno);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarServicosOperadorDiaEscala($efetivoMesID, $dia, $escala, $tipo) {

    $sql = "SELECT t.id as turno, t.periodo, t.pos_noturno, t.legenda "
            . "FROM efetivo_escala as ee "
            . "JOIN servico as s "
            . "ON s.operador = ee.id "
            . "LEFT JOIN turnos as t "
            . "ON s.turno = t.id "
            . "JOIN tipo_escala as te "
            . "ON s.tipo_escala = te.id "
            . "WHERE ee.efetivo = ? AND ee.escala = ? AND s.dia = ? AND te.tipo = ? AND s.turno IS NOT NULL ";


    $binding = array('iiii', $efetivoMesID, $escala, $dia, $tipo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarServicosOperadorDia($opr, $mes, $ano, $dia, $tipoEscala) {
    $sql4 = "SELECT te.tipo "
            . "FROM tipo_escala as te "
            . "WHERE id = ? ";

    $sql3 = "SELECT id ";
    $sql3 .= "FROM grupos_escalas ";
    $sql3 .= "WHERE mes = ? AND ano = ? ";

    $sql2 = "SELECT em.usuario ";
    $sql2 .= "FROM efetivo_mes as em ";
    $sql2 .= "JOIN efetivo_escala as ee ";
    $sql2 .= "ON ee.id = ? ";
    $sql2 .= "WHERE ee.efetivo = em.id ";

    $sql1 = "SELECT ee1.id ";
    $sql1 .= "FROM efetivo_escala as ee1 ";
    $sql1 .= "JOIN efetivo_mes as em1 ";
    $sql1 .= "ON em1.id = ee1.efetivo AND em1.usuario = ($sql2) ";
    $sql1 .= "WHERE ee1.grupo_escalas IN ($sql3) ";

    $sql = "SELECT t.id as turno, t.periodo, t.pos_noturno, t.legenda ";
    $sql .= "FROM servico as s ";
    $sql .= "JOIN turnos as t ";
    $sql .= "ON s.turno = t.id ";
    $sql .= "JOIN tipo_escala as te1 ";
    $sql .= "ON s.tipo_escala = te1.id ";
    $sql .= "WHERE s.dia = ? AND s.operador IN ($sql1) AND te1.tipo IN ($sql4) ";

    $binding = array('iiiii', $dia, $opr, $mes, $ano, $tipoEscala);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarIdEfetivoMes($grupo, $usuario) {
    $sql = "SELECT * "
            . "FROM efetivo_mes "
            . "WHERE grupo_escalas = ? AND usuario = ?";


    $binding = array('is', $grupo, $usuario);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarTiposEscalaGrupo($grupo) {
    $sql = "SELECT * "
            . "FROM tipo_escala "
            . "WHERE grupo_escalas = ? ";


    $binding = array('is', $grupo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarIdEfetivoEscala($grupo, $usuario, $escala) {
    $sql = "SELECT ee.id "
            . "FROM efetivo_escala as ee "
            . "JOIN efetivo_mes as em "
            . "ON ee.efetivo = em.id "
            . "WHERE em.grupo_escalas = ? AND em.usuario = ? AND ee.escala = ? ";


    $binding = array('isi', $grupo, $usuario, $escala);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

/*
  function pegarServicosOperador($opr, $mes, $ano) {
  $sql3 = "SELECT id ";
  $sql3 .= "FROM grupos_escalas ";
  $sql3 .= "WHERE mes = ? AND ano = ? ";

  $sql2 = "SELECT em.usuario ";
  $sql2 .= "FROM efetivo_mes as em ";
  $sql2 .= "JOIN efetivo_escala as ee ";
  $sql2 .= "ON ee.id = ? ";
  $sql2 .= "WHERE ee.efetivo = em.id ";

  $sql1 = "SELECT ee1.id ";
  $sql1 .= "FROM efetivo_escala as ee1 ";
  $sql1 .= "JOIN efetivo_mes as em1 ";
  $sql1 .= "ON em1.id = ee1.efetivo AND em1.usuario = ($sql2) ";
  $sql1 .= "WHERE ee1.grupo_escalas IN ($sql3) ";

  $sql = "SELECT s.dia ";
  $sql .= "FROM servico as s ";
  $sql .= "JOIN turnos as t ";
  $sql .= "ON s.turno = t.id ";
  $sql .= "WHERE s.operador IN ($sql1) ";

  $binding = array('iii', $opr, $mes, $ano);
  return sql_busca($sql, $GLOBALS['conn'], $binding);
  }
 */

function pegarDadosTurno($id) {
    $sql = "SELECT * ";
    $sql .= "FROM turnos ";
    $sql .= "WHERE id = ? ";
    $binding = array('i', $id);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarLimitesCH($idEscala) {
    $sql = "SELECT ch_maxima, ch_minima ";
    $sql .= "FROM escalas ";
    $sql .= "WHERE id = ?";
    $binding = array('i', $idEscala);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarCargaHorariaTurno($turno) {
    $sbq1 = ""
            . "SUM("
            . "TIME_TO_SEC("
            . "CASE WHEN"
            . " SUBTIME(t.termino, t.inicio) < 0 "
            . "THEN"
            . " SUBTIME(t.termino, t.inicio) + INTERVAL 24 HOUR "
            . "ELSE"
            . " SUBTIME(t.termino, t.inicio) "
            . "END"
            . ")"
            . ")";

    $sql = "SELECT SEC_TO_TIME($sbq1) as ch "
            . "FROM turnos AS t "
            . "WHERE t.id = ? ";
    $binding = array('i', $turno);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function inserirTroca($proponente, $escalaPE, $turnoPE, $diaPE, $proposto, $escalaPO, $turnoPO, $diaPO, $grupo, $tipo) {
    $sbq = "SELECT ee.id "
            . "FROM efetivo_escala as ee "
            . "JOIN efetivo_mes as em "
            . "ON em.id = ee.efetivo "
            . "WHERE em.usuario = ? AND em.grupo_escalas = ? AND ee.escala = ? ";

    $sql = "INSERT INTO trocas (proponente, dia, turno_proponente, proposto, dia_proposto, turno_proposto, grupo_escalas, tipo) "
            . "VALUES (($sbq),?,?,($sbq),?,?,?,?) "; //deixe sempre  um espaco no final
    $sqls[] = $sql;
    $bindings[] = array('siiiisiiiiii', $proponente, $grupo, $escalaPE, $diaPE, $turnoPE, $proposto, $grupo, $escalaPO, $diaPO, $turnoPO, $grupo, $tipo);

    //recuperando o último id inserido
    $sql = "SET @id_troca = LAST_INSERT_ID() ";
    $sqls[] = $sql;
    $bindings[] = null;

    $sql = "INSERT INTO trocas_status "
            . "(troca, usuario, data, texto, status) "
            . "VALUES (@id_troca, ?, NOW(), NULL, 1)";
    $sqls[] = $sql;
    $bindings[] = array('s', $proponente);

    return sql_transaction($sqls, $GLOBALS['conn'], $bindings);
}

function inserirNovoStatusTroca($trocaID, $novoStatus, $usuario, $texto) {
    $sql = "UPDATE trocas  "
            . "SET status = ? "
            . "WHERE id = ? ";
    $sqls[] = $sql;
    $bindings[] = array('ii', $novoStatus, $trocaID);

    $sql = "INSERT INTO trocas_status "
            . "(troca, usuario, data, texto, status) "
            . "VALUES (?, ?, NOW(), ?, ?)";
    $sqls[] = $sql;
    $bindings[] = array('issi', $trocaID, $usuario, $texto, $novoStatus);

    return sql_transaction($sqls, $GLOBALS['conn'], $bindings);
}

function pegarEscalasDoOperadorPeloEfetivo($operador) {

    $sql = "SELECT ee.escala, e.legenda "; //deixe sempre  um espaco no final
    $sql .= "FROM efetivo_escala as ee "; //deixe sempre  um espaco no final
    $sql .= "JOIN efetivo_mes as em "; //deixe sempre  um espaco no final
    $sql .= "ON ee.efetivo = em.id "; //deixe sempre  um espaco no final
    $sql .= "JOIN escalas as e "; //deixe sempre  um espaco no final
    $sql .= "ON ee.escala = e.id "; //deixe sempre  um espaco no final
    $sql .= "WHERE em.id = ? "; //deixe sempre  um espaco no final 
    $binding = array('i', $operador);

    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function checarPublicacaoEscala($escalas, $tipo) {
    $string = 'i';
    $binding = array($string, $tipo);
    $juncao = array();
    foreach ($escalas as $e) {
        $juncao[] = 'escala = ?';
        $string .= 'i';
        $binding[] = $e['escala'];
    }
    $binding[0] = $string;

    $textoEscalas = implode(" OR ", $juncao);
    $sql = "SELECT COUNT(*) as qtd "; //deixe sempre  um espaco no final
    $sql .= "FROM publicacao_escala "; //deixe sempre  um espaco no final
    $sql .= "WHERE tipo = ? AND publicada = 2 AND ($textoEscalas) "; //deixe sempre  um espaco no final 

    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarEscalasPublicadas($grupo, $tipo) {
    $sql = "SELECT pe.escala "
            . "FROM publicacao_escala as pe "
            . "JOIN escalas as e "
            . "ON pe.escala = e.id "
            . "WHERE pe.tipo = ? AND e.grupo_escalas = ? AND pe.publicada = 2 "; //deixe sempre  um espaco no final
    $binding = array('ii', $tipo, $grupo);

    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarEscalasPublicadasGeral($grupo, $tipo) {
    $sql = "SELECT * "
            . "FROM grupos_escalas as ge "
            . "JOIN grupos_escalas as ge2 "
            . "ON ge.mes = ge2.mes AND ge.ano = ge2.ano "
            . "JOIN escalas as e "
            . "ON e.grupo_escalas = ge2.id "
            . "JOIN publicacao_escala as pe "
            . "ON pe.escala  = e.id "
            . "WHERE pe.tipo = ? AND ge.id = ? AND pe.publicada = 2 "; //deixe sempre  um espaco no final
    $binding = array('ii', $tipo, $grupo);

    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function mesAnoGrupo($grupo) {
    $sql = "SELECT mes, ano ";
    $sql .= "FROM grupos_escalas ";
    $sql .= "WHERE id = ? ";
    $binding = array('i', $grupo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

//function pegarDadosGrupo($grupo) {
//    $sql = "SELECT * ";
//    $sql .= "FROM grupos_escalas ";
//    $sql .= "WHERE id = ? ";
//    $binding = array('i', $grupo);
//    return sql_busca($sql, $GLOBALS['conn'], $binding);
//}
//function servicoTotaisDasEscalas($usuariosEscala, $grupos) {
//
//    //pegar os grupos de escalas onde usuarios que estao nesta escala possuem servico
//    $sql = "SELECT "
//            . "em.usuario, "
//            . "em.id as em_id, "
//            . "em.legenda as usu_leg, "
//            . "em.manutencao, "
//            . "ee.id as ee_id, "
//            . "e.id as esc_id, "
//            . "e.legenda as esc_leg, "
//            . "s.id as svc_id, "
//            . "s.dia, "
//            . "t.id as turno_id, "
//            . "t.legenda as turnos_leg, "
//            . "t.etapa_full as etapa, "
//            . "t.inicio, "
//            . "t.termino, "
//            . "te.tipo as tipo_escala, "
//            . "ge.orgao,"
//            . "ge.mes,"
//            . "ge.ano "
//            . ""
//            . "FROM efetivo_mes as em "
//            . "JOIN efetivo_escala as ee "
//            . "ON ee.efetivo = em.id "
//            . ""
//            . "JOIN escalas as e "
//            . "ON ee.escala = e.id "
//            . ""
//            . "JOIN servico as s "
//            . "ON s.operador = ee.id "
//            . ""
//            . "JOIN turnos as t "
//            . "ON t.id = s.turno "
//            . ""
//            . "JOIN tipo_escala as te "
//            . "ON te.id =  s.tipo_escala "
//            . ""
//            . "JOIN grupos_escalas as ge "
//            . "ON ge.id = te.grupo_escalas "
//            . ""
//            . "WHERE em.usuario IN (";
//    $var = array();
//    $binding[] = "";
//    foreach ($usuariosEscala as $id) {
//        $var[] = "?";
//        $binding[0] .= "s";
//        $binding[] = $id;
//    }
//
//    $sql .= implode(",", $var) . ") AND ge.id IN(";
//    $var = array();
//    foreach ($grupos as $id) {
//        $var[] = "?";
//        $binding[0] .= "i";
//        $binding[] = $id;
//    }
//
//    $sql .= implode(",", $var) . ")";
//
//    return sql_busca($sql, $GLOBALS['conn'], $binding);
//}
//function servicoTotaisRISAER($usuariosEscala, $grupos) {
//
//    //pegar os grupos de escalas onde usuarios que estao nesta escala possuem servico
//    $sql = "SELECT "
//            . "em.usuario, "
//            . "em.id as em_id, "
//            . "em.legenda as usu_leg, "
//            . "em.manutencao, "
//            . "sr.id as svcr_id, "
//            . "sr.dia, "
//            . "r.id as risaer_id, "
//            . "r.legenda as risaer_leg, "
//            . "r.tipo_etapa, "
//            . "r.inicio, "
//            . "r.termino, "
//            . "te.tipo as tipo_escala, "
//            . "ge.mes, "
//            . "ge.ano, "
//            . "ge.orgao "
//            . ""
//            . "FROM efetivo_mes as em "
//            . ""
//            . "JOIN servico_risaer as sr "
//            . "ON sr.efetivo_mes = em.id "
//            . ""
//            . "LEFT JOIN risaer as r "
//            . "ON r.id = sr.servico "
//            . ""
//            . "LEFT JOIN tipo_escala as te "
//            . "ON te.id =  sr.tipo_escala "
//            . ""
//            . "LEFT JOIN grupos_escalas as ge "
//            . "ON ge.id = te.grupo_escalas "
//            . ""
//            . "WHERE em.usuario IN (";
//    $var = array();
//    $binding[] = "";
//    foreach ($usuariosEscala as $id) {
//        $var[] = "?";
//        $binding[0] .= "s";
//        $binding[] = $id;
//    }
//
//    $sql .= implode(",", $var) . ") AND em.grupo_escalas IN(";
//    $var = array();
//    foreach ($grupos as $id) {
//        $var[] = "?";
//        $binding[0] .= "i";
//        $binding[] = $id;
//    }
//
//    $sql .= implode(",", $var) . ")";
//
//    return sql_busca($sql, $GLOBALS['conn'], $binding);
//}
//function pegarGruposAtualAnteriorPosteriorEfetivoEscala($usuarios, $mesAnoArray) {
//
//
//    //pegar os grupos de escalas onde usuarios que estao nesta escala possuem servico
//    $sql = "SELECT DISTINCT ge.* "
//            . "FROM efetivo_mes as em "
//            . "JOIN grupos_escalas as ge "
//            . "ON em.grupo_escalas =  ge.id "
//            . "WHERE usuario IN (";
//    $var = array();
//    $binding[] = "";
//    foreach ($usuarios as $id) {
//        $var[] = "?";
//        $binding[0] .= "i";
//        $binding[] = $id;
//    }
//    $sql .= implode(",", $var) . ") AND (";
//    $var = array();
//    foreach ($mesAnoArray as $ma) {
//        $var[] = "(ge.mes = ? AND ge.ano = ?)";
//        $binding[0] .= "ii";
//        $binding[] = $ma[0];
//        $binding[] = $ma[1];
//    }
//    $sql .= implode(" OR ", $var) . ") ";
//
//    return sql_busca($sql, $GLOBALS['conn'], $binding);
//}

function qtdTrocasOpr($em_id) {
    $sql = "SELECT COUNT(*) as qtd "
            . "FROM trocas as t "
            . "JOIN efetivo_escala as ee "
            . "ON ee.id = t.proponente "
            . "JOIN efetivo_escala as ee2 "
            . "ON ee2.id = t.proposto "
            . "WHERE ee.efetivo = ? OR ee2.efetivo = ? "; //deixe sempre  um espaco no final
    $binding = array('ii', $em_id, $em_id);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarStatusTrocas($grupo, $tipo) {
    $sql = "SELECT trocas_liberadas "; //deixe sempre  um espaco no final
    $sql .= "FROM tipo_escala "; //deixe sempre  um espaco no final
    $sql .= "WHERE grupo_escalas = ? AND tipo = ? "; //deixe sempre  um espaco no final    
    $binding = array('ii', $grupo, $tipo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function cargaHorariaOprMes($operador, $mes, $ano, $tipo) {
    $ssbq1 = "(CASE WHEN"
            . " s.turno IS NULL "
            . "THEN"
            . " 0 "
            . "ELSE"
            . " t.termino "
            . "END)";

    $ssbq2 = "(CASE WHEN"
            . " s.turno IS NULL "
            . "THEN"
            . " 0 "
            . "ELSE"
            . " t.inicio "
            . "END)";

    $sbq1 = ""
            . "SUM("
            . "TIME_TO_SEC("
            . "CASE WHEN"
            . " SUBTIME($ssbq1, $ssbq2) < 0 "
            . "THEN"
            . " SUBTIME($ssbq1, $ssbq2) + INTERVAL 24 HOUR "
            . "ELSE"
            . " SUBTIME($ssbq1, $ssbq2) "
            . "END"
            . ")"
            . ")";


    $sql = "SELECT SEC_TO_TIME($sbq1) as chm "
            . "FROM efetivo_mes AS em "
            . "JOIN efetivo_mes AS em2 "
            . "ON em2.usuario = em.usuario "
            . "JOIN grupos_escalas AS ge "
            . "ON ge.id = em2.grupo_escalas "
            . "LEFT JOIN efetivo_escala as ee "
            . "ON ee.efetivo = em2.id "
            . "LEFT JOIN servico as s "
            . "ON s.operador = ee.id "
            . "JOIN tipo_escala as te "
            . "ON s.tipo_escala = te.id "
            . "LEFT JOIN turnos as t "
            . "ON s.turno = t.id "
            . "WHERE em.id = ? AND ge.mes = ? AND ge.ano = ? AND te.tipo = ? ";
    $binding = array('iiii', $operador, $mes, $ano, $tipo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function cargaHorariaOprCHT($operador, $ano, $mes, $grupo, $tipo, $usu) {
    unset($sqls);
    unset($bindings);

    $mesAnterior1 = ($mes - 1) == 0 ? 12 : ($mes - 1);
    $anoMesAnterior1 = ($mes - 1) == 0 ? $ano - 1 : $ano;

    $mesAnterior2 = ($mes - 2) <= 0 ? 12 + $mes - 2 : ($mes - 2);
    $anoMesAnterior2 = ($mes - 2) <= 0 ? $ano - 1 : $ano;

    $mesAnterior3 = ($mes - 3) <= 0 ? 12 + $mes - 3 : ($mes - 3);
    $anoMesAnterior3 = ($mes - 3) <= 0 ? $ano - 1 : $ano;
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    //retorna 0 para os campos nulos a fim  de se realizar as operacoes abaixo
    $ssbq1 = "(CASE WHEN s.turno IS NULL THEN 0 ELSE t.termino END)";
    $ssbq2 = "(CASE WHEN s.turno IS NULL THEN 0 ELSE t.inicio END)";

    //transforma em segundos a diferenca de tempo entre o inicio e termino do turno, corrigindo so valores para turnos que terminam em dias diferentes
    //em seguida soma todos esses valores
    $sbq1 = "SUM(TIME_TO_SEC(CASE WHEN SUBTIME($ssbq1, $ssbq2) < 0 THEN SUBTIME($ssbq1, $ssbq2) + INTERVAL 24 HOUR ELSE SUBTIME($ssbq1, $ssbq2) END))";

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //adiciona o grupo atual para uma variavel local
    $sql = "SET @grupoAtual = ? ";
    $sqls[] = $sql;
    $bindings[] = array('i', $grupo);

    //pega qual o id utilizado para o operador no mes em questao, retornando 0 se este operador não existir no mes
    $sql = "SET @efetivoMesAtual = ?";
    $sqls[] = $sql;
    $bindings[] = array('i', $operador);

    //pega qual o orgao da escala para ser utilizado nos meses anteriores
    $sql = "SELECT orgao INTO @orgao "
            . "FROM grupos_escalas "
            . "WHERE id = @grupoAtual ";
    $sqls[] = $sql;
    $bindings[] = null;

    //pega qual o id utilizado para o tipo da escala no mes em questao, retornando 0 se este tipo não existir no mes
    $sql = "SET @idTipoAtual = CASE WHEN"
            . " (SELECT COUNT(*) FROM tipo_escala WHERE grupo_escalas = @grupoAtual AND tipo = ?) = 0 "
            . "THEN"
            . " 0 "
            . "ELSE"
            . "  (SELECT id FROM tipo_escala WHERE grupo_escalas = @grupoAtual AND tipo = ?)"
            . "END";
    $sqls[] = $sql;
    $bindings[] = array('ii', $tipo, $tipo);

    //faz o somatorio das horas em que o operador trabalhou no mes em questao
    $sql = "SELECT ($sbq1) INTO @segundosAtual ";
    $sql .= "FROM servico AS s ";
    $sql .= "JOIN efetivo_escala AS ee ";
    $sql .= "ON ee.id = s.operador ";
    $sql .= "JOIN efetivo_mes AS em ";
    $sql .= "ON em.id = ee.efetivo ";
    $sql .= "LEFT JOIN turnos AS t ";
    $sql .= "ON t.id = s.turno ";
    $sql .= "WHERE em.id = @efetivoMesAtual AND s.tipo_escala = @idTipoAtual";
    $sqls[] = $sql;
    $bindings[] = null;

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //verifica se o grupo do mes anterior existe, caso contrario retorna 0
    $sql = "SET @grupoAnterior1 = CASE WHEN"
            . " (SELECT COUNT(*) FROM grupos_escalas WHERE ano = ? AND mes = ? AND orgao = @orgao) = 0 "
            . "THEN"
            . " 0 "
            . "ELSE"
            . "  (SELECT id FROM grupos_escalas WHERE ano = ? AND mes = ? AND orgao = @orgao)"
            . "END";
    $sqls[] = $sql;
    $bindings[] = array('iiii', $anoMesAnterior1, $mesAnterior1, $anoMesAnterior1, $mesAnterior1);

    //verifica se o operador existe no mes anterior, caso contrario retorna 0
    $sql = "SET @efetivoMesAnterior1 = CASE WHEN"
            . " (SELECT COUNT(*) FROM efetivo_mes WHERE grupo_escalas = @grupoAnterior1 AND usuario = ?) = 0 "
            . "THEN"
            . " 0 "
            . "ELSE"
            . "  (SELECT id FROM efetivo_mes WHERE grupo_escalas = @grupoAnterior1 AND usuario = ?)"
            . "END";
    $sqls[] = $sql;
    $bindings[] = array('ss', $usu, $usu);

    //verifica qual o maior tipo de escala existente no mes anterior, caso o grupo nao existe retorna 0
    $sql = "SET @idTipoAnterior1 = CASE WHEN"
            . " (SELECT COUNT(*) FROM tipo_escala WHERE grupo_escalas = @grupoAnterior1 ORDER BY tipo DESC LIMIT 1) = 0 "
            . "THEN"
            . " 0 "
            . "ELSE"
            . "  (SELECT id FROM tipo_escala WHERE grupo_escalas = @grupoAnterior1 ORDER BY tipo DESC LIMIT 1)"
            . "END";
    $sqls[] = $sql;
    $bindings[] = null;

    $sql = "SELECT ($sbq1) INTO @segundosAnterior1 ";
    $sql .= "FROM servico AS s ";
    $sql .= "JOIN efetivo_escala AS ee ";
    $sql .= "ON ee.id = s.operador ";
    $sql .= "JOIN efetivo_mes AS em ";
    $sql .= "ON em.id = ee.efetivo ";
    $sql .= "LEFT JOIN turnos AS t ";
    $sql .= "ON t.id = s.turno ";
    $sql .= "WHERE em.id = @efetivoMesAnterior1 AND s.tipo_escala = @idTipoAnterior1";
    $sqls[] = $sql;
    $bindings[] = null;

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $sql = "SET @grupoAnterior2 = CASE WHEN"
            . " (SELECT COUNT(*) FROM grupos_escalas WHERE ano = ? AND mes = ? AND orgao = @orgao) = 0 "
            . "THEN"
            . " 0 "
            . "ELSE"
            . "  (SELECT id FROM grupos_escalas WHERE ano = ? AND mes = ? AND orgao = @orgao)"
            . "END";
    $sqls[] = $sql;
    $bindings[] = array('iiii', $anoMesAnterior2, $mesAnterior2, $anoMesAnterior2, $mesAnterior2);

    $sql = "SET @efetivoMesAnterior2 = CASE WHEN"
            . " (SELECT COUNT(*) FROM efetivo_mes WHERE grupo_escalas = @grupoAnterior2 AND usuario = ?) = 0 "
            . "THEN"
            . " 0 "
            . "ELSE"
            . "  (SELECT id FROM efetivo_mes WHERE grupo_escalas = @grupoAnterior2 AND usuario = ?)"
            . "END";
    $sqls[] = $sql;
    $bindings[] = array('ss', $usu, $usu);

    $sql = "SET @tipoAnterior2 = CASE WHEN"
            . " (SELECT COUNT(*) FROM tipo_escala WHERE grupo_escalas = @grupoAnterior2 ORDER BY tipo DESC LIMIT 1) = 0 "
            . "THEN"
            . " 0 "
            . "ELSE"
            . "  (SELECT tipo FROM tipo_escala WHERE grupo_escalas = @grupoAnterior2 ORDER BY tipo DESC LIMIT 1)"
            . "END";
    $sqls[] = $sql;
    $bindings[] = null;

    $sql = "SELECT ($sbq1) INTO @segundosAnterior2 ";
    $sql .= "FROM servico AS s ";
    $sql .= "JOIN efetivo_escala AS ee ";
    $sql .= "ON ee.id = s.operador ";
    $sql .= "JOIN efetivo_mes AS em ";
    $sql .= "ON em.id = ee.efetivo ";
    $sql .= "LEFT JOIN turnos AS t ";
    $sql .= "ON t.id = s.turno ";
    $sql .= "WHERE em.id = @efetivoMesAnterior2 AND s.tipo_escala = @idTipoAnterior2";
    $sqls[] = $sql;
    $bindings[] = null;

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $sql = "SET @grupoAnterior3 = CASE WHEN"
            . " (SELECT COUNT(*) FROM grupos_escalas WHERE ano = ? AND mes = ? AND orgao = @orgao) = 0 "
            . "THEN"
            . " 0 "
            . "ELSE"
            . "  (SELECT id FROM grupos_escalas WHERE ano = ? AND mes = ? AND orgao = @orgao)"
            . "END";
    $sqls[] = $sql;
    $bindings[] = array('iiii', $anoMesAnterior3, $mesAnterior3, $anoMesAnterior3, $mesAnterior3);

    $sql = "SET @efetivoMesAnterior3 = CASE WHEN"
            . " (SELECT COUNT(*) FROM efetivo_mes WHERE grupo_escalas = @grupoAnterior3 AND usuario = ?) = 0 "
            . "THEN"
            . " 0 "
            . "ELSE"
            . "  (SELECT id FROM efetivo_mes WHERE grupo_escalas = @grupoAnterior3 AND usuario = ?)"
            . "END";
    $sqls[] = $sql;
    $bindings[] = array('ss', $usu, $usu);

    $sql = "SET @tipoAnterior3 = CASE WHEN"
            . " (SELECT COUNT(*) FROM tipo_escala WHERE grupo_escalas = @grupoAnterior3 ORDER BY tipo DESC LIMIT 1) = 0 "
            . "THEN"
            . " 0 "
            . "ELSE"
            . "  (SELECT tipo FROM tipo_escala WHERE grupo_escalas = @grupoAnterior3 ORDER BY tipo DESC LIMIT 1)"
            . "END";
    $sqls[] = $sql;
    $bindings[] = null;

    $sql = "SELECT ($sbq1) INTO @segundosAnterior3 ";
    $sql .= "FROM servico AS s ";
    $sql .= "JOIN efetivo_escala AS ee ";
    $sql .= "ON ee.id = s.operador ";
    $sql .= "JOIN efetivo_mes AS em ";
    $sql .= "ON em.id = ee.efetivo ";
    $sql .= "LEFT JOIN turnos AS t ";
    $sql .= "ON t.id = s.turno ";
    $sql .= "WHERE em.id = @efetivoMesAnterior2 AND s.tipo_escala = @idTipoAnterior2";
    $sqls[] = $sql;
    $bindings[] = null;

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $sql = "SET @segundosAtual = CASE WHEN @segundosAtual IS NULL THEN 0 ELSE @segundosAtual END";
    $sqls[] = $sql;
    $bindings[] = null;

    $sql = "SET @segundosAnterior1 = CASE WHEN @segundosAnterior1 IS NULL THEN 0 ELSE @segundosAnterior1 END";
    $sqls[] = $sql;
    $bindings[] = null;

    $sql = "SET @segundosAnterior2 = CASE WHEN @segundosAnterior2 IS NULL THEN 0 ELSE @segundosAnterior2 END";
    $sqls[] = $sql;
    $bindings[] = null;

    $sql = "SET @segundosAnterior3 = CASE WHEN @segundosAnterior3 IS NULL THEN 0 ELSE @segundosAnterior3 END";
    $sqls[] = $sql;
    $bindings[] = null;

    $sql = "SET @resultado = (@segundosAtual + @segundosAnterior1 + @segundosAnterior2 + @segundosAnterior3)";
    $sqls[] = $sql;
    $bindings[] = null;

    $sql = "SELECT SEC_TO_TIME(@resultado) AS resultado";
    $sqls[] = $sql;
    $bindings[] = null;
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////
    return sql_transaction($sqls, $GLOBALS['conn'], $bindings);
}

function qtdEtapas($operador, $mes, $ano, $tipo) {
    $sql = "SELECT SUM(CASE WHEN s.turno IS NULL THEN 0 ELSE t.etapa_full END) as qtd "
            . "FROM efetivo_mes AS em "
            . "JOIN efetivo_mes AS em2 "
            . "ON em2.usuario = em.usuario "
            . "JOIN grupos_escalas AS ge "
            . "ON ge.id = em2.grupo_escalas "
            . "LEFT JOIN efetivo_escala as ee "
            . "ON ee.efetivo = em2.id "
            . "LEFT JOIN servico as s "
            . "ON s.operador = ee.id "
            . "JOIN tipo_escala as te "
            . "ON s.tipo_escala = te.id "
            . "LEFT JOIN turnos as t "
            . "ON s.turno = t.id "
            . "WHERE em.id = ? AND ge.mes = ? AND ge.ano = ? AND te.tipo = ? ";
    $binding = array('iiii', $operador, $mes, $ano, $tipo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

//function pegarlistaCombinacoes($grupo) {
//    $sql = "SELECT rc.turno1, "
//            . "rc.turno2, "
//            . "tu1.legenda as leg_turno1, "
//            . "tu2.legenda as leg_turno2 "
//            . ""
//            . "FROM restricoes_combinacoes_turno as rc "
//            . ""
//            . "JOIN turnos as tu1 "
//            . "ON tu1.id = rc.turno1 "
//            . ""
//            . "JOIN turnos as tu2 "
//            . "ON tu2.id = rc.turno2 "
//            . ""
//            . "WHERE rc.grupo_escalas = ? ";
//    $binding = array('i', $grupo);
//    return sql_busca($sql, $GLOBALS['conn'], $binding);
//}

/* * *****************************************************************************
 *                              TURNOS
 * **************************************************************************** */

function listaTurnos($grupo) {
    $sql = "SELECT * "; //deixe sempre  um espaco no final
    $sql .= "FROM turnos "; //deixe sempre  um espaco no final
    $sql .= "WHERE grupo_escalas = ? "; //deixe sempre  um espaco no final    
    $binding = array('i', $grupo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function listaTurnosEscalas($grupo) {
    $sql = "SELECT te.escala, t.id, t.nome, t.legenda "; //deixe sempre  um espaco no final
    $sql .= "FROM turno_escala AS te "
            . "JOIN turnos AS t "
            . "ON t.id = te.turno "; //deixe sempre  um espaco no final
    $sql .= "WHERE te.grupo_escalas = ? "; //deixe sempre  um espaco no final    
    $binding = array('i', $grupo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function buscarTurno($col, $valor, $grupo) {

    //checar se o nome do turno já existe
    $sql = "SELECT * "; //deixe sempre  um espaco no final
    $sql .= "FROM turnos "; //deixe sempre  um espaco no final
    $sql .= "WHERE $col = ? AND grupo_escalas = ? "; //deixe sempre  um espaco no final
    $binding = array('si', $valor, $grupo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function buscarRISAER($col, $valor, $grupo) {

    //checar se o nome do turno já existe
    $sql = "SELECT * "; //deixe sempre  um espaco no final
    $sql .= "FROM risaer "; //deixe sempre  um espaco no final
    $sql .= "WHERE $col = ? AND grupo_escalas = ? "; //deixe sempre  um espaco no final
    $binding = array('si', $valor, $grupo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function buscarTurnosEscala($id) {
    $sql = "SELECT t.legenda, t.id "; //deixe sempre  um espaco no final
    $sql .= "FROM turno_escala as te "; //deixe sempre  um espaco no final
    $sql .= "JOIN turnos as t "; //deixe sempre  um espaco no final
    $sql .= "ON t.id = te.turno "; //deixe sempre  um espaco no final
    $sql .= "WHERE escala = ? "; //deixe sempre  um espaco no final
    $binding = array('i', $id);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function buscarTurnoDiferente($col, $nome, $grupo, $id) {
    $col = mb_strtoupper($col, "UTF-8");
    $sql = "SELECT * "; //deixe sempre  um espaco no final
    $sql .= "FROM turnos "; //deixe sempre  um espaco no final
    $sql .= "WHERE $col = ? AND grupo_escalas = ? AND id != ? "; //deixe sempre  um espaco no final
    $binding = array('sii', $nome, $grupo, $id);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function buscarRISAERDiferente($col, $nome, $grupo, $id) {
    $col = mb_strtoupper($col, "UTF-8");
    $sql = "SELECT * "; //deixe sempre  um espaco no final
    $sql .= "FROM risaer "; //deixe sempre  um espaco no final
    $sql .= "WHERE $col = ? AND grupo_escalas = ? AND id != ? "; //deixe sempre  um espaco no final
    $binding = array('sii', $nome, $grupo, $id);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function inserirTurno($nome, $legenda, $inicio, $termino, $etapa, $periodo, $posNoturno, $grupo) {
    $sql = "INSERT INTO turnos (nome, legenda, inicio, termino, etapa_full, grupo_escalas, periodo, pos_noturno) "; //deixe sempre  um espaco no final
    $sql .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?) "; //deixe sempre  um espaco no final
    $binding = array('ssssiiii', $nome, $legenda, $inicio, $termino, $etapa, $grupo, $periodo, $posNoturno);
    return sql_ins($sql, $GLOBALS['conn'], $binding);
}

function inserirRISAER($nome, $legenda, $inicio, $termino, $maior24, $etapa, $dant, $dpost, $grupo) {

    $sql = "INSERT INTO risaer (nome, legenda, inicio, termino, tipo_etapa, intervalo_antes, intervalo_depois, grupo_escalas, mais_q_24h) "; //deixe sempre  um espaco no final
    $sql .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) "; //deixe sempre  um espaco no final
    $binding = array('ssssiiiii', $nome, $legenda, $inicio, $termino, $etapa, $dant, $dpost, $grupo, $maior24);
    return sql_ins($sql, $GLOBALS['conn'], $binding);
}

function removerTurno($id) {
    $sql = "DELETE FROM turnos ";
    $sql .= "WHERE id = ? "; //deixe sempre  um espaco no final    
    $binding = array('i', $id);
    return sql_del($sql, $GLOBALS['conn'], $binding);
}

function removerGrupo($id) {
    $sql = "DELETE FROM grupos_escalas ";
    $sql .= "WHERE id = ? "; //deixe sempre  um espaco no final    
    $binding = array('i', $id);
    return sql_del($sql, $GLOBALS['conn'], $binding);
}

function alterarTurno($nome, $legenda, $inicio, $termino, $etapa, $periodo, $posNoturno, $id) {
    $sql = "UPDATE turnos "; //deixe sempre  um espaco no final (nome, legenda, inicio, termino, etapa_full, grupo_escalas) 
    $sql .= "SET nome = ?, legenda = ?, inicio = ?, termino = ?, etapa_full = ?, periodo = ?, pos_noturno = ? "; //deixe sempre  um espaco no final
    $sql .= "WHERE id = ? "; //deixe sempre  um espaco no final
    $binding = array('ssssiiii', $nome, $legenda, $inicio, $termino, $etapa, $periodo, $posNoturno, $id);
    return sql_up($sql, $GLOBALS['conn'], $binding);
}

/* * *****************************************************************************
 *                              ESCALAS
 * **************************************************************************** */

//function listaEscalas($grupo) {
//    $sql = "SELECT * "; //deixe sempre  um espaco no final
//    $sql .= "FROM escalas "; //deixe sempre  um espaco no final
//    $sql .= "WHERE grupo_escalas = ? "; //deixe sempre  um espaco no final;
////    $sql = "SELECT es.id, es.nome, es.legenda, es.grupo_escalas, es.ch_maxima, es.ch_minima, es.qtd_svc, es.soma_geral, pe.publicada, pe.tipo ";
////    $sql .= "FROM escalas es ";
////    $sql .= "LEFT JOIN publicacao_escala as pe ";
////    $sql .= "ON pe.escala = es.id ";
////    $sql .= "WHERE es.grupo_escalas = ? ";
//    $binding = array('i', $grupo);
//    return sql_busca($sql, $GLOBALS['conn'], $binding);
//}

function buscarEscala($col, $nome, $grupo) {
    $col = mb_strtoupper($col, "UTF-8");
    $sql = "SELECT * "; //deixe sempre  um espaco no final
    $sql .= "FROM escalas "; //deixe sempre  um espaco no final
    $sql .= "WHERE $col = ? AND grupo_escalas = ? "; //deixe sempre  um espaco no final
    $binding = array('si', $nome, $grupo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function buscarEscalaDiferente($col, $nome, $grupo, $id) {
    $col = mb_strtoupper($col, "UTF-8");
    $sql = "SELECT * "; //deixe sempre  um espaco no final
    $sql .= "FROM escalas "; //deixe sempre  um espaco no final
    $sql .= "WHERE $col = ? AND grupo_escalas = ? "; //deixe sempre  um espaco no final
    $sql .= "AND id != ? ";
    $binding = array('sii', $nome, $grupo, $id);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function listaEscalasGrupo($grupo) {
    $sql = "SELECT * "; //deixe sempre  um espaco no final
    $sql .= "FROM escalas "; //deixe sempre  um espaco no final
    $sql .= "WHERE grupo_escalas = ? "; //deixe sempre  um espaco no final    
    $binding = array('i', $grupo);
    $resposta = sql_busca($sql, $GLOBALS['conn'], $binding);

    $re = array();
    foreach ($resposta as $r) {
        $re[$r['id']] = $r;
    }

    return $re;
}

function checarGrupoEscala($mes, $ano, $orgao, $unidade) {
    $sql = "SELECT id "; //deixe sempre  um espaco no final
    $sql .= "FROM grupos_escalas "; //deixe sempre  um espaco no final
    $sql .= "WHERE mes = ? AND ano = ? AND orgao = ? AND unidade = ? "; //deixe sempre  um espaco no final
    $binding = array('iiis', $mes, $ano, $orgao, $unidade);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function inserirEscala($nome, $legenda, $grupo, $ch1, $ch2, $svc) {
    $sql = "INSERT INTO escalas (nome, legenda, grupo_escalas, ch_minima, ch_maxima, qtd_svc) "; //deixe sempre  um espaco no final
    $sql .= "VALUES (?, ?, ?, ?, ?, ?) "; //deixe sempre  um espaco no final
    $binding = array('ssiiii', $nome, $legenda, $grupo, $ch1, $ch2, $svc);
    return sql_ins($sql, $GLOBALS['conn'], $binding);
}

function inserirTurnoEscala($id_escala, $t, $grupo) {
    $sql = "INSERT INTO turno_escala (escala, turno, grupo_escalas, ref_soma) "; //deixe sempre  um espaco no final
    $sql .= "VALUES (?, ?, ?, 0) "; //deixe sempre  um espaco no final
    $binding = array('iii', $id_escala, $t, $grupo);
    return sql_ins($sql, $GLOBALS['conn'], $binding);
}

function listaFuncoesEscala() {
    $sql = "SELECT * "; //deixe sempre  um espaco no final
    $sql .= "FROM funcoes_escala "; //deixe sempre  um espaco no final
    $resposta = sql_busca($sql, $GLOBALS['conn']);
    $re = array();
    foreach ($resposta as $r) {
        $re[$r['id']] = $r;
    }
    return $re;
}

function listaEfetivo($grupo) {
//    $sql = "SELECT em.id "; //deixe sempre  um espaco no final
//    $sql .= "FROM efetivo_mes as em "; //deixe sempre  um espaco no final
//    $sql .= "WHERE grupo_escalas = ? "; //deixe sempre  um espaco no final
//    $sub_query = $sql;

    $sql = "SELECT em.id, em.legenda, em.grupo_escalas, em.usuario as operador, fe.nome as funcao, e.legenda as escala, em.manutencao as manutencao ";
    $sql .= "FROM efetivo_mes as em ";
    $sql .= "LEFT OUTER JOIN efetivo_escala as ee "; //deixe sempre  um espaco no final
    $sql .= "ON ee.efetivo = em.id "; //deixe sempre  um espaco no final    
    $sql .= "JOIN funcoes_escala as fe "; //deixe sempre  um espaco no final
    $sql .= "ON fe.id = em.funcao_escala "; //deixe sempre  um espaco no final 
    $sql .= "LEFT OUTER JOIN escalas as e "; //deixe sempre  um espaco no final
    $sql .= "ON e.id = ee.escala "; //deixe sempre  um espaco no final
    $sql .= "WHERE em.grupo_escalas = ? "; //efetivo IN ($sub_query) ";
    $sql .= "ORDER BY em.legenda ";

    $binding = array('i', $grupo);
    $resposta = sql_busca($sql, $GLOBALS['conn'], $binding);
    $retorno = array();
    foreach ($resposta as $r) {
        $retorno[$r['id']]['legenda'] = $r['legenda'];
        $retorno[$r['id']]['operador'] = $r['operador'];
        $retorno[$r['id']]['funcao'] = $r['funcao'];
        $retorno[$r['id']]['escalas'][] = $r['escala'];
        $retorno[$r['id']]['manutencao'] = $r['manutencao'];
    }
    return $retorno;
}

function pegarOperEfetivo($id) {
    $sql = "SELECT em.id, em.legenda, em.usuario, em.funcao_escala, ee.escala, em.manutencao "; //deixe sempre  um espaco no final
    $sql .= "FROM efetivo_mes as em "; //deixe sempre  um espaco no final
    $sql .= "LEFT JOIN efetivo_escala as ee "; //deixe sempre  um espaco no final
    $sql .= "ON ee.efetivo = em.id "; //deixe sempre  um espaco no final
    $sql .= "WHERE em.id = ? "; //deixe sempre  um espaco no final

    $binding = array('i', $id);
    $resposta = sql_busca($sql, $GLOBALS['conn'], $binding);


    foreach ($resposta as $re) {
        $retorno[$re['id']]['legenda'] = $re['legenda'];
        $retorno[$re['id']]['operador'] = $re['usuario'];
        $retorno[$re['id']]['funcao'] = $re['funcao_escala'];
        $retorno[$re['id']]['manutencao'] = $re['manutencao'];
        $retorno[$re['id']]['escalas'][] = $re['escala'];
    }
    $resposta = $retorno[$id];

    return $resposta;
}

function removerEscala($id) {
    $sql = "DELETE FROM escalas ";
    $sql .= "WHERE id = ? "; //deixe sempre  um espaco no final    
    $binding = array('i', $id);
    return sql_del($sql, $GLOBALS['conn'], $binding);
}

function buscarEscalasComTurno($turno) {
    $sql = "SELECT escala ";
    $sql .="FROM turno_escala ";
    $sql .="WHERE turno = ?";
    $binding = array('i', $turno);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function qtdTurnos($esc) {
    $sql = "SELECT COUNT(turno) as qtd ";
    $sql .="FROM turno_escala ";
    $sql .="WHERE escala = ?";
    $binding = array('i', $esc);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function alterarEscala($nome, $legenda, $ch0, $ch1, $id, $svc) {
    $sql = "UPDATE escalas "; //deixe sempre  um espaco no final
    $sql .= "SET nome = ?, legenda = ?, ch_minima = ?, ch_maxima = ?, qtd_svc = ? "; //deixe sempre  um espaco no final
    $sql .= "WHERE id = ? "; //deixe sempre  um espaco no final
    $binding = array('ssiiii', $nome, $legenda, $ch0, $ch1, $svc, $id);
    return sql_up($sql, $GLOBALS['conn'], $binding);
}

function buscarTurnoEscala($id) {
    $sql = "SELECT turno ";
    $sql .= "FROM turno_escala ";
    $sql .= "WHERE escala = ? ";
    $binding = array('i', $id);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function deletarTurnoEscala($ta, $id) {
    $sql = "DELETE FROM turno_escala "; //deixe sempre  um espaco no final
    $sql .= "WHERE turno = ? AND escala = ? "; //deixe sempre  um espaco no final
    $binding = array('ii', $ta, $id);
    return sql_ins($sql, $GLOBALS['conn'], $binding);
}

/* * *****************************************************************************
 *                              EFETIVO
 * **************************************************************************** */

function buscarEfetivoEscala($id) {
    $sql = "SELECT ee.escala, es.legenda "; //deixe sempre  um espaco no final
    $sql .= "FROM efetivo_escala as ee "; //deixe sempre  um espaco no final
    $sql .= "JOIN escalas as es ";
    $sql .= "ON es.id = ee.escala ";
    $sql .= "WHERE efetivo = ? "; //deixe sempre  um espaco no final
    $binding = array('i', $id);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarHabilitacoesValidasUsuario($cadastroID) {
    $sub1 = "(SELECT MONTH(h2.dt_validade) FROM habilitacoes h2 WHERE h2.habilitacaoID = h.habilitacaoID)";
    $sub2 = "(SELECT YEAR(h2.dt_validade) FROM habilitacoes h2 WHERE h2.habilitacaoID = h.habilitacaoID)";
    $sub3 = "(CASE WHEN c.posto = 'CV' THEN c.NOME ELSE c.nome_guerra END)";

    $sub4 = "(CASE WHEN"
            . " $sub2 > ? "
            . "THEN"
            . "  TRUE "
            . "ELSE"
            . " (CASE WHEN"
            . "   $sub2 = ? "
            . " THEN"
            . "    (CASE WHEN"
            . "      $sub1 >= ? "
            . "    THEN"
            . "     TRUE "
            . "    ELSE"
            . "     FALSE"
            . "    END) "
            . " ELSE"
            . "  FALSE"
            . " END)"
            . "END)";

    $sql = "SELECT h.setorID, h.unidadeID , h.cadastroID AS usuario_id, c.posto AS pg, $sub3 AS ng, ch.h AS ordem "
            . "FROM habilitacoes h "
            . "JOIN cadastros c "
            . "ON c.cadastroID = h.cadastroID "
            . "JOIN cadastros_hierarquia ch "
            . "ON ch.posto = c.posto "
            . "WHERE h.cadastroID = ? AND h.deletedat IS NULL "//AND $sub4 "
            . "ORDER BY ng ";
    $binding = array('s', $cadastroID);
    return sql_busca($sql, $GLOBALS['connL'], $binding);
}

function listaOrgaoFuncaoUsuario($cpf) {
    $sql = "SELECT org, unidadeID "; //deixe sempre  um espaco no final
    $sql .= "FROM root_usuarios "; //deixe sempre  um espaco no final    
    $sql .= "WHERE cpf = ? AND deletedat IS NULL "; //deixe sempre  um espaco no final

    $binding = array('i', $cpf);
    return sql_busca($sql, $GLOBALS['connL'], $binding);
}

function pegarOrgaoId($orgaoId) {
    $sql = "SELECT hs.setor "; //deixe sempre  um espaco no final
    $sql .= "FROM habilitacoes_setores hs "; //deixe sempre  um espaco no final        
    $sql .= "WHERE hs.setorID = ? "; //deixe sempre  um espaco no final

    $binding = array('i', $orgaoId);
    return sql_busca($sql, $GLOBALS['connL'], $binding);
}

function pegarUnidadeId($unidadeId) {
    $sql = "SELECT ur.nome as unidade "; //deixe sempre  um espaco no final
    $sql .= "FROM unidades_regionais ur "; //deixe sempre  um espaco no final          
    $sql .= "WHERE ur.regionalID = ? "; //deixe sempre  um espaco no final

    $binding = array('s', $unidadeId);
    return sql_busca($sql, $GLOBALS['connL'], $binding);
}

function pegarListaUsuariosHabilitacaoValida($orgID, $ano, $mes, $unidade) {
    $sub1 = "(SELECT MONTH(h2.dt_validade) FROM habilitacoes h2 WHERE h2.habilitacaoID = h.habilitacaoID)";
    $sub2 = "(SELECT YEAR(h2.dt_validade) FROM habilitacoes h2 WHERE h2.habilitacaoID = h.habilitacaoID)";
    $sub3 = "(CASE WHEN c.posto = 'CV' THEN c.NOME ELSE c.nome_guerra END)";

    $sub4 = "(CASE WHEN"
            . " $sub2 > ? "
            . "THEN"
            . "  TRUE "
            . "ELSE"
            . " (CASE WHEN"
            . "   $sub2 = ? "
            . " THEN"
            . "    (CASE WHEN"
            . "      $sub1 >= ? "
            . "    THEN"
            . "     TRUE "
            . "    ELSE"
            . "     FALSE"
            . "    END) "
            . " ELSE"
            . "  FALSE"
            . " END)"
            . "END)";

    $sql = "SELECT h.cadastroID AS usuario_id, c.posto AS pg, $sub3 AS ng, ch.h AS ordem "
            . "FROM habilitacoes h "
            . "JOIN cadastros c "
            . "ON c.cadastroID = h.cadastroID "
            . "JOIN cadastros_hierarquia ch "
            . "ON ch.posto = c.posto "
            . "WHERE h.setorID = ? AND h.unidadeID = ? h.deletedat IS NULL AND $sub4 "
            . "ORDER BY ng ";
    $binding = array('isiii', $orgID, $unidade, $ano, $ano, $mes);
    return sql_busca($sql, $GLOBALS['connL'], $binding);
}

function pegarListaUsuariosHabilitados() {

    $sub3 = "(CASE WHEN c.posto = 'CV' THEN c.NOME ELSE c.nome_guerra END)";
    $sql = "SELECT h.cadastroID AS usuario_id, c.posto AS pg, $sub3 AS ng, ch.h as ordem "
            . "FROM habilitacoes h "
            . "JOIN cadastros c "
            . "ON c.cadastroID = h.cadastroID "
            . "JOIN cadastros_hierarquia ch "
            . "ON ch.posto = c.posto "
            . "WHERE h.setorID = ? AND h.unidadeID = ? ";
    $binding = array('is', $GLOBALS['sessao']['orgao_usu_id'], $GLOBALS['sessao']['unidade_usu_id']);
    $retorno = sql_busca($sql, $GLOBALS['connL'], $binding);
    $usu_porID = array();
    foreach ($retorno as $u) {
        $primeiro = explode(" ", $u['ng'])[0];
        $u['ng'] = $u['pg'] == 'CV' ? $primeiro : $u['ng'];
        $usu_porID[$u['usuario_id']] = $u;
    }

    return $usu_porID;
}

//
//function listaUsuOrgFunc($usuariosSistema) {
//    $sql = "SELECT * "; //deixe sempre  um espaco no final
//    $sql .= "FROM orgao_funcao "; //deixe sempre  um espaco no final
//    $sql .= "WHERE orgao_id = ? "; //deixe sempre  um espaco no final 
//    $sql .= "ORDER BY usuario_id ASC ";
//    $binding = array('s', $GLOBALS['sessao']['orgao_usu']);
//
//    $resposta = sql_busca($sql, $GLOBALS['conn'], $binding);
//
//    if (!$resposta['erro']) {
//        $orgFunc = $resposta['retorno'];
//        //ordenando por graduacao            
//        $orgFuncOrd = array();
//        foreach ($orgFunc as $or) {
//            $ordem = $usuariosSistema[$or['usuario_id']]['ord'];
//            $orgFuncOrd[$ordem][] = $or;
//        }
//        ksort($orgFuncOrd);
//        unset($orgFunc);
//        foreach ($orgFuncOrd as $or) {
//            foreach ($or as $ep) {
//                $orgFunc[] = $ep;
//            }
//        }
//        $resposta['retorno'] = $orgFunc;
//    }
//    return $resposta;
//}

function checarUsuEfetivo($usuario, $grupo) {
    $sql = "SELECT id "; //deixe sempre  um espaco no final
    $sql .= "FROM efetivo_mes "; //deixe sempre  um espaco no final
    $sql .= "WHERE usuario = ? AND grupo_escalas = ? "; //deixe sempre  um espaco no final
    $binding = array('si', $usuario, $grupo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function checarLegEfetivo($legenda, $grupo) {
    $sql = "SELECT id "; //deixe sempre  um espaco no final
    $sql .= "FROM efetivo_mes "; //deixe sempre  um espaco no final
    $sql .= "WHERE legenda = ? AND grupo_escalas = ? "; //deixe sempre  um espaco no final
    $binding = array('si', $legenda, $grupo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function checarLegEfetivoDiferente($legenda, $grupo, $id) {
    $sql = "SELECT id "; //deixe sempre  um espaco no final
    $sql .= "FROM efetivo_mes "; //deixe sempre  um espaco no final
    $sql .= "WHERE legenda = ? AND grupo_escalas = ? AND id != ? "; //deixe sempre  um espaco no final
    $binding = array('sii', $legenda, $grupo, $id);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function inserirEfetivo($usuario, $legenda, $grupo, $funcao, $manutencao) {
    $sql = "INSERT INTO efetivo_mes (legenda, grupo_escalas , usuario, funcao_escala, manutencao) "; //deixe sempre  um espaco no final
    $sql .= "VALUES (?, ?, ?, ?, ?) "; //deixe sempre  um espaco no final
    $binding = array('sisii', $legenda, $grupo, $usuario, $funcao, $manutencao);
    return sql_ins($sql, $GLOBALS['conn'], $binding);
}

function inserirEscEfetivo($escala, $efetivo, $grupo) {
    $sql = "INSERT INTO efetivo_escala (escala, efetivo, grupo_escalas) "; //deixe sempre  um espaco no final
    $sql .= "VALUES (?, ?, ?) "; //deixe sempre  um espaco no final
    $binding = array('iii', $escala, $efetivo, $grupo);
    return sql_ins($sql, $GLOBALS['conn'], $binding);
}

function removerEfetivo($id) {
    $sql = "DELETE FROM efetivo_mes ";
    $sql .= "WHERE id = ? "; //deixe sempre  um espaco no final    
    $binding = array('i', $id);
    return sql_del($sql, $GLOBALS['conn'], $binding);
}

function atualizarefetivoMes($legenda, $funcao, $id, $manutencao) {
    $sql = "UPDATE efetivo_mes "; //deixe sempre  um espaco no final
    $sql .= "SET legenda = ?, funcao_escala = ?, manutencao = ? "; //deixe sempre  um espaco no final
    $sql .= "WHERE id = ? "; //deixe sempre  um espaco no final
    $binding = array('siii', $legenda, $funcao, $manutencao, $id);
    return sql_up($sql, $GLOBALS['conn'], $binding);
}

function buscarEscalaDoEfetivo($id) {
    $sql = "SELECT escala ";
    $sql .= "FROM efetivo_escala ";
    $sql .= "WHERE efetivo = ? ";
    $binding = array('i', $id);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function deletarEfetivoEscala($ea, $id) {
    $sql = "DELETE FROM efetivo_escala "; //deixe sempre  um espaco no final
    $sql .= "WHERE escala = ? AND efetivo = ? "; //deixe sempre  um espaco no final
    $binding = array('ii', $ea, $id);
    return sql_del($sql, $GLOBALS['conn'], $binding);
}

/* * *****************************************************************************
 *                              RESTRIÇÕES
 * **************************************************************************** */

function pegarListaRestricoes($grupo) {
    $sub_query1 = "SELECT legenda FROM turnos WHERE id = rc.turno1";
    $sub_query2 = "SELECT legenda FROM turnos WHERE id = rc.turno2";
    $sql = "SELECT ge.id as grupo, ge.qtd_folgas as folgas, ge.qtd_trocas as "
            . "trocas, rc.id as combinacao,  rc.turno1, rc.turno2, "
            . "($sub_query1) as leg_turno1, ($sub_query2) as leg_turno2 ";
    $sql .= "FROM grupos_escalas as ge ";
    $sql .= "LEFT JOIN restricoes_combinacoes_turno as rc ";
    $sql .= "ON rc.grupo_escalas = ge.id ";
    $sql .= "WHERE ge.id = ? ";
    $binding = array('i', $grupo);
    $retorno = sql_busca($sql, $GLOBALS['conn'], $binding);

    $re = array();
    foreach ($retorno as $r) {
        $re[$r['grupo']]['folgas'] = $r['folgas'];
        $re[$r['grupo']]['trocas'] = $r['trocas'];
        $comb = array();
        $comb['turno1'] = array("id" => $r['turno1'], "leg" => $r['leg_turno1']);
        $comb['turno2'] = array("id" => $r['turno2'], "leg" => $r['leg_turno2']);
        $comb['id'] = $r['combinacao'];
        $re[$r['grupo']]['comb'][] = $comb;
    }
    $retorno = $re[$grupo];

    return $retorno;
}

function alterarConsecutivos($grupo, $folgas, $trocas) {
    $sql = "UPDATE grupos_escalas "; //deixe sempre  um espaco no final
    $sql .= "SET qtd_folgas = ? , qtd_trocas = ? "; //deixe sempre  um espaco no final
    $sql .= "WHERE id = ? "; //deixe sempre  um espaco no final
    $binding = array('iii', $folgas, $trocas, $grupo);
    return sql_up($sql, $GLOBALS['conn'], $binding);
}

function alterarCombinacao($id, $turno1, $turno2) {
    $sql = "UPDATE restricoes_combinacoes_turno "; //deixe sempre  um espaco no final
    $sql .= "SET turno1 = ?, turno2 = ? "; //deixe sempre  um espaco no final
    $sql .= "WHERE id = ? "; //deixe sempre  um espaco no final
    $binding = array('iii', $turno1, $turno2, $id);
    return sql_up($sql, $GLOBALS['conn'], $binding);
}

function alterarRISAER($id, $nome, $legenda, $inicio, $termino, $maior24, $etapa, $dant, $dpost) {
    $sql = "UPDATE risaer "; //deixe sempre  um espaco no final
    $sql .= "SET nome = ?, legenda = ?, inicio = ?, termino = ?, tipo_etapa = ?, intervalo_antes = ?, intervalo_depois = ?, mais_q_24h = ? "; //deixe sempre  um espaco no final
    $sql .= "WHERE id = ? "; //deixe sempre  um espaco no final
    $binding = array('ssssiiiii', $nome, $legenda, $inicio, $termino, $etapa, $dant, $dpost, $maior24, $id);
    return sql_up($sql, $GLOBALS['conn'], $binding);
}

function listaCombinacoesDiferente($grupo, $id) {
    $sql = "SELECT * ";
    $sql .="FROM restricoes_combinacoes_turno as rc ";
    $sql .="WHERE rc.grupo_escalas = ? AND rc.id != ? ";
    $binding = array('ii', $grupo, $id);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function listaCombinacoes($grupo) {
    $sql = "SELECT * ";
    $sql .="FROM restricoes_combinacoes_turno as rc ";
    $sql .="WHERE rc.grupo_escalas = ? ";
    $binding = array('i', $grupo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarCombinacao($id) {
    $sql = "SELECT rc.turno1, rc.turno2 ";
    $sql .="FROM restricoes_combinacoes_turno as rc ";
    $sql .="WHERE rc.id = ? ";
    $binding = array('i', $id);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function inserirCombinação($grupo, $turno1, $turno2) {
    $sql = "INSERT INTO restricoes_combinacoes_turno (turno1, turno2, grupo_escalas) "; //deixe sempre  um espaco no final
    $sql .= "VALUES (?, ?, ?) "; //deixe sempre  um espaco no final
    $binding = array('iii', $turno1, $turno2, $grupo);
    return sql_ins($sql, $GLOBALS['conn'], $binding);
}

function removerCombinacao($id) {
    $sql = "DELETE FROM restricoes_combinacoes_turno ";
    $sql .= "WHERE id = ? "; //deixe sempre  um espaco no final    
    $binding = array('i', $id);
    return sql_del($sql, $GLOBALS['conn'], $binding);
}

function removerRISAER($id) {
    $sql = "DELETE FROM risaer ";
    $sql .= "WHERE id = ? "; //deixe sempre  um espaco no final    
    $binding = array('i', $id);
    return sql_del($sql, $GLOBALS['conn'], $binding);
}

function pegarEscalasDoOperador($grupo, $usuario) {

    $sql = "SELECT ee.escala "; //deixe sempre  um espaco no final
    $sql .= "FROM efetivo_escala as ee "; //deixe sempre  um espaco no final
    $sql .= "JOIN efetivo_mes as em "; //deixe sempre  um espaco no final
    $sql .= "ON ee.efetivo = em.id "; //deixe sempre  um espaco no final
    $sql .= "WHERE em.grupo_escalas = ? AND em.usuario = ? "; //deixe sempre  um espaco no final 
    $binding = array('is', $grupo, $usuario);

    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarlistaRISAER($grupo) {
    $sql = "SELECT * "; //deixe sempre  um espaco no final
    $sql .= "FROM risaer "; //deixe sempre  um espaco no final
    $sql .= "WHERE grupo_escalas = ? "; //deixe sempre  um espaco no final    
    $binding = array('i', $grupo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarRISAERid($id) {
    $sql = "SELECT ri.*, ge.mes, ge.ano "; //deixe sempre  um espaco no final
    $sql .= "FROM risaer as ri "; //deixe sempre  um espaco no final
    $sql .= "JOIN grupos_escalas as ge "; //deixe sempre  um espaco no final
    $sql .= "ON ge.id = ri.grupo_escalas "; //deixe sempre  um espaco no final
    $sql .= "WHERE ri.id = ? "; //deixe sempre  um espaco no final    
    $binding = array('i', $id);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function copiarEscalas($grupo, $tipo) {
    if ($tipo != 4) {
        $sqls = array();
        $bindings = array();

        //criar relacionamento tipo_escala
        $sql = "INSERT INTO tipo_escala "
                . " (grupo_escalas, tipo) "
                . "VALUES (?, ?)";
        $sqls[] = $sql;
        $bindings[] = array('ii', $grupo, $tipo);

        //recuperando o último id inserido
        $sql = "SET @id_tipo_escala = LAST_INSERT_ID() ";
        $sqls[] = $sql;
        $bindings[] = null;

        //pegando o id do tipo_escala anterior
        $sql = "SET @id_tipo_escala_anterior = (SELECT id FROM tipo_escala WHERE grupo_escalas = ? AND tipo = ?) ";
        $sqls[] = $sql;
        $bindings[] = array('ii', $grupo, $tipo - 1);

        //criando uma tabela temporária para poder copiar os valores
        $sql = "CREATE TEMPORARY TABLE servico_temp LIKE servico";
        $binding = NULL;
        $sqls[] = $sql;
        $bindings[] = $binding;

        //copiando os valores de servico para a tabela temporária
        $sql = "INSERT INTO servico_temp (dia, turno, operador, tipo_escala) "; //deixe sempre  um espaco no final
        $sql .= "SELECT svc.dia, svc.turno, svc.operador, @id_tipo_escala FROM servico as svc WHERE svc.tipo_escala = @id_tipo_escala_anterior "; //deixe sempre  um espaco no final
        $binding = null;
        $sqls[] = $sql;
        $bindings[] = $binding;

        //copiando os servicos
        $sql = "INSERT INTO servico (dia, turno, operador, tipo_escala) "; //deixe sempre  um espaco no final
        $sql .= "SELECT s.dia, s.turno, s.operador, s.tipo_escala FROM servico_temp as s "; //deixe sempre  um espaco no final
        $binding = null;
        $sqls[] = $sql;
        $bindings[] = $binding;

        //apagando a tabela temporária
        $sql = "DROP TEMPORARY TABLE servico_temp";
        //$binding = array('i', $grupo);
        $binding = NULL;
        $sqls[] = $sql;
        $bindings[] = $binding;

        //publicacao_escala    
        return sql_transaction($sqls, $GLOBALS['conn'], $bindings);
    } else {
        $sql = "UPDATE tipo_escala "
                . "SET tipo = 4 "
                . "WHERE grupo_escalas = ? AND tipo = ? ";
        $binding = array('ii', $grupo, $tipo - 1);
        return sql_up($sql, $GLOBALS['conn'], $binding);
    }
}

function removerComentario($id) {
    $sql = "DELETE FROM anotacoes ";
    $sql .= "WHERE id = ? "; //deixe sempre  um espaco no final    
    $binding = array('i', $id);
    return sql_del($sql, $GLOBALS['conn'], $binding);
}

function atualizarComentario($id, $texto) {
    $sql = "UPDATE anotacoes "
            . "SET texto = ? "
            . "WHERE id = ? ";
    $binding = array('si', $texto, $id);
    return sql_up($sql, $GLOBALS['conn'], $binding);
}

function inserirComentario($em_id, $tipo, $texto) {
    $sql = "INSERT INTO anotacoes (texto, operador, tipo_escala) "
            . "VALUES (?, ?, ?) ";
    $binding = array('sii', $texto, $em_id, $tipo);
    return sql_ins($sql, $GLOBALS['conn'], $binding);
}

function pegarComentario($em_id) {
    $sql = "SELECT * ";
    $sql .= "FROM anotacoes ";
    $sql .= "WHERE operador = ? ";
    $binding = array('i', $em_id);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function dispensar($svc) {
    $sql = "DELETE FROM servico ";
    $sql .= "WHERE id = ? "; //deixe sempre  um espaco no final    
    $binding = array('i', $svc);
    return sql_del($sql, $GLOBALS['conn'], $binding);
}

function escalar($operador, $tipoEscala, $diaEsc, $turnoEsc) {
    $sql = "INSERT INTO servico (dia, turno, operador, tipo_escala) "; //deixe sempre  um espaco no final
    $sql .= "VALUES (?, ?, ?, ?) "; //deixe sempre  um espaco no final   
    $binding = array('iiii', $diaEsc, $turnoEsc, $operador, $tipoEscala);
    return sql_ins($sql, $GLOBALS['conn'], $binding);
}

function remanejar($idSvc, $diaEsc, $turnoEsc) {
    $sql = "UPDATE servico "; //deixe sempre  um espaco no final (nome, legenda, inicio, termino, etapa_full, grupo_escalas) 
    $sql .= "SET dia = ?, turno = ? "; //deixe sempre  um espaco no final
    $sql .= "WHERE id = ? "; //deixe sempre  um espaco no final
    $binding = array('iii', $diaEsc, $turnoEsc, $idSvc);
    return sql_up($sql, $GLOBALS['conn'], $binding);
}

function pegarServicosUsuarios($usuarios, $mes_inicio, $ano_inicio, $mes_fim, $ano_fim, $tipoEscala) {
    $sql4 = "SELECT te.tipo "
            . "FROM tipo_escala as te "
            . "WHERE id = ? ";

    $sql3 = "SELECT ge.id ";
    $sql3 .= "FROM grupos_escalas as ge ";
    $sql3 .= "WHERE MAKEDATE(ge.ano, ge.mes) BETWEEN MAKEDATE(?, ?) AND MAKEDATE(?, ?) ";

    $sql1 = "SELECT ee1.id ";
    $sql1 .= "FROM efetivo_escala as ee1 ";
    $sql1 .= "JOIN efetivo_mes as em1 ";
    $sql1 .= "ON em1.id = ee1.efetivo AND em1.usuario IN (";
    $var = array();
    $binding[] = "";
    foreach ($usuarios as $id) {
        $var[] = "?";
        $binding[0] .= "s";
        $binding[] = $id;
    }
    $sql1 .= implode(",", $var) . ") ";
    $sql1 .= "WHERE ee1.grupo_escalas IN ($sql3) ";

    $sql = "SELECT "
            . "em.usuario, "
            . "em.id as em_id, "
            . "em.legenda as usu_leg, "
            . "em.manutencao, "
            . "ee.id as ee_id, "
            . "e.id as esc_id, "
            . "e.legenda as esc_leg, "
            . "s.id as svc_id, "
            . "s.dia, "
            . "t.id as turno_id, "
            . "t.legenda as turnos_leg, "
            . "t.etapa_full as etapa, "
            . "t.inicio, "
            . "t.termino, "
            . "t.periodo, "
            . "t.pos_noturno, "
            . "te1.tipo as tipo_escala, "
            . "ge1.orgao, "
            . "ge1.mes, "
            . "ge1.ano ";
    $sql .= "FROM servico as s ";

    $sql .= "JOIN tipo_escala as te1 ";
    $sql .= "ON s.tipo_escala = te1.id ";

    $sql .= "JOIN efetivo_escala as ee ";
    $sql .= "ON s.operador = ee.id ";

    $sql .= "JOIN escalas as e ";
    $sql .= "ON ee.escala = e.id ";

    $sql .= "JOIN efetivo_mes as em ";
    $sql .= "ON ee.efetivo = em.id ";

    $sql .= "JOIN turnos as t ";
    $sql .= "ON t.id = s.turno ";

    $sql .= "JOIN grupos_escalas as ge1 ";
    $sql .= "ON te1.grupo_escalas = ge1.id ";

    $sql .= "WHERE s.operador IN ($sql1) AND te1.tipo IN ($sql4) ";

    $binding[0] .= "iiiii";
    $binding[] = $ano_inicio;
    $binding[] = $mes_inicio;
    $binding[] = $ano_fim;
    $binding[] = $mes_fim;
    $binding[] = $tipoEscala;

    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarServicosEfetivoMes($efetivo_mes, $mes_inicio, $ano_inicio, $mes_fim, $ano_fim, $tipoEscala) {
    $sql4 = "SELECT te.tipo "
            . "FROM tipo_escala as te "
            . "WHERE id = ? ";

    $sql3 = "SELECT ge.id ";
    $sql3 .= "FROM grupos_escalas as ge ";
    $sql3 .= "WHERE MAKEDATE(ge.ano, ge.mes) BETWEEN MAKEDATE(?, ?) AND MAKEDATE(?, ?) ";

    $sql2 = "SELECT em.usuario ";
    $sql2 .= "FROM efetivo_mes as em ";
    $sql2 .= "WHERE em.id = ? ";

    $sql1 = "SELECT ee1.id ";
    $sql1 .= "FROM efetivo_escala as ee1 ";
    $sql1 .= "JOIN efetivo_mes as em1 ";
    $sql1 .= "ON em1.id = ee1.efetivo AND em1.usuario = ($sql2) ";
    $sql1 .= "WHERE ee1.grupo_escalas IN ($sql3) ";

    $sql = "SELECT s.dia, tu.inicio, tu.termino, tu.legenda, ge1.mes, ge1.ano ";
    $sql .= "FROM servico as s ";
    $sql .= "JOIN tipo_escala as te1 ";
    $sql .= "ON s.tipo_escala = te1.id ";
    $sql .= "JOIN turnos as tu ";
    $sql .= "ON tu.id = s.turno ";
    $sql .= "JOIN grupos_escalas as ge1 ";
    $sql .= "ON te1.grupo_escalas = ge1.id ";
    $sql .= "WHERE s.operador IN ($sql1) AND te1.tipo IN ($sql4) ";

    $binding = array('iiiiii', $efetivo_mes, $ano_inicio, $mes_inicio, $ano_fim, $mes_fim, $tipoEscala);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarServicosOperadorMesAno($opr, $mes, $ano, $tipoEscala) {
    $sql4 = "SELECT te.tipo "
            . "FROM tipo_escala as te "
            . "WHERE id = ? ";

    $sql3 = "SELECT id ";
    $sql3 .= "FROM grupos_escalas ";
    $sql3 .= "WHERE mes = ? AND ano = ? ";

    $sql2 = "SELECT em.usuario ";
    $sql2 .= "FROM efetivo_mes as em ";
    $sql2 .= "JOIN efetivo_escala as ee ";
    $sql2 .= "ON ee.id = ? ";
    $sql2 .= "WHERE ee.efetivo = em.id ";

    $sql1 = "SELECT ee1.id ";
    $sql1 .= "FROM efetivo_escala as ee1 ";
    $sql1 .= "JOIN efetivo_mes as em1 ";
    $sql1 .= "ON em1.id = ee1.efetivo AND em1.usuario = ($sql2) ";
    $sql1 .= "WHERE ee1.grupo_escalas IN ($sql3) ";

    $sql = "SELECT s.dia ";
    $sql .= "FROM servico as s ";
    $sql .= "JOIN tipo_escala as te1 ";
    $sql .= "ON s.tipo_escala = te1.id ";
    $sql .= "WHERE s.operador IN ($sql1) AND te1.tipo IN ($sql4) ";

    $binding = array('iiii', $opr, $mes, $ano, $tipoEscala);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarTipoTurno($id) {
    $sql = "SELECT periodo ";
    $sql .= "FROM turnos ";
    $sql .= "WHERE id = ? ";
    $binding = array('i', $id);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarTurnoPeloServico($servico) {
    $sql = "SELECT t.legenda, t.id ";
    $sql .= "FROM servico as s ";
    $sql .= "JOIN turnos as t ";
    $sql .= "ON t.id = s.turno ";
    $sql .= "WHERE s.id = ?";
    $binding = array('i', $servico);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarEquipes($grupo) {
    $sql = "SELECT ee.escala, ee.id as operador, SUBSTRING(em.legenda, 1, 1) as equipe, em.usuario ";
    $sql .= "FROM efetivo_escala as ee ";
    $sql .= "JOIN efetivo_mes as em ";
    $sql .= "ON em.id = ee.efetivo ";
    $sql .= "WHERE ee.grupo_escalas = ? ";
    $sql .= "ORDER BY equipe ";

    $binding = array('i', $grupo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarEquipesPorEscala($grupo, $escala) {
    $sql = "SELECT DISTINCT SUBSTRING(em.legenda, 1, 1) as equipe ";
    $sql .= "FROM efetivo_escala as ee ";
    $sql .= "JOIN efetivo_mes as em ";
    $sql .= "ON em.id = ee.efetivo ";
    $sql .= "WHERE ee.grupo_escalas = ? AND ee.escala = ? ";
    $sql .= "ORDER BY equipe ";

    $binding = array('ii', $grupo, $escala);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function inserirSequencia($grupo, $turnos, $equipes, $qtdDiasMes) {
    $sqls = array();
    $bindings = array();

    //inserindo a escala
    $sql = "INSERT INTO tipo_escala (grupo_escalas, tipo, trocas_liberadas) "; //deixe sempre  um espaco no final
    $sql .= "VALUES (?, 1, 0) "; //deixe sempre  um espaco no final
    $sqls[] = $sql;
    $bindings[] = array('i', $grupo);

    //recuperando o último id inserido
    $sql = "SELECT LAST_INSERT_ID() INTO @id_tipo_escala ";
    $sqls[] = $sql;
    $bindings[] = null;

    foreach ($equipes as $j => $eq) {
        if (array_key_exists('geral', $turnos)) {
            $turnosAtual = $turnos['geral'];
        } else {
            $turnosAtual = $turnos[$j];
        }
        $equipeAnterior = "";
        $increm = 0;
        foreach ($eq as $dados) {
            $equipeAtual = $dados['equipe'];
            if ($equipeAtual != $equipeAnterior && $equipeAnterior != "") {
                if ($increm == 0) {
                    $increm = sizeof($turnosAtual) - 1;
                } else {
                    $increm = $increm - 1;
                }
                $equipeAnterior = $equipeAtual;
            } else if ($equipeAnterior == "") {
                $equipeAnterior = $equipeAtual;
            }
            $resposta = pegarDadosGrupo($grupo);
            $orgaoID = $resposta[0]['orgao'];
            $mes = $resposta[0]['mes'];
            $ano = $resposta[0]['ano'];
            $verHabilitacao = checaValidadeHabilitacao($dados['usuario'], $orgaoID, $mes, $ano);
            for ($i = 1; $i <= $qtdDiasMes; $i++) {
                $turnoDia = $turnosAtual[(($i - 1 + $increm) % (sizeof($turnosAtual)))];
                if ($turnoDia != "folga") {
                    if (!in_array($i, $verHabilitacao)) {
                        $verAfastamento = verificarAfastamentoRemanejEscalacao($dados['operador'], $grupo, $i, "");
                        if (!$verAfastamento['afastado']) {
                            $turnoComb = explode("-", $turnoDia);
                            if (sizeof($turnoComb) == 1) {
                                if (in_array(array('turno' => $turnoComb[0]), $dados['turnos'])) {
                                    //adiciona os Serviços
                                    $sql = "INSERT INTO servico "
                                            . " (dia, turno, operador, tipo_escala) "
                                            . "VALUES (?, ?, ?, @id_tipo_escala)";
                                    $sqls[] = $sql;
                                    $bindings[] = array('iii', $i, $turnoDia, $dados['operador']);
                                }
                            } else {
                                if (in_array(array('turno' => $turnoComb[0]), $dados['turnos'])) {
                                    //adiciona o 1º Serviço
                                    $sql = "INSERT INTO servico "
                                            . " (dia, turno, operador, tipo_escala) "
                                            . "VALUES (?, ?, ?, @id_tipo_escala)";
                                    $sqls[] = $sql;
                                    $bindings[] = array('iii', $i, $turnoComb[0], $dados['operador']);
                                }
                                if (in_array(array('turno' => $turnoComb[1]), $dados['turnos'])) {
                                    //adiciona o 2º serviço
                                    $sql = "INSERT INTO servico "
                                            . " (dia, turno, operador, tipo_escala) "
                                            . "VALUES (?, ?, ?, @id_tipo_escala)";
                                    $sqls[] = $sql;
                                    $bindings[] = array('iii', $i, $turnoComb[1], $dados['operador']);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return sql_transaction($sqls, $GLOBALS['conn'], $bindings);
}

function inserirEscalaBranco($grupo) {
    $sql = "INSERT INTO tipo_escala (grupo_escalas, tipo, trocas_liberadas) "; //deixe sempre  um espaco no final
    $sql .= "VALUES (?, 1, 0) "; //deixe sempre  um espaco no final
    $binding = array('i', $grupo);
    return sql_ins($sql, $GLOBALS['conn'], $binding);
}

function listaTurnosEscala($id) {
    $sql = "SELECT te.id as te_id, te.escala, e.legenda as escala_legenda, t.id, t.legenda, t.nome, te.ref_soma "; //deixe sempre  um espaco no final
    $sql .= "FROM turno_escala AS te "; //deixe sempre  um espaco no final
    $sql .= "JOIN turnos AS t "; //deixe sempre  um espaco no final
    $sql .= "ON t.id = te.turno "; //deixe sempre  um espaco no final
    $sql .= "JOIN escalas AS e "; //deixe sempre  um espaco no final
    $sql .= "ON e.id = te.escala "; //deixe sempre  um espaco no final
    $sql .= "WHERE escala = ? "; //deixe sempre  um espaco no final    
    $binding = array('i', $id);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function turnosSomaQtdGeral($turnoId) {
    $sql = "SELECT tsqg.id as ts_id, t.id, t.legenda "; //deixe sempre  um espaco no final
    $sql .= "FROM turnos_soma_qtd_geral AS tsqg "; //deixe sempre  um espaco no final
    $sql .= "JOIN turnos AS t "; //deixe sempre  um espaco no final    
    $sql .= "ON tsqg.turno_secundario = t.id "; //deixe sempre  um espaco no final    
    $sql .= "WHERE tsqg.turno_principal = ? "; //deixe sempre  um espaco no final    
    $binding = array('i', $turnoId);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function turnosSomaQtd($turnoEscId) {
    $sql = "SELECT tsq.id as ts_id, t.id, t.legenda "; //deixe sempre  um espaco no final
    $sql .= "FROM turnos_soma_qtd AS tsq "; //deixe sempre  um espaco no final
    $sql .= "JOIN turno_escala AS te "; //deixe sempre  um espaco no final    
    $sql .= "ON tsq.turno_escala_secundario = te.id "; //deixe sempre  um espaco no final    
    $sql .= "JOIN turnos AS t "; //deixe sempre  um espaco no final    
    $sql .= "ON te.turno = t.id "; //deixe sempre  um espaco no final    
    $sql .= "WHERE tsq.turno_escala_principal = ? "; //deixe sempre  um espaco no final    
    $binding = array('i', $turnoEscId);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function alterarStatusTroca($grupo, $tipo, $valor) {
    $sql = "UPDATE tipo_escala "; //deixe sempre  um espaco no final (nome, legenda, inicio, termino, etapa_full, grupo_escalas) 
    $sql .= "SET trocas_liberadas = ? "; //deixe sempre  um espaco no final
    $sql .= "WHERE grupo_escalas = ? AND tipo = ? "; //deixe sempre  um espaco no final
    $binding = array('iii', $valor, $grupo, $tipo);
    return sql_up($sql, $GLOBALS['conn'], $binding);
}

function alterarStatusPublicada($id, $valor, $tipo, $publicada) {
    if ($publicada != -1) {
        $sql = "UPDATE publicacao_escala "; //deixe sempre  um espaco no final (nome, legenda, inicio, termino, etapa_full, grupo_escalas) 
        $sql .= "SET publicada = ? "; //deixe sempre  um espaco no final
        $sql .= "WHERE escala = ? AND tipo = ? "; //deixe sempre  um espaco no final
        $binding = array('iii', $valor, $id, $tipo);
        return sql_up($sql, $GLOBALS['conn'], $binding);
    } else {
        $sql = "INSERT INTO publicacao_escala (escala, tipo, publicada) "; //deixe sempre  um espaco no final
        $sql .= "VALUES (?, ?, ?) "; //deixe sempre  um espaco no final   
        $binding = array('iii', $id, $tipo, $valor);
        return sql_ins($sql, $GLOBALS['conn'], $binding);
    }
}

function qtdOprTurnoDia($turnos, $escalas, $tipo, $grupo) {
    unset($binding);
    unset($juntar1);
    unset($juntar2);
    unset($juntar3);

    $sql = "SELECT s.dia "//s.dia, COUNT(s.id) AS qtd "
            . "FROM servico AS s "
            . "JOIN efetivo_escala AS ef "
            . "ON ef.id = s.operador "
            . "JOIN tipo_escala AS te "
            . "ON te.id = s.tipo_escala "
            . "WHERE (";
//
    $totalDeVariaveis = sizeof($turnos) + sizeof($escalas) + 2;
    $string = "";
    for ($i = 0; $i < $totalDeVariaveis; $i++) {
        $string .= "i";
    }
    $binding[] = $string;
    foreach ($turnos as $t) {
        $juntar1[] = "s.turno = ?";
        $binding[] = $t;
    }

    $sql .= implode(" OR ", $juntar1);
    $sql .= ") AND (";

    foreach ($escalas as $e) {
        $juntar3[] = "ef.escala = ?";
        $binding[] = $e;
    }
    $sql .= implode(" OR ", $juntar3);
    $sql .=") AND te.tipo = ? AND te.grupo_escalas = ?";

    $binding[] = $tipo;
    $binding[] = $grupo;

    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function atualizarEscSomaGeral($id, $mudar) {
    $sql = "UPDATE escalas "; //deixe sempre  um espaco no final (nome, legenda, inicio, termino, etapa_full, grupo_escalas) 
    $sql .= "SET soma_geral = ? "; //deixe sempre  um espaco no final
    $sql .= "WHERE id = ? "; //deixe sempre  um espaco no final
    $binding = array('ii', $mudar, $id);
    return sql_up($sql, $GLOBALS['conn'], $binding);
}

function removerTurnoSoma($geral, $id) {
    $geral = $geral ? "_geral" : "";
    $sql = "DELETE FROM turnos_soma_qtd$geral ";
    $sql .= "WHERE id = ? "; //deixe sempre  um espaco no final    
    $binding = array('i', $id);
    return sql_del($sql, $GLOBALS['conn'], $binding);
}

function inserirTurnoSoma($geral, $turnoPrinc, $turnoSec) {
    $escala = $geral ? "" : "escala_";
    $geral = $geral ? "_geral" : "";
    $sql = "INSERT INTO turnos_soma_qtd$geral (turno_$escala" . "principal, turno_$escala" . "secundario) "; //deixe sempre  um espaco no final
    $sql .= "VALUES (?, ?) "; //deixe sempre  um espaco no final
    $binding = array('ii', $turnoPrinc, $turnoSec);
    return sql_ins($sql, $GLOBALS['conn'], $binding);
}

function listaOperadoresEscala($id) {
    $sql = "SELECT  em.id AS em_id, em.legenda, em.usuario ";
    $sql .= "FROM efetivo_escala AS ee ";
    $sql .= "JOIN efetivo_mes AS em ";
    $sql .= "ON ee.efetivo = em.id ";
    $sql .= "WHERE ee.escala = ? ";
    $sql .= "ORDER BY em.legenda ";
    $binding = array('i', $id);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function listaRisaerOperadoresOrgao($grupo, $mes, $ano, $tipo) {
    $sql = "SELECT "
            . "em.id as em_id, "
            . "sr.dia, "
            . "sr.id as risaer_id, "
            . "r.legenda "
            . ""
            . "FROM efetivo_mes AS em "
            . ""
            . "JOIN grupos_escalas AS ge "
            . "ON ge.id = em.grupo_escalas "
            . ""
            . "LEFT JOIN tipo_escala as te "
            . "ON ge.id = te.grupo_escalas "
            . ""
            . "LEFT JOIN servico_risaer as sr "
            . "ON em.id = sr.efetivo_mes AND sr.tipo_escala = te.id "
            . "JOIN risaer as r "
            . "ON sr.servico = r.id "
            . ""
            . " WHERE em.grupo_escalas = ? AND ge.mes = ? AND ge.ano = ? AND "
            . "te.tipo = (CASE WHEN ge.orgao = (SELECT ge2.orgao FROM grupos_escalas AS ge2 WHERE ge2.id = ?) "
            . "THEN ? "
            . "ELSE (SELECT MAX(te2.tipo) FROM tipo_escala as te2 WHERE te2.grupo_escalas = ge.id)"
            . " END)";
    $binding = array('iiiii', $grupo, $mes, $ano, $grupo, $tipo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function listaOperadoresOrgao($grupo, $mes, $ano, $tipo) {
    $sql = "SELECT "
            . "em.id as em_id, "
            . "em2.legenda, "
            . "em2.manutencao, "
            . "em.usuario, "
            . "ee.escala as escala_id, "
            . "s.id as svc_id, "
            . "s.dia, "
            . "ee.id as operador,"
            . "te.id as tipo_escala, "
            . "t.legenda as turno, "
            . "t.periodo as turno_periodo, "
            . "t.id as turno_id, "
            . "t.pos_noturno as turno_posnoturno "
            . ""
            . "FROM efetivo_mes AS em "
            . "JOIN efetivo_mes AS em2 "
            . "ON em2.usuario = em.usuario "
            . "JOIN grupos_escalas AS ge "
            . "ON ge.id = em2.grupo_escalas "
            . ""
            . "LEFT JOIN efetivo_escala as ee "
            . "ON ee.efetivo = em2.id "
            . ""
            . "LEFT JOIN tipo_escala as te "
            . "ON ge.id = te.grupo_escalas "
            . ""
            . "LEFT JOIN servico as s "
            . "ON s.operador = ee.id AND s.tipo_escala = te.id "
            . "LEFT JOIN turnos as t "
            . "ON s.turno = t.id "
            . ""
            . "WHERE em.grupo_escalas = ? AND ge.mes = ? AND ge.ano = ? AND "
            . "te.tipo = (CASE WHEN ge.orgao = (SELECT ge2.orgao FROM grupos_escalas AS ge2 WHERE ge2.id = ?) "
            . "THEN ? "
            . "ELSE (SELECT MAX(te2.tipo) FROM tipo_escala as te2 WHERE te2.grupo_escalas = ge.id)"
            . " END)";

    $binding = array('iiiii', $grupo, $mes, $ano, $grupo, $tipo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarNomeGuerraOperador($cadastroID) {
    $sb1 = "(CASE WHEN"
            . " posto = 'CV' "
            . "THEN"
            . " NOME "
            . "ELSE"
            . " nome_guerra "
            . "END)";

    $sql = "SELECT posto AS pg, $sb1 AS ng ";
    $sql .= "FROM cadastros ";
    $sql .= "WHERE cadastroID = ? ";
    $binding = array('s', $cadastroID);
    return sql_busca($sql, $GLOBALS['connL'], $binding);
}

function pegarServicoOperadorDiaNaEscala($operador, $tipo, $dia) {
    $sql = "SELECT s.turno ";
    $sql .= "FROM servico AS s ";
    $sql .= "WHERE s.dia = ? AND s.operador = ? AND s.tipo_escala = ? AND s.turno IS NOT NULL ";
    $binding = array('iii', $dia, $operador, $tipo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarServicoOperadorDiaEmOutraEscala($operador, $escala, $grupo, $tipo, $dia) {
    $sql = "SELECT t.legenda AS turno1 ";
    $sql .= "FROM servico AS s ";
    $sql .= "JOIN efetivo_escala AS ee ";
    $sql .= "ON ee.id = s.operador ";
    $sql .= "JOIN tipo_escala AS te ";
    $sql .= "ON te.id = s.tipo_escala ";
    $sql .= "LEFT JOIN turnos AS t ";
    $sql .= "ON t.id = s.turno ";
    $sql .= "WHERE s.dia = ?  AND ee.escala != ? AND ee.efetivo = ? AND te.grupo_escalas = ? AND te.tipo = ? ";
    $binding = array('iiiii', $dia, $escala, $operador, $grupo, $tipo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarQtdServicoOperadorMes($operador, $mes, $ano, $tipo) {
    $sql = "SELECT SUM(CASE WHEN s.turno IS NULL THEN 0 ELSE 1 END) as qtd "
            . "FROM efetivo_mes AS em "
            . "JOIN efetivo_mes AS em2 "
            . "ON em2.usuario = em.usuario "
            . "JOIN grupos_escalas AS ge "
            . "ON ge.id = em2.grupo_escalas "
            . "LEFT JOIN efetivo_escala as ee "
            . "ON ee.efetivo = em2.id "
            . "LEFT JOIN servico as s "
            . "ON s.operador = ee.id "
            . "JOIN tipo_escala as te "
            . "ON s.tipo_escala = te.id "
            . "LEFT JOIN turnos as t "
            . "ON s.turno = t.id "
            . "WHERE em.id = ? AND ge.mes = ? AND ge.ano = ? AND te.tipo = ? ";
    $binding = array('iiii', $operador, $mes, $ano, $tipo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function qtdTurnosOprMes($turnos, $operador, $tipo, $grupo) {
    $sql = "SELECT COUNT(*) AS qtd ";
    $sql .= "FROM servico AS s ";
    $sql .= "JOIN efetivo_escala AS ee ";
    $sql .= "ON ee.id = s.operador ";
    $sql .= "JOIN efetivo_mes AS em ";
    $sql .= "ON em.id = ee.efetivo ";
    $sql .= "JOIN tipo_escala AS te ";
    $sql .= "ON te.id = s.tipo_escala ";
    $sql .= "WHERE (";

    $totalDeVariaveis = sizeof($turnos) + 3;
    $string = "";
    for ($i = 0; $i < $totalDeVariaveis; $i++) {
        $string .= "i";
    }
    $binding[] = $string;
    foreach ($turnos as $t) {
        $juntar1[] = "turno = ?";
        $binding[] = $t;
    }
    $sql .= implode(" OR ", $juntar1);

    $sql .= ")AND te.tipo = ? AND te.grupo_escalas = ? AND em.id = ?";

    $binding[] = $tipo;
    $binding[] = $grupo;
    $binding[] = $operador;
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

//function informacoesEscala($id, $tipo) {
//    $sql = "SELECT es.id, es.nome, es.legenda, es.grupo_escalas, es.ch_maxima, es.ch_minima, es.qtd_svc, es.soma_geral, pe.publicada ";
//    $sql .= "FROM escalas es ";
//    $sql .= "LEFT JOIN publicacao_escala as pe ";
//    $sql .= "ON pe.escala = es.id AND pe.tipo = ? ";
//    $sql .= "WHERE es.id = ? ";
//    $binding = array('ii', $tipo, $id);
//    return sql_busca($sql, $GLOBALS['conn'], $binding);
//}

function pegarValoresSvcs($ids) {
    $juntar = array();
    $stringBinding = "";
    $binding = array($stringBinding);
    $sql = "SELECT s.id, t.legenda as turno "
            . "FROM servico as s "
            . "LEFT JOIN turnos as t "
            . "ON t.id = s.turno "
            . "WHERE ";
    foreach ($ids as $id) {
        $juntar[] = "s.id = ?";
        $stringBinding .= "i";
        $binding[] = $id;
    }
    $binding[0] = $stringBinding;
    $sql .= implode(" OR ", $juntar);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function atualizarDiaSvc($remover, $addinfos) {
    $sqls = array();
    $bindings = array();

    foreach ($remover as $sId) {
        $sql = "DELETE FROM servico "
                . "WHERE id = ? "; //deixe sempre  um espaco no final    
        $sqls[] = $sql;
        $bindings[] = array('i', $sId);
    }

    foreach ($addinfos as $inf) {
        //adiciona o grupo atual para uma variavel local
        $sql = "INSERT INTO servico "
                . " (dia, turno, operador, tipo_escala) "
                . "VALUES (?, ?, ?, ?)";
        $sqls[] = $sql;
        $bindings[] = array('iiii', $inf['dia'], $inf['turno'], $inf['operador'], $inf['tipo_escala']);
    }
    return sizeof($sqls) > 0 ? sql_transaction($sqls, $GLOBALS['conn'], $bindings) : "";
}

function checarPASSID($passID) {
    $sql = "SELECT c.cadastroID, c.CPF, c.posto, c.nome_guerra as ng, c.ORGAO_matricula, c.email_inst, c.email "
            . "FROM cadastros as c "
            . "JOIN root_usuarios as ru "
            . "ON ru.cpf = c.CPF "
            . "WHERE ru.passID = ? ";
    $bindings = array('s', $passID);
    return sql_busca($sql, $GLOBALS['connL'], $bindings);
}

function aasort(&$array, $args) {
    $sort_rule = "";
    foreach ($args as $arg) {
        $order_field = substr($arg, 1, strlen($arg));
        foreach ($array as $array_row) {
            $sort_array[$order_field][] = $array_row[$order_field];
        }
        $sort_rule .= '$sort_array["' . $order_field . '"], ' . ($arg[0] == "+" ? SORT_ASC : SORT_DESC) . ',';
    }
    eval("array_multisort($sort_rule" . ' $array);');
}

function pegarGrupo($mes, $ano, $orgao, $unidade) {//ok
    $sql = "SELECT id ";
    $sql .= "FROM grupos_escalas ";
    $sql .= "WHERE mes = ? AND ano = ? AND orgao = ? AND unidade = ? ";
    $binding = array('iiis', $mes, $ano, $orgao, $unidade);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarTiposEscala($grupo) {
    $sql = "SELECT * ";
    $sql .= "FROM tipo_escala ";
    $sql .= "WHERE grupo_escalas = ? ";
    $binding = array('i', $grupo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function verificaUsuarioHabilitacaoValida($usuario, $orgID, $ano, $mes, $unidade) {
    $sub1 = "(SELECT MONTH(h2.dt_validade) FROM habilitacoes h2 WHERE h2.habilitacaoID = h.habilitacaoID)";
    $sub2 = "(SELECT YEAR(h2.dt_validade) FROM habilitacoes h2 WHERE h2.habilitacaoID = h.habilitacaoID)";

    $sub4 = "(CASE WHEN"
            . " $sub2 > ? "
            . "THEN"
            . "  TRUE "
            . "ELSE"
            . " (CASE WHEN"
            . "   $sub2 = ? "
            . " THEN"
            . "    (CASE WHEN"
            . "      $sub1 >= ? "
            . "    THEN"
            . "     TRUE "
            . "    ELSE"
            . "     FALSE"
            . "    END) "
            . " ELSE"
            . "  FALSE"
            . " END)"
            . "END)";

    $sql = "SELECT MAX(h.dt_validade) AS validade "
            . "FROM habilitacoes h "
            . "WHERE h.cadastroID = ? AND h.setorID = ? AND h.unidadeID = ? AND h.deletedat IS NULL AND $sub4 ";

    $binding = array('siiii', $usuario, $orgID, $unidade, $ano, $ano, $mes);
    return sql_busca($sql, $GLOBALS['connL'], $binding);
}

//function pegarAfastamentosNoMes($usuario, $mes, $ano) {
//    $s1 = "CASE WHEN"
//            . " MONTH(dt_i) < 10 "
//            . "THEN"
//            . " CONCAT('0',MONTH(dt_i)) "
//            . "ELSE"
//            . " MONTH(dt_i) "
//            . "END";
//    $inicio = "CAST(CONCAT(YEAR(dt_i),$s1) AS UNSIGNED)";
//
//    $s2 = "CASE WHEN"
//            . " MONTH(dt_f) < 10 "
//            . "THEN"
//            . " CONCAT('0',MONTH(dt_f)) "
//            . "ELSE"
//            . " MONTH(dt_f) "
//            . "END";
//    $termino = "CAST(CONCAT(YEAR(dt_f),$s2) AS UNSIGNED)";
//
//    $mes = $mes < 10 ? "0" . $mes : $mes;
//    $anoMes = $ano . $mes;
//
//    $sql = "SELECT  cadastroID as usuario, tipo, REPLACE(dt_i,'-','') as inicio, REPLACE(dt_f,'-','') as termino "
//            . "FROM afastamentos "
//            . "WHERE cadastroID = ? AND $inicio <= ? AND $termino >= ? ";
//    $binding = array('sii', $usuario, $anoMes, $anoMes);
//    return sql_busca($sql, $GLOBALS['connL'], $binding);
//}

function removerInformacoesAfastamentos($ids) {

    foreach ($ids as $id) {
        $sql = "DELETE FROM infos "
                . "WHERE id = ? "; //deixe sempre  um espaco no final    
        $binding = array('i', $id);
        $sqls[] = $sql;
        $bindings[] = $binding;
    }

    return sql_transaction($sqls, $GLOBALS['conn'], $bindings);
}

function pegarUsuarioPeloEfetivoEscala($eeID) {
    $sql = "SELECT em.usuario "
            . "FROM efetivo_escala as ee "
            . "JOIN efetivo_mes as em "
            . "ON em.id = ee.efetivo "
            . "WHERE ee.id = ?";
    $binding = array('i', $eeID);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarUsuarioPeloEfetivoMes($emID) {
    $sql = "SELECT em.usuario "
            . "FROM efetivo_mes as em "
            . "WHERE em.id = ?";
    $binding = array('i', $emID);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarEfetivoEscalaID($efetivoMesID, $escalaID) {
    $sql = "SELECT ee.id "
            . "FROM efetivo_escala as ee "
            . "WHERE ee.efetivo = ? AND ee.escala = ? ";
    $binding = array('ii', $efetivoMesID, $escalaID);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function checarSeExisteServicoNoAfastamento($usuario, $inicio, $termino, $anoMesIni, $anoMesTer) {
    $s1 = "CASE WHEN"
            . " ge.mes < 10 "
            . "THEN"
            . " CONCAT('0',ge.mes) "
            . "ELSE"
            . " ge.mes "
            . "END";
    $anoMes = "(CAST(CONCAT(ge.ano,$s1) AS UNSIGNED))";

    $maiorTipo = "(SELECT MAX(te2.tipo) "
            . "FROM tipo_escala as te2 "
            . "WHERE te2.grupo_escalas = ge.id)";

    $s2 = "CASE WHEN"
            . " s.dia < 10 "
            . "THEN"
            . " CONCAT('0',s.dia) "
            . "ELSE"
            . " s.dia "
            . "END";
    $anoMes = "(CAST(CONCAT(ge.ano,$s1) AS UNSIGNED))";
    $anoMesDia = "(CAST(CONCAT($anoMes,$s2) AS UNSIGNED))";

    $sql = "SELECT COUNT(*) as qtd "
            . "FROM efetivo_mes as em "
            . "JOIN grupos_escalas as ge "
            . "ON em.grupo_escalas = ge.id "
            . "JOIN efetivo_escala as ee "
            . "ON em.id = ee.efetivo "
            . "JOIN servico as s "
            . "ON s.operador = ee.id "
            . "JOIN tipo_escala as te "
            . "ON te.id = s.tipo_escala "
            . ""
            . "WHERE"
            . " em.usuario = ? "
            . "AND"
            . " $anoMes >= ? "
            . "AND"
            . " $anoMes <= ? "
            . "AND"
            . " s.turno IS NOT NULL "
            . "AND"
            . " $anoMesDia >= ? "
            . "AND"
            . " $anoMesDia <= ? "
            . "AND"
            . " te.tipo = $maiorTipo "
            . "AND"
            . " te.tipo != 4";

    $binding = array('siiii', $usuario, $anoMesIni, $anoMesTer, $inicio, $termino);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function removerOsSerivicosOperacionaisDevidoAfastamento($usuario, $inicio, $termino, $anoMesIni, $anoMesTer) {
    $s1 = "CASE WHEN"
            . " ge.mes < 10 "
            . "THEN"
            . " CONCAT('0',ge.mes) "
            . "ELSE"
            . " ge.mes "
            . "END";
    $anoMes = "(CAST(CONCAT(ge.ano,$s1) AS UNSIGNED))";

    $maiorTipo = "(SELECT MAX(te2.tipo) "
            . "FROM tipo_escala as te2 "
            . "WHERE te2.grupo_escalas = ge.id)";

    $s2 = "CASE WHEN"
            . " s.dia < 10 "
            . "THEN"
            . " CONCAT('0',s.dia) "
            . "ELSE"
            . " s.dia "
            . "END";
    $anoMes = "(CAST(CONCAT(ge.ano,$s1) AS UNSIGNED))";
    $anoMesDia = "(CAST(CONCAT($anoMes,$s2) AS UNSIGNED))";

    $s3 = "SELECT"
            . " s.dia, "
            . "t.legenda, "
            . "em.id as em_idm, "
            . "te.id as tipo_escala, "
            . "ee.id as ed_id "
            . ""
            . "FROM servico as s "
            . ""
            . "JOIN efetivo_escala as ee "
            . "ON ee.id = s.operador "
            . ""
            . "JOIN efetivo_mes as em "
            . "ON ee.efetivo = em.id "
            . ""
            . "JOIN grupos_escalas as ge "
            . "ON ge.id = em.grupo_escalas "
            . ""
            . "JOIN tipo_escala as te "
            . "ON te.id = s.tipo_escala "
            . ""
            . "JOIN turnos as t "
            . "ON s.turno = t.id "
            . ""
            . "WHERE"
            . " em.usuario = ? "
            . "AND"
            . " $anoMes >= ? "
            . "AND"
            . " $anoMes <= ? "
            . "AND"
            . " s.turno IS NOT NULL "
            . "AND"
            . " $anoMesDia >= ? "
            . "AND"
            . " $anoMesDia <= ? "
            . "AND"
            . " te.tipo = $maiorTipo "
            . "AND"
            . " te.tipo != 4";

    $sql = "INSERT INTO "
            . "infos (dia, texto, efetivo_mes_id, tipo_escala, efetivo_escala_id) "
            . "$s3";
    $sqls[] = $sql;
    $bindings[] = array('siiii', $usuario, $anoMesIni, $anoMesTer, $inicio, $termino);

    $sql = "DELETE s "
            . "FROM servico as s "
            . ""
            . "JOIN efetivo_escala as ee "
            . "ON ee.id = s.operador "
            . ""
            . "JOIN efetivo_mes as em "
            . "ON ee.efetivo = em.id "
            . ""
            . "JOIN grupos_escalas as ge "
            . "ON ge.id = em.grupo_escalas "
            . ""
            . "JOIN tipo_escala as te "
            . "ON te.id = s.tipo_escala "
            . ""
            . "WHERE"
            . " em.usuario = ? "
            . "AND"
            . " $anoMes >= ? "
            . "AND"
            . " $anoMes <= ? "
            . "AND"
            . " s.turno IS NOT NULL "
            . "AND"
            . " $anoMesDia >= ? "
            . "AND"
            . " $anoMesDia <= ? "
            . "AND"
            . " te.tipo = $maiorTipo "
            . "AND"
            . " te.tipo != 4";
    $sqls[] = $sql;
    $bindings[] = array('siiii', $usuario, $anoMesIni, $anoMesTer, $inicio, $termino);

    return sql_transaction($sqls, $GLOBALS['conn'], $bindings);
}

function reinserirOsSerivicosOperacionaisDevidoAfastamento($usuario, $inicio, $termino, $anoMesIni, $anoMesTer) {
    $s1 = "CASE WHEN"
            . " ge.mes < 10 "
            . "THEN"
            . " CONCAT('0',ge.mes) "
            . "ELSE"
            . " ge.mes "
            . "END";
    $anoMes = "(CAST(CONCAT(ge.ano,$s1) AS UNSIGNED))";

    $maiorTipo = "(SELECT MAX(te2.tipo) "
            . "FROM tipo_escala as te2 "
            . "WHERE te2.grupo_escalas = ge.id)";

    $s2 = "CASE WHEN"
            . " i.dia < 10 "
            . "THEN"
            . " CONCAT('0',i.dia) "
            . "ELSE"
            . " i.dia "
            . "END";
    $anoMes = "(CAST(CONCAT(ge.ano,$s1) AS UNSIGNED))";
    $anoMesDia = "(CAST(CONCAT($anoMes,$s2) AS UNSIGNED))";

    $s3 = "SELECT "
            . "i.dia, "
            . "t.id, "
            . "ee.id as ee_id, "
            . "te.id as tipo_escala "
            . ""
            . "FROM infos as i "
            . ""
            . "LEFT JOIN efetivo_escala as ee "
            . "ON ee.id = i.efetivo_escala_id "
            . ""
            . "JOIN efetivo_mes as em "
            . "ON i.efetivo_mes_id = em.id "
            . ""
            . "JOIN grupos_escalas as ge "
            . "ON ge.id = em.grupo_escalas "
            . ""
            . "JOIN tipo_escala as te "
            . "ON te.id = i.tipo_escala "
            . ""
            . "LEFT JOIN turnos as t "
            . "ON i.texto = t.legenda AND t.grupo_escalas = ge.id "
            . ""
            . "WHERE"
            . " em.usuario = ? "
            . "AND"
            . " $anoMes >= ? "
            . "AND"
            . " $anoMes <= ? "
            . "AND"
            . " $anoMesDia >= ? "
            . "AND"
            . " $anoMesDia <= ? "
            . "AND"
            . " te.tipo = $maiorTipo "
            . "AND"
            . " te.tipo != 4";
    $sql = "INSERT INTO "
            . "servico (dia, turno, operador, tipo_escala) "
            . "$s3";
    $sqls[] = $sql;
    $bindings[] = array('siiii', $usuario, $anoMesIni, $anoMesTer, $inicio, $termino);

    $sql = "DELETE i "
            . "FROM infos as i "
            . ""
            . "LEFT JOIN efetivo_escala as ee "
            . "ON ee.id = i.efetivo_escala_id "
            . ""
            . "JOIN efetivo_mes as em "
            . "ON ee.efetivo = em.id "
            . ""
            . "JOIN grupos_escalas as ge "
            . "ON ge.id = em.grupo_escalas "
            . ""
            . "JOIN tipo_escala as te "
            . "ON te.id = i.tipo_escala "
            . ""
            . "WHERE"
            . " em.usuario = ? "
            . "AND"
            . " $anoMes >= ? "
            . "AND"
            . " $anoMes <= ? "
            . "AND"
            . " $anoMesDia >= ? "
            . "AND"
            . " $anoMesDia <= ? "
            . "AND"
            . " te.tipo = $maiorTipo "
            . "AND"
            . " te.tipo != 4";
    $sqls[] = $sql;
    $bindings[] = array('siiii', $usuario, $anoMesIni, $anoMesTer, $inicio, $termino);


    return sql_transaction($sqls, $GLOBALS['conn'], $bindings);
}

function verificarAfastamentoRemanejEscalacao($operador, $grupo, $diaEsc, $tipo) {
    $erro = false;
    $textoErro = "";

    $usuario = pegarUsuarioPeloEfetivoEscala($operador);
    $usuario = $usuario[0]['usuario'];

    $resposta = mesAnoGrupo($grupo);
    $mes = $resposta[0]['mes'];
    $ano = $resposta[0]['ano'];

    $afastamentos = pegarAfastamentosNoMes(array($usuario), $mes, $ano);

    $diaS = $diaEsc < 10 ? "0$diaEsc" : $diaEsc;
    $anoMesDia = "$ano$mes$diaS";
    $afastado = false;

    foreach ($afastamentos as $af) {
        if ($af['inicio'] <= $anoMesDia && $af['termino'] >= $anoMesDia) {
            $afastado = true;
            $erro = true;
            $resp = pegarNomeGuerraOperador($usuario);

            $posto = $resp[0]['pg'];
            $ngex = explode(" ", $resp[0]['ng']);
            $ng = $posto == "CV" ? $ngex[0] : $resp[0]['ng'];
            $nome = "$posto $ng";
            $textoErro = "$tipo pois o(a) $nome está afastado no dia $diaEsc.<br>";
            break;
        }
    }
    return array('afastado' => $afastado, 'erro' => $erro, 'textoErro' => $textoErro);
}

//function pegarIdTipoEscala($grupo, $tipo) {
//    $sql = "SELECT id "
//            . "FROM tipo_escala "
//            . "WHERE grupo_escalas = ? AND tipo = ? ";
//    $binding = array('ii', $grupo, $tipo);
//    return sql_busca($sql, $GLOBALS['conn'], $binding);
//}

function checaFolgasConsecultivas($operador, $mes, $ano, $tipoEscala, $maxFolgasConsecutivas, $geral, $diaDisp, $afastamentos, $diaEsc) {
    $errosfolgas = array();
    $servicos = pegarServicosOperadorMesAno($operador, $mes, $ano, $tipoEscala);
    $servicosOrdenados = array();
    foreach ($servicos as $s) {
        $servicosOrdenados[] = $s['dia'];
    }
    //retiro o turno dispensado
    $diaDisp != null ? array_splice($servicosOrdenados, array_search($diaDisp, $servicosOrdenados), 1) : null;

    //adiciono o turno escalado se for o caso
    $diaEsc != null ? $servicosOrdenados[] = $diaEsc : null;

    //adiciono os afastamentos
    foreach ($afastamentos as $af) {
        array_splice($servicosOrdenados, sizeof($servicosOrdenados), 0, $af);
    }

    //ordeno os dias e deixo apenas valores unicos
    $servicosOrdenados = array_unique($servicosOrdenados);
    sort($servicosOrdenados);

    $qtdDiasMes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
    if ($geral) {
        $k = 1;
        $lim = $qtdDiasMes;
    } else {
        //verifica se infrige as folgas no range dele
        $k = $diaDisp - $maxFolgasConsecutivas;
        $k = $k < 1 ? 1 : $k;
        $lim = $diaDisp;
    }
    while ($k <= $lim) {
        $qtd = 0;
        while ($k <= $qtdDiasMes && !in_array($k, $servicosOrdenados)) {
            $qtd++;
            $k++;
        }
        if ($qtd > $maxFolgasConsecutivas) {
            $ini = $k - $qtd;
            $final = $k - 1;
            while ($ini - 1 > 0 && !in_array($ini - 1, $servicosOrdenados)) {
                $ini--;
            }
            while ($final + 1 <= $qtdDiasMes && !in_array($final + 1, $servicosOrdenados)) {
                $final++;
            }
            array_splice($errosfolgas, sizeof($errosfolgas), 0, range($ini, $final));
        }
        $k++;
    }
    return array('errosfolgas' => $errosfolgas, 'servicosOrdenados' => $servicosOrdenados);
}

function pegarServicosOperador($grupo, $usuario, $tipo) {
    $sql = "SELECT te.tipo, "
            . "s.dia, "
            . "t.legenda as turno,"
            . "e.legenda as escala, "
            . "e.id as escala_id, "
            . "ge2.orgao "
            . ""
            . "FROM grupos_escalas as ge "
            . "JOIN grupos_escalas as ge2 "
            . "ON ge.mes= ge2.mes AND ge.ano = ge2.ano "
            . "JOIN efetivo_mes as em "
            . "ON em.grupo_escalas = ge2.id "
            . "JOIN efetivo_escala as ee "
            . "ON ee.efetivo = em.id "
            . "JOIN escalas as e "
            . "ON ee.escala = e.id "
            . "JOIN servico as s "
            . "ON s.operador = ee.id "
            . "JOIN tipo_escala as te "
            . "ON s.tipo_escala = te.id "
            . "LEFT JOIN turnos as t "
            . "ON s.turno = t.id "
            . "WHERE ge.id = ?  AND em.usuario = ? AND te.tipo = ? ";

    $binding = array('isi', $grupo, $usuario, $tipo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

//function checaServicosNaoPosNoturno($operador, $turnoEsc, $diaEsc, $tipoEscala, $mes, $ano) {
//    $erro = array();
//    $resposta = pegarTurno($turnoEsc);
//    //verifica que o turno atual não é pós-noturno
//    $turnoCaract = $resposta[0];
//    if ($turnoCaract['pos_noturno'] == 0) {
//        //verifica se o turno anterior a ele é noturno
//        if ($diaEsc - 1 > 0) {//verifica se continua no mesmo mês o dia anterior
//            $diaAnterior = $diaEsc - 1;
//            $mesAnterior = $mes;
//            $anoAnterior = $ano;
//        } else {
//            if ($mes - 1 > 0) {//verifica se o mês continua o mesmo do mês anterior
//                $diaAnterior = cal_days_in_month(CAL_GREGORIAN, $mes - 1, $ano);
//                $mesAnterior = $mes - 1;
//                $anoAnterior = $ano;
//            } else {
//                $diaAnterior = 31;
//                $mesAnterior = 12;
//                $anoAnterior = $ano - 1;
//            }
//        }
//        $resposta = pegarServicosOperadorDia($operador, $mesAnterior, $anoAnterior, $diaAnterior, $tipoEscala);
//        foreach ($resposta as $ta) {
//            if ($ta['periodo'] == 2) {
//                $erro = array('dia' => $diaEsc, 'dia_anterior' => "$diaAnterior/$mesAnterior/$anoAnterior");
//                break;
//            }
//        }
//    }
//    return $erro;
//}
//function checaCombinacoesTurnos($verificarCombinacaoTurnos, $combinacoesTurnos) {
//    if (sizeof($verificarCombinacaoTurnos) <= 1) {
//        $erroCombinacaoTurnos = false;
//    } else {
//        $errosDeCombinacoesPossiveis = array();
//        foreach ($combinacoesTurnos as $ct) {
//            $erroCombinacaoTurnos = true;
//
//            if (sizeof($verificarCombinacaoTurnos) == 2) {
//                if (
//                        ($verificarCombinacaoTurnos[0] == $ct[0] && $verificarCombinacaoTurnos[1] == $ct[1]) ||
//                        ($verificarCombinacaoTurnos[0] == $ct[1] && $verificarCombinacaoTurnos[1] == $ct[0])
//                ) {
//                    $erroCombinacaoTurnos = false;
//                }
//            }
//
//            $errosDeCombinacoesPossiveis[] = $erroCombinacaoTurnos;
//        }
//
//        $erroCombinacaoTurnos = !in_array(false, $errosDeCombinacoesPossiveis);
//    }
//    return $erroCombinacaoTurnos;
//}

function checaCombinacaoTurnosREDT($efetivoEscalaID, $diaEsc, $turnoEsc, $grupo, $mes, $ano, $tipoEscala) {
    $erro = false;
    $textoErro = "";
//verifica se a combinação de turnos é permitida 
    $turnosDiaEsc = pegarServicosOperadorDia($efetivoEscalaID, $mes, $ano, $diaEsc, $tipoEscala);

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
    return array('erro' => $erro, 'textoErro' => $textoErro);
}

function checaCargaHoraria($turnoDisp, $turnoEsc, $chMensal, $efetivoMesID, $tipo, $usuario, $mes, $ano) {
    $erro = false;
    $textoErro = array();
    if ($tipo == 'dispensa' || $tipo == 'remanejamento') {
        $resposta = pegarCargaHorariaTurno($turnoDisp);
        $chTurnoDisp = explode(":", $resposta[0]['ch']);
        $chDif = ($chTurnoDisp[0] + ($chTurnoDisp[1] / 60));
        $ver = $chMensal - $chDif;
    }
    if ($tipo == 'escalacao' || $tipo == 'remanejamento') {
        $resposta = pegarCargaHorariaTurno($turnoEsc);
        $chTurnoEsc = explode(":", $resposta[0]['ch']);
        $chDif = ($chTurnoEsc[0] + ($chTurnoEsc[1] / 60));
        $ver = $chMensal + $chDif;
    }
    if ($tipo == 'remanejamento') {
        $chDif = ($chTurnoEsc[0] + ($chTurnoEsc[1] / 60)) - ($chTurnoDisp[0] + ($chTurnoDisp[1] / 60));
        $ver = $chMensal + $chDif;
    }

    $afastamentos = pegarAfastamentosNoMes(array($usuario), $mes, $ano);
    $qtdDiasAfastamento = sizeof(pegarDiasAfastamentosMes($afastamentos, $ano, $mes));
    $qtdDiasMes = date("t", strtotime("$ano-$mes-1"));

    $escalas = pegarEscalasDoOperadorPeloEfetivo($efetivoMesID);

    foreach ($escalas as $e) {
        $resposta = pegarLimitesCH($e['escala']);
        $chMaxProporcional = ($qtdDiasMes - $qtdDiasAfastamento) * ($resposta[0]['ch_maxima']) / $qtdDiasMes;
        $chMinProporcional = ($qtdDiasMes - $qtdDiasAfastamento) * ($resposta[0]['ch_minima']) / $qtdDiasMes;

        //verifica se ultrapassa limite superior da carga            
        if ($ver > $chMaxProporcional) {
            $erro = true;
            $textoErro[] = "&nbsp;&nbsp;- A carga horária irá ultrapassar o limite superior permitido na escala de " . $e['legenda'] . ";";
        }
        if ($ver < $chMinProporcional) {
            $erro = true;
            $textoErro[] = "&nbsp;&nbsp;- A carga horária ficará abaixo do limite inferior permitido na escala de " . $e['legenda'] . ";";
        }
    }

    return array('erro' => $erro, 'textoErro' => $textoErro);
}

function checaTurnoNoturno($operador, $turnoEsc, $diaEsc, $tipoEscala, $mes, $ano) {
    $erro = array();
    $resposta = pegarTurno($turnoEsc);
    //verifica que o turno atual não é pós-noturno
    $turnoCaract = $resposta[0];
    if ($turnoCaract['periodo'] == 2) {
        //verifica se o turno posterior a ele é pós-noturno
        if ($diaEsc + 1 > cal_days_in_month(CAL_GREGORIAN, $mes, $ano)) {//verifica se continua no mesmo mês o dia anterior
            $diaPosterior = 1;
            if ($mes + 1 > 12) {
                $mesPosterior = 1;
                $anoPosterior = $ano + 1;
            } else {
                $mesPosterior = $mes + 1;
                $anoPosterior = $ano;
            }
        } else {
            $diaPosterior = $diaEsc + 1;
            $mesPosterior = $mes;
            $anoPosterior = $ano;
        }
        $resposta = pegarServicosOperadorDia($operador, $mesPosterior, $anoPosterior, $diaPosterior, $tipoEscala);
        foreach ($resposta as $ta) {
            if ($ta['pos_noturno'] == 0) {
                $erro = array('dia' => $diaEsc, 'dia_posterior' => "$diaPosterior/$mesPosterior/$anoPosterior");
                break;
            }
        }
    }
    return $erro;
}

function checaValidadeHabilitacao($opr, $orgaoID, $mes, $ano, $unidade) {
    $hab = verificaUsuarioHabilitacaoValida($opr, $orgaoID, $mes, $ano, $unidade);

    $anoMes = "$ano$mes";
    $inicio = 1;
    $fim = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
    $erroHab = array();
    if ($hab[0]['validade'] == "") {//habilitação vencida no mês todo
        $erroHab = range($inicio, $fim);
    } else {
        $habEx = explode("-", $hab[0]['validade']);
        $anoMesHab = $habEx[0] . $habEx[1];
        if ($anoMesHab < $anoMes) {
            $erroHab = range($inicio, $fim);
        } else if ($anoMes == $anoMesHab) {
            $inicio = $habEx[2] + 1;
            $inicio = $inicio > $fim ? 1 : $inicio;
            $erroHab = range($inicio, $fim);
        }
    }
    return $erroHab;
}

//function pegarDiasAfastamentosMes($afastOpr, $ano, $mes) {
//    $anoMes = "$ano$mes";
//    $afastMes = array();
//    foreach ($afastOpr as $af) {
//        $inicio = 1;
//        $fim = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
//        if (substr($af['inicio'], 0, 6) <= $anoMes && $anoMes >= substr($af['termino'], 0, 6)) {
//            if ($mes == substr($af['inicio'], 4, 2)) {
//                $inicio = substr($af['inicio'], 6, 2);
//            }
//            if ($mes == substr($af['termino'], 4, 2)) {
//                $fim = substr($af['termino'], 6, 2);
//            }
//        }
//        array_splice($afastMes, sizeof($afastMes), 0, range($inicio, $fim));
//    }
//    return $afastMes;
//}

function checaCargaHorariaCHT($efetivoMesID, $ano, $mes, $grupo, $tipoEscala, $usuario, $turnoDisp, $turnoEsc, $tipo) {
    $erro = false;
    $textoErro = array();
    if ($tipo == 'dispensa' || $tipo == 'remanejamento') {
        $resposta = pegarCargaHorariaTurno($turnoDisp);
        $chTurnoDisp = explode(":", $resposta[0]['ch']);
        $chDif = ($chTurnoDisp[0] + ($chTurnoDisp[1] / 60));
    }
    if ($tipo == 'remanejamento') {
        $resposta = pegarCargaHorariaTurno($turnoEsc);
        $chTurnoEsc = explode(":", $resposta[0]['ch']);
        $chDif = ($chTurnoEsc[0] + ($chTurnoEsc[1] / 60)) - ($chTurnoDisp[0] + ($chTurnoDisp[1] / 60));
    }

    $resposta = cargaHorariaOprCHT($efetivoMesID, $ano, $mes, $grupo, $tipoEscala, $usuario);
    $chCHT = $resposta[22][0]['resultado'] == null ? "00:00" : $resposta[22][0] ['resultado'];
    $chCHTA = explode(":", $chCHT);

    $chCHT = ($chCHTA[0] + ($chCHTA [1] / 60));
    if ($chCHT + $chDif < 120) {
        $erro = true;
        $textoErro[] = "&nbsp;&nbsp;- Ficará com a carga horária abaixo do limite permitido para a manutenção da habilitação ($chCHTA[0]:$chCHTA[1]);<br>";
    }
    return array('erro' => $erro, 'textoErro' => $textoErro);
}

function checarServicoEstaNoDiaEscala($efetivoMesID, $diaDisp, $escalaDisp, $tipo, $turnoDisp) {
    $turnosEscala = pegarServicosOperadorDiaEscala($efetivoMesID, $diaDisp, $escalaDisp, $tipo);

    $estaNoTurno = false;
    foreach ($turnosEscala as $t) {
        if ($t['turno'] == $turnoDisp) {
            $estaNoTurno = true;
        }
    }

    return $estaNoTurno;
}

function pegarTrocasLancadasUsuario($usuario, $grupo, $tipo, $status) {
    $binding = array('', $grupo, $usuario, $tipo);
    $strings = 'isi';
    foreach ($status as $s) {
        $sq[] = 'tc.status = ?';
        $strings .= 'i';
        $binding[] = $s;
    }

    $binding[0] = $strings;
    $sqstring = implode(" OR ", $sq);

    $sql = "SELECT tc.id, "
            . "em2.usuario as usuPE, "
            . "tc.dia as diaPE, "
            . "t.id as turnoPE, "
            . "t.legenda as turnoLegPE, "
            . "e2.id as escalaPE, "
            . "e2.legenda as escalaLegPE, "
            . ""
            . "em3.usuario as usuPO, "
            . "tc.dia_proposto as diaPO, "
            . "t2.id as turnoPO, "
            . "t2.legenda as turnoLegPO, "
            . "e3.id as escalaPO, "
            . "e3.legenda as escalaLegPO, "
            . ""
            . "tc.tipo, "
            . "tc.status "
            . ""
            . "FROM efetivo_mes as em "
            . "JOIN efetivo_escala as ee "
            . "ON ee.efetivo = em.id "
            . "JOIN trocas as tc "
            . "ON tc.proponente = ee.id OR tc.proposto = ee.id "
            . ""
            . "JOIN turnos as t "
            . "ON t.id = tc.turno_proponente "
            . "JOIN turnos as t2 "
            . "ON t2.id = tc.turno_proposto "
            . ""
            . "JOIN efetivo_escala as ee2 "
            . "ON ee2.id = tc.proponente "
            . "JOIN efetivo_mes as em2 "
            . "ON ee2.efetivo = em2.id "
            . "JOIN escalas as e2 "
            . "ON ee2.escala = e2.id "
            . ""
            . "JOIN efetivo_escala as ee3 "
            . "ON ee3.id = tc.proposto "
            . "JOIN efetivo_mes as em3 "
            . "ON ee3.efetivo = em3.id "
            . "JOIN escalas as e3 "
            . "ON ee3.escala = e3.id "
            . ""
            . "WHERE em.grupo_escalas = ? AND em.usuario = ? AND tc.tipo = ? AND ($sqstring) ";

    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarTrocasLancadas($grupo, $tipo, $status) {
    $binding = array('', $grupo, $tipo);
    $strings = 'ii';
    foreach ($status as $s) {
        $sq[] = 'tc.status = ?';
        $strings .= 'i';
        $binding[] = $s;
    }

    $binding[0] = $strings;
    $sqstring = implode(" OR ", $sq);


    $sql = "SELECT tc.id, "
            . "em2.usuario as usuPE, "
            . "tc.dia as diaPE, "
            . "t.id as turnoPE, "
            . "t.legenda as turnoLegPE, "
            . "e2.id as escalaPE, "
            . "e2.legenda as escalaLegPE, "
            . ""
            . "em3.usuario as usuPO, "
            . "tc.dia_proposto as diaPO, "
            . "t2.id as turnoPO, "
            . "t2.legenda as turnoLegPO, "
            . "e3.id as escalaPO, "
            . "e3.legenda as escalaLegPO, "
            . ""
            . "tc.tipo, "
            . "tc.status "
            . ""
            . "FROM trocas as tc "
            . ""
            . "JOIN turnos as t "
            . "ON t.id = tc.turno_proponente "
            . "JOIN turnos as t2 "
            . "ON t2.id = tc.turno_proposto "
            . ""
            . "JOIN efetivo_escala as ee2 "
            . "ON ee2.id = tc.proponente "
            . "JOIN efetivo_mes as em2 "
            . "ON ee2.efetivo = em2.id "
            . "JOIN escalas as e2 "
            . "ON ee2.escala = e2.id "
            . ""
            . "JOIN efetivo_escala as ee3 "
            . "ON ee3.id = tc.proposto "
            . "JOIN efetivo_mes as em3 "
            . "ON ee3.efetivo = em3.id "
            . "JOIN escalas as e3 "
            . "ON ee3.escala = e3.id "
            . ""
            . "WHERE tc.grupo_escalas = ? AND tc.tipo = ? AND ($sqstring) ";

    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarHistoricoTroca($trocaID) {
    $sql = "SELECT * "
            . "FROM trocas_status "
            . "WHERE troca = ? "
            . "ORDER BY data DESC ";

    $binding = array('i', $trocaID);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarTroca($id) {

    $sql = "SELECT tc.id, "
            . "em2.usuario as usuPE, "
            . "tc.proponente as eeIDPE, "
            . "tc.dia as diaPE, "
            . "t.id as turnoPE, "
            . "t.legenda as turnoLegPE, "
            . "e2.id as escalaPE, "
            . "e2.legenda as escalaLegPE, "
            . ""
            . "em3.usuario as usuPO, "
            . "tc.proposto as eeIDPO, "
            . "tc.dia_proposto as diaPO, "
            . "t2.id as turnoPO, "
            . "t2.legenda as turnoLegPO, "
            . "e3.id as escalaPO, "
            . "e3.legenda as escalaLegPO, "
            . ""
            . "tc.tipo, "
            . "tc.status "
            . ""
            . "FROM trocas as tc "
            . ""
            . "JOIN turnos as t "
            . "ON t.id = tc.turno_proponente "
            . "JOIN turnos as t2 "
            . "ON t2.id = tc.turno_proposto "
            . ""
            . "JOIN efetivo_escala as ee2 "
            . "ON ee2.id = tc.proponente "
            . "JOIN efetivo_mes as em2 "
            . "ON ee2.efetivo = em2.id "
            . "JOIN escalas as e2 "
            . "ON ee2.escala = e2.id "
            . ""
            . "JOIN efetivo_escala as ee3 "
            . "ON ee3.id = tc.proposto "
            . "JOIN efetivo_mes as em3 "
            . "ON ee3.efetivo = em3.id "
            . "JOIN escalas as e3 "
            . "ON ee3.escala = e3.id "
            . ""
            . "WHERE tc.id = ? ";
    $binding = array('i', $id);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarEfetivoMesID($usuario, $grupo) {
    $sql = "SELECT id "
            . "FROM efetivo_mes "
            . "WHERE usuario = ? AND grupo_escalas = ? ";
    $bindings = array('si', $usuario, $grupo);
    return sql_busca($sql, $GLOBALS['conn'], $bindings);
}

function pegarIDServicoOperador($eeID, $dia, $turno, $grupo, $tipo) {
    $sql = "SELECT s.id "
            . "FROM servico  as s "
            . "JOIN tipo_escala as te "
            . "WHERE s.operador = ? AND s.dia = ? AND s.turno = ? AND  te.grupo_escalas = ? AND te.tipo = ?";
    $bindings = array('iiiii', $eeID, $dia, $turno, $grupo, $tipo);
    return sql_busca($sql, $GLOBALS['conn'], $bindings);
}

function efetivarTroca($trocaId, $grupo, $tipo, $texto, $status) {
    $r = pegarTroca($trocaId);
//    print_r($r);
    $troca = $r[0];

    //verifico se o proponente está na escala do proposto
    $r = pegarEfetivoMesID($troca['usuPE'], $grupo);
    $efetivoMesIDPE = $r[0]['id'];

    $r = pegarEfetivoEscalaID($efetivoMesIDPE, $troca['escalaPO']);
    $PEestaEscalaPO = !empty($r);
    $eeIDPEPO = empty($r) ? 0 : $r[0]['id'];
//    
    //verifico se o proposto está na escala do proponente
    $r = pegarEfetivoMesID($troca['usuPO'], $grupo);
    $efetivoMesIDPO = $r[0]['id'];

    $r = pegarEfetivoEscalaID($efetivoMesIDPO, $troca['escalaPE']);
    $POestaEscalaPE = !empty($r);
    $eeIDPOPE = empty($r) ? 0 : $r[0]['id'];

    if ($PEestaEscalaPO && $POestaEscalaPE) {
        $eeIDdestinoPE = $eeIDPEPO;
        $eeIDdestinoPO = $eeIDPOPE;
    } else {
        $eeIDdestinoPE = $t['eeIDPE'];
        $eeIDdestinoPO = $t['eeIDPO'];
    }

    //pegar o id do servico que será retirado o proponente
    $r = pegarIDServicoOperador($troca['eeIDPE'], $troca['diaPE'], $troca['turnoPE'], $grupo, $tipo);
    $servicoPE = $r[0]['id'];

    //pegar o id do servico que será retirado o proposto
    $r = pegarIDServicoOperador($troca['eeIDPO'], $troca['diaPO'], $troca['turnoPO'], $grupo, $tipo);
    $servicoPO = $r[0]['id'];

    $sql = "UPDATE servico "
            . "SET operador = ? "
            . "WHERE id = ? "; //deixe sempre  um espaco no final    
    $binding = array('ii', $eeIDdestinoPE, $servicoPO);
    $sqls[] = $sql;
    $bindings[] = $binding;

    $sql = "UPDATE servico "
            . "SET operador = ? "
            . "WHERE id = ? "; //deixe sempre  um espaco no final    
    $binding = array('ii', $eeIDdestinoPO, $servicoPE);
    $sqls[] = $sql;
    $bindings[] = $binding;

    $sql = "UPDATE trocas  "
            . "SET status = ? "
            . "WHERE id = ? ";
    $sqls[] = $sql;
    $bindings[] = array('ii', $status, $trocaId);

    $usuario = ($status == 4) ? "auto" : $GLOBALS['sessao']['usu'];
    $sql = "INSERT INTO trocas_status "
            . "(troca, usuario, data, texto, status) "
            . "VALUES (?, ?, NOW(), ?, ?)";
    $sqls[] = $sql;
    $bindings[] = array('issi', $trocaId, $usuario, $texto, $status);

    return sql_transaction($sqls, $GLOBALS['conn'], $bindings);
}

function lancarTrocasAutomaticamente($grupo, $tipo) {
    $reiniciar = true;
    $qtd = 0;
    while ($reiniciar) {
        $reiniciar = false;
        $trocas = pegarTrocasLancadas($grupo, $tipo, array(1));
        foreach ($trocas as $t) {
            $resp1 = analisarTroca(false, $grupo, $t['usuPE'], $t['escalaPE'], $t['diaPO'], $t['tipo'], $t['diaPE'], $t['turnoPE'], $t['turnoPO'], $t['turnoLegPE'], $t['escalaLegPE'], $t['turnoLegPO'], $t['escalaPO'], $t['usuPO']);
            $corErro[] = $resp1['corErro'];

            $resp2 = analisarTroca(false, $grupo, $t['usuPO'], $t['escalaPO'], $t['diaPE'], $t['tipo'], $t['diaPO'], $t['turnoPO'], $t['turnoPE'], $t['turnoLegPO'], $t['escalaLegPO'], $t['turnoLegPE'], $t['escalaPE'], $t['usuPE']);
            $corErro[] = $resp2['corErro'];
            $efetivar = in_array('danger', $corErro) ? false : (in_array('warning', $corErro) ? false : true);

            if ($efetivar) {
                efetivarTroca($t['id'], $grupo, $tipo, '--', 4);
                $reiniciar = true;
                $qtd++;
            }
        }
    }
    return $qtd;
}

function analisarTroca($lancamento, $grupo, $operador, $escalaDisp, $diaEsc, $tipo, $diaDisp, $turnoDisp, $turnoEsc, $legendaTurnoDisp, $legendaEscDisp, $legendaTurnoEsc, $escalaEsc, $usuPO) {

    $erro = false;
    $critico = false;
    $textoErro = array();
    $resposta = pegarDadosGrupo($grupo);
    $mes = $resposta['mes'];
    $ano = $resposta['ano'];
    $maxFolgasConsecutivas = $resposta['qtd_folgas'];
    $maxTrocas = $resposta['qtd_trocas'];
    $qtdMaxEtapas = 10;

    $resposta = pegarIdEfetivoMes($grupo, $operador);
    $efetivoMesID = $resposta[0]['id'];

    $resposta = pegarEfetivoEscalaID($efetivoMesID, $escalaDisp);
    $eeID = $resposta[0]['id'];

    $resposta = verificarAfastamentoRemanejEscalacao($eeID, $grupo, $diaEsc, $tipo);

    if ($resposta['afastado']) {
        $erro = true;
        $critico = true;
        $textoErro[] = "&nbsp;&nbsp;- Está afastado no dia $diaEsc;";
    } else {

        //verifico se o proponente está na escala do proposto
        $r = pegarEfetivoMesID($operador, $grupo);
        $efetivoMesIDPE = $r[0]['id'];

        $r = pegarEfetivoEscalaID($efetivoMesIDPE, $escalaEsc);
        $PEestaEscalaPO = !empty($r);
//    
        //verifico se o proposto está na escala do proponente
        $r = pegarEfetivoMesID($usuPO, $grupo);
        $efetivoMesIDPO = $r[0]['id'];
//
        $r = pegarEfetivoEscalaID($efetivoMesIDPO, $escalaDisp);
        $POestaEscalaPE = !empty($r);
//
        if ($PEestaEscalaPO && $POestaEscalaPE) {
            $escalaDestino = $escalaEsc;
        } else {
            $escalaDestino = $escalaDisp;
        }
        //checar se o turno escalado existe na escala de destino
        $turnosEscala = listaTurnosEscala($escalaDestino);
        $turnosEscalaId = array();
        foreach ($turnosEscala as $te) {
            $turnosEscalaId[] = $te['id'];
        }

        if (!in_array($turnoEsc, $turnosEscalaId)) {
            $erro = true;
            $critico = true; //cria um erro critico caso não for uma análise de lançamento
            $textoErro[] = "&nbsp;&nbsp;- O turno $legendaTurnoEsc não existe na escala de destino;<br>";
        } else {
            $erroEstaTurno = checarServicoEstaNoDiaEscala($efetivoMesID, $diaDisp, $escalaDisp, $tipo, $turnoDisp);
            if (!$erroEstaTurno) {
                $erro = true;
                $critico = !$lancamento; //cria um erro critico caso não for uma análise de lançamento
                $textoErro[] = "&nbsp;&nbsp;- Não está de $legendaTurnoDisp no dia $diaDisp na escala de $legendaEscDisp;<br>";
            }

            $resposta = cargaHorariaOprMes($efetivoMesID, $mes, $ano, $tipo);

            $chm = explode(":", $resposta[0]['chm']);
            $chMensal = ($chm[0] + ($chm[1] / 60));
            $resposta = checaCargaHoraria($turnoDisp, $turnoEsc, $chMensal, $efetivoMesID, 'remanejamento', $operador, $mes, $ano);

            if ($resposta['erro']) {
                $erro = true;
                $textoErro = array_merge($textoErro, $resposta['textoErro']);
            }

            $resposta = checaCargaHorariaCHT($efetivoMesID, $ano, $mes, $grupo, $tipo, $operador, $turnoDisp, $turnoEsc, 'remanejamento');
            if ($resposta['erro']) {
                $erro = true;
                $textoErro = array_merge($textoErro, $resposta['textoErro']);
            }

            $resposta = pegarIdTipoEscala($grupo, $tipo);
            $tipoEscalaID = $resposta[0]['id'];

            $resposta = pegarIdEfetivoEscala($grupo, $operador, $escalaDisp);
            $eeID = $resposta[0]['id'];

            $resposta = checaCombinacaoTurnosREDT($eeID, $diaEsc, $turnoEsc, $grupo, $mes, $ano, $tipoEscalaID);
            if ($resposta['erro']) {
                $erro = true;
                $textoErro[] = "&nbsp;&nbsp;- Combinação de turnos não permitida no dia $diaEsc;";
            }

            //verifica se dia tem mais de 2 turnos
            $turnosDiaEsc = pegarServicosOperadorDia($eeID, $mes, $ano, $diaEsc, $tipoEscalaID);

            if (sizeof($turnosDiaEsc) >= 2) {//já tem 2 turnos no dia
                $erro = true;
                $textoErro[] = "&nbsp;&nbsp;- Já existem 2 ou mais turnos escalados para o dia $diaEsc;";
            }

            if ($maxFolgasConsecutivas != 11) {
                $afastamentos = pegarAfastamentosNoMes(array($operador), $mes, $ano);
                $resposta = checaFolgasConsecultivas($eeID, $mes, $ano, $tipoEscalaID, $maxFolgasConsecutivas, false, $diaDisp, $afastamentos, $diaEsc);
                if (!empty($resposta['errosfolgas'])) {
                    $erro = true;
                    $textoErro[] = "&nbsp;&nbsp;- Máximo de folgas consecutivos($maxFolgasConsecutivas) ultrapassadas na sequência de " . $resposta['errosfolgas'][0] . " a " . end($resposta['errosfolgas']) . ";";
                }
            }


            $resposta = checaServicosNaoPosNoturno($eeID, $turnoEsc, $diaEsc, $tipoEscalaID, $mes, $ano);
            if (!empty($resposta)) {
                $erro = true;
                $textoErro[] = "&nbsp;&nbsp;- O turno $legendaTurnoEsc não pode ser escalado no dia $diaEsc após um turno noturno;";
            }

            $resposta = checaTurnoNoturno($eeID, $turnoEsc, $diaEsc, $tipoEscalaID, $mes, $ano);
            if (!empty($resposta)) {
                $erro = true;
                $textoErro[] = "&nbsp;&nbsp;- O turno $legendaTurnoEsc não pode ser escalado no dia $diaEsc pois no dia " . $resposta['dia_posterior'] . " está alocado um turno que não pode ser exercido após um turno noturno;";
            }

            if ($maxFolgasConsecutivas != 11) {
                //verificar a quantidade maxima de trocas permitida para cada operador
                $resposta = qtdTrocasOpr($efetivoMesID);
                $qtdTrocas = $resposta[0]['qtd'];
                if ($qtdTrocas + 1 > $maxTrocas) {
                    $erro = true;
                    $textoErro[] = "&nbsp;&nbsp;- Terá um total de (" . ($qtdTrocas + 1) . ") trocas, ultrapassando o máximo permitdo de ($maxTrocas) trocas no mês;<br>";
                }
            }

            //verificar a quantidade maxima de etapas permitida para cada operador
            $resposta = qtdEtapas($efetivoMesID, $mes, $ano, $tipo);
            $qtdEtapas = $resposta[0]['qtd'];
            if ($qtdEtapas + 1 > $qtdMaxEtapas) {
                $erro = true;
                $textoErro[] = "&nbsp;&nbsp;-Terá um total de (" . ($qtdEtapas + 1) . ") etapas 5X, ultrapassando o máximo de permitdo de ($qtdMaxEtapas) etapas 5X no mês;<br>";
            }
        }
    }
    if (!$erro) {
        $textoErro = array();
        $textoErro[] = "&nbsp;&nbsp;- Não possui nenhuma restrição para esta troca.";
        $corErro = "success";
    } else {
        $corErro = $critico ? "danger" : "warning";
    }
    return array('corErro' => $corErro, 'textoErro' => $textoErro);
}

function alterarTexto($dia, $efetivo_mes, $tipoEscala, $remover, $inserir) {
    $sqls = array();
    $bindings = array();
    //removendo
    foreach ($remover as $r) {
        $sql = "DELETE FROM infos ";
        $sql .= "WHERE id = ? "; //deixe sempre  um espaco no final    
        $binding = array('i', $r);

        $sqls[] = $sql;
        $bindings[] = $binding;
    }

    //inserindo
    foreach ($inserir as $in) {
        $sql = "INSERT INTO infos (dia, texto, efetivo_mes_id, tipo_escala) "; //deixe sempre  um espaco no final
        $sql .= "VALUES (?, ?, ?, ?) "; //deixe sempre  um espaco no final
        $binding = array('isii', $dia, $in, $efetivo_mes, $tipoEscala);

        $sqls[] = $sql;
        $bindings[] = $binding;
    }

    return sql_transaction($sqls, $GLOBALS['conn'], $bindings);
}

function pegarservicosOperadoresNasEscalas($escalas, $tipo) {
    $sql = "SELECT "
            . "em.usuario, "
            . "fe.nome as funcao, "
            . "s.dia, "
            . "s.turno, "
            . "t.nome as t_nome, "
            . "t.inicio as t_ini, "
            . "t.termino as  t_term, "
            . "ge.mes, "
            . "ge.ano "
            . ""
            . "FROM efetivo_escala as ee "
            . ""
            . "JOIN efetivo_mes as em "
            . "ON ee.efetivo =  em.id "
            . ""
            . "JOIN funcoes_escala as fe "
            . "ON fe.id = em.funcao_escala "
            . ""
            . "JOIN servico as s "
            . "ON s.operador = ee.id "
            . ""
            . "JOIN tipo_escala as te "
            . "ON te.id = s.tipo_escala "
            . ""
            . "JOIN grupos_escalas as ge "
            . "ON ge.id =  te.grupo_escalas "
            . ""
            . "JOIN turnos as t "
            . "ON t.id = s.turno "
            . ""
            . "WHERE ee.escala IN (";
    $var = array();
    $binding[] = "";
    foreach ($escalas as $id) {
        $var[] = "?";
        $binding[0] .= "i";
        $binding[] = $id;
    }

    $sql .= implode(",", $var) . ") AND te.tipo = ? ";
    $binding[0] .= "i";
    $binding[] = $tipo;

    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarNomeCompletoOperadores($usuarios) {

    $sql = "SELECT cadastroID as usuario, posto AS pg, NOME AS nome ";
    $sql .= "FROM cadastros ";
    $sql .= "WHERE cadastroID IN (";
    $var = array();
    $binding[] = "";
    foreach ($usuarios as $id) {
        $var[] = "?";
        $binding[0] .= "s";
        $binding[] = $id;
    }

    $sql .= implode(",", $var) . ") ";
    $resposta = sql_busca($sql, $GLOBALS['connL'], $binding);

    $resp = array();
    foreach ($resposta as $r) {
        $resp[$r['usuario']] = $r['pg'] . " " . $r['nome'];
    }
    return $resp;
}

function ehXMesesAnteriores($mesAtual, $anoAtual, $mesVer, $anoVer) {
    for ($i = 0; $i < 3; $i++) {
        $mesAtual = $mesAtual - 1 > 0 ? $mesAtual - 1 : 12;
        $anoAtual = $mesAtual == 12 ? $anoAtual - 1 : $anoAtual;
        if ($mesAtual == $mesVer && $anoAtual == $anoVer) {
            return true;
        }
    }
    return false;
}

function pegarEfetivoMesDaEscala($escalaId) {
    //pegar os grupos de escalas onde usuarios que estao nesta escala possuem servico
    $sql = "SELECT "
            . "ee.id as ee_id, "
            . "ee.escala, "
            . "em.id as em_id, "
            . "em.legenda, "
            . "em.usuario, "
            . "em.manutencao "
            . "FROM efetivo_escala as ee "
            . "JOIN efetivo_mes as em "
            . "ON ee.efetivo = em.id "
            . "WHERE ee.escala = ? ";
    $binding = array('i', $escalaId);

    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarGruposAtualAnteriorPosteriorEfetivoEscala($usuarios, $mesAnoArray) {


    //pegar os grupos de escalas onde usuarios que estao nesta escala possuem servico
    $sql = "SELECT DISTINCT ge.* "
            . "FROM efetivo_mes as em "
            . "JOIN grupos_escalas as ge "
            . "ON em.grupo_escalas =  ge.id "
            . "WHERE usuario IN (";
    $var = array();
    $binding[] = "";
    foreach ($usuarios as $id) {
        $var[] = "?";
        $binding[0] .= "i";
        $binding[] = $id;
    }
    $sql .= implode(",", $var) . ") AND (";
    $var = array();
    foreach ($mesAnoArray as $ma) {
        $var[] = "(ge.mes = ? AND ge.ano = ?)";
        $binding[0] .= "ii";
        $binding[] = $ma[0];
        $binding[] = $ma[1];
    }
    $sql .= implode(" OR ", $var) . ") ";

    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function pegarEscalasGAM($grupos) {
    $sql = "SELECT * "; //deixe sempre  um espaco no final
    $sql .= "FROM escalas "; //deixe sempre  um espaco no final
    $sql .= "WHERE grupo_escalas IN (";
    $var = array();
    $binding[] = "";
    foreach ($grupos as $id) {
        $var[] = "?";
        $binding[0] .= "i";
        $binding[] = $id;
    }

    $sql .= implode(",", $var) . ") "; //deixe sempre  um espaco no final;
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function servicoTotaisDasEscalas($grupos) {

    //pegar os grupos de escalas onde usuarios que estao nesta escala possuem servico
    $sql = "SELECT "
            . "em.usuario, "
            . "em.id as em_id, "
            . "em.legenda as usu_leg, "
            . "em.manutencao, "
            . "ee.id as ee_id, "
            . "e.id as esc_id, "
            . "e.legenda as esc_leg, "
            . "s.id as svc_id, "
            . "s.dia, "
            . "t.id as turno_id, "
            . "t.legenda as turnos_leg, "
            . "t.etapa_full as etapa, "
            . "t.inicio, "
            . "t.termino, "
            . "t.periodo, "
            . "t.pos_noturno, "
            . "te.tipo as tipo_escala, "
            . "ge.orgao, "
            . "ge.unidade, "
            . "ge.mes, "
            . "ge.ano,"
            . "ge.id as grupo "
            . ""
            . "FROM efetivo_mes as em "
            . "JOIN efetivo_escala as ee "
            . "ON ee.efetivo = em.id "
            . ""
            . "JOIN escalas as e "
            . "ON ee.escala = e.id "
            . ""
            . "JOIN servico as s "
            . "ON s.operador = ee.id "
            . ""
            . "JOIN turnos as t "
            . "ON t.id = s.turno "
            . ""
            . "JOIN tipo_escala as te "
            . "ON te.id =  s.tipo_escala "
            . ""
            . "JOIN grupos_escalas as ge "
            . "ON ge.id = te.grupo_escalas "
            . ""
            . "WHERE ge.id IN(";
    $var = array();
    $binding[] = "";
    foreach ($grupos as $id) {
        $var[] = "?";
        $binding[0] .= "i";
        $binding[] = $id;
    }

    $sql .= implode(",", $var) . ")";

    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function servicoTotaisRISAER($usuariosEscala, $grupos) {

    //pegar os grupos de escalas onde usuarios que estao nesta escala possuem servico
    $sql = "SELECT "
            . "em.usuario, "
            . "em.id as em_id, "
            . "em.legenda as usu_leg, "
            . "em.manutencao, "
            . "sr.id as svcr_id, "
            . "sr.dia, "
            . "r.id as risaer_id, "
            . "r.legenda as risaer_leg, "
            . "r.tipo_etapa, "
            . "r.inicio, "
            . "r.termino, "
            . "r.mais_q_24h, "
            . "te.tipo as tipo_escala, "
            . "ge.mes, "
            . "ge.ano, "
            . "ge.orgao,"
            . "ge.unidade "
            . ""
            . "FROM efetivo_mes as em "
            . ""
            . "JOIN servico_risaer as sr "
            . "ON sr.efetivo_mes = em.id "
            . ""
            . "LEFT JOIN risaer as r "
            . "ON r.id = sr.servico "
            . ""
            . "LEFT JOIN tipo_escala as te "
            . "ON te.id =  sr.tipo_escala "
            . ""
            . "LEFT JOIN grupos_escalas as ge "
            . "ON ge.id = te.grupo_escalas "
            . ""
            . "WHERE em.usuario IN (";
    $var = array();
    $binding[] = "";
    foreach ($usuariosEscala as $id) {
        $var[] = "?";
        $binding[0] .= "s";
        $binding[] = $id;
    }

    $sql .= implode(",", $var) . ") AND em.grupo_escalas IN(";
    $var = array();
    foreach ($grupos as $id) {
        $var[] = "?";
        $binding[0] .= "i";
        $binding[] = $id;
    }

    $sql .= implode(",", $var) . ")";

    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function servicoTotaisINFO($usuariosEscala, $grupos) {

    //pegar os grupos de escalas onde usuarios que estao nesta escala possuem servico
    $sql = "SELECT "
            . "em.usuario, "
            . "em.id as em_id, "
            . "em.legenda as usu_leg, "
            . "em.manutencao, "
            . "i.id as info_id, "
            . "i.dia, "
            . "i.texto, "
            . "te.tipo as tipo_escala, "
            . "ge.orgao, "
            . "ge.unidade, "
            . "ge.mes,"
            . "ge.ano "
            . ""
            . "FROM efetivo_mes as em "
            . ""
            . "JOIN infos as i "
            . "ON i.efetivo_mes_id = em.id "
            . ""
            . "JOIN tipo_escala as te "
            . "ON te.id =  i.tipo_escala "
            . ""
            . "JOIN grupos_escalas as ge "
            . "ON ge.id = te.grupo_escalas "
            . ""
            . "WHERE em.usuario IN (";
    $var = array();
    $binding[] = "";
    foreach ($usuariosEscala as $id) {
        $var[] = "?";
        $binding[0] .= "s";
        $binding[] = $id;
    }

    $sql .= implode(",", $var) . ") AND em.grupo_escalas IN(";
    $var = array();
    foreach ($grupos as $id) {
        $var[] = "?";
        $binding[0] .= "i";
        $binding[] = $id;
    }

    $sql .= implode(",", $var) . ")";

    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function listaTurnosEscalaComTurnosSecundariosDaSoma($id) {
    $sql = "SELECT "
            . "te.id as te_id, "
            . "te.escala, "
            . "e.legenda as escala_legenda, "
            . "t.id, "
            . "t.legenda, "
            . "t.nome,"
            . "t2.id as ts_id, "
            . "t2.legenda as ts_leg, "
            . "t2.nome as ts_nome, "
            . "te.ref_soma "
            . ""
            . "FROM turno_escala AS te "
            . ""
            . "JOIN turnos AS t "
            . "ON t.id = te.turno "
            . ""
            . "LEFT JOIN turnos_soma_qtd as tsq "
            . "ON te.id = tsq.turno_escala_principal "
            . ""
            . "LEFT JOIN turno_escala as te2 "
            . "ON te2.id = tsq.turno_escala_secundario "
            . ""
            . "LEFT JOIN turnos as t2 "
            . "ON t2.id = te2.turno "
            . ""
            . "JOIN escalas AS e "
            . "ON e.id = te.escala "
            . ""
            . "WHERE te.escala = ? "; //deixe sempre  um espaco no final    
    $binding = array('i', $id);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function listaTurnosGrupoComTurnosSecundariosDaSoma($grupo) {
    $sql = "SELECT "
            . "t.id, "
            . "t.legenda, "
            . "t.nome,"
            . "t2.id as ts_id, "
            . "t2.legenda as ts_leg, "
            . "t2.nome as ts_nome, "
            . "t.ref_soma_geral "
            . ""
            . "FROM turnos AS t "
            . ""
            . "LEFT JOIN turnos_soma_qtd_geral as tsqg "
            . "ON t.id = tsqg.turno_principal "
            . ""
            . "LEFT JOIN turnos as t2 "
            . "ON t2.id = tsqg.turno_secundario "
            . ""
            . "WHERE t.grupo_escalas = ? "; //deixe sempre  um espaco no final    
    $binding = array('i', $grupo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
}

function qtdOprTurnoDias($turnos, $servicos, $qtdDiaMes, $escalaId, $tipo) {
    for ($i = 1; $i <= $qtdDiaMes; $i++) {
        $qtdDia[$i] = 0;
    }

    foreach ($servicos as $s) {
        if ($s['esc_id'] == $escalaId && $s['tipo_escala'] == $tipo) {
            in_array($s['turno_id'], $turnos) ? $qtdDia[$s['dia']] ++ : null;
        }
    }

    return $qtdDia;
}

function qtdOprTurnoDiasGeral($turnos, $servicos, $qtdDiaMes, $escalasSoma, $tipo) {
    for ($i = 1; $i <= $qtdDiaMes; $i++) {
        $qtdDia[$i] = 0;
    }

    foreach ($servicos as $s) {
        if (in_array($s['esc_id'], $escalasSoma) && $s['tipo_escala'] == $tipo) {
            in_array($s['turno_id'], $turnos) ? $qtdDia[$s['dia']] ++ : null;
        }
    }

    return $qtdDia;
}

function pegarAfastamentosNoMes($usuarios, $mes, $ano) {
    $s1 = "CASE WHEN"
            . " MONTH(dt_i) < 10 "
            . "THEN"
            . " CONCAT('0',MONTH(dt_i)) "
            . "ELSE"
            . " MONTH(dt_i) "
            . "END";
    $inicio = "CAST(CONCAT(YEAR(dt_i),$s1) AS UNSIGNED)";

    $s2 = "CASE WHEN"
            . " MONTH(dt_f) < 10 "
            . "THEN"
            . " CONCAT('0',MONTH(dt_f)) "
            . "ELSE"
            . " MONTH(dt_f) "
            . "END";
    $termino = "CAST(CONCAT(YEAR(dt_f),$s2) AS UNSIGNED)";

    $mes = $mes < 10 ? "0" . $mes : $mes;
    $anoMes = $ano . $mes;

    $sql = "SELECT  cadastroID as usuario, tipo, REPLACE(dt_i,'-','') as inicio, REPLACE(dt_f,'-','') as termino "
            . "FROM afastamentos "
            . "WHERE cadastroID IN(";
    $var = array();
    $binding[] = "";
    foreach ($usuarios as $id) {
        $var[] = "?";
        $binding[0] .= "s";
        $binding[] = $id;
    }
    $binding[0] .= "ii";
    $binding[] = $anoMes;
    $binding[] = $anoMes;

    $sql .= implode(",", $var) . ") AND $inicio <= ? AND $termino >= ? ";
    return sql_busca($sql, $GLOBALS['connL'], $binding);
}

function pegarNomeGuerraOperadores($usuarios) {
    $sb1 = "(CASE WHEN"
            . " posto = 'CV' "
            . "THEN"
            . " NOME "
            . "ELSE"
            . " nome_guerra "
            . "END)";

    $sql = "SELECT cadastroID as usuario, posto AS pg, $sb1 AS ng ";
    $sql .= "FROM cadastros ";
    $sql .= "WHERE cadastroID IN (";
    $var = array();
    $binding[] = "";
    foreach ($usuarios as $id) {
        $var[] = "?";
        $binding[0] .= "s";
        $binding[] = $id;
    }

    $sql .= implode(",", $var) . ") ";
    $resposta = sql_busca($sql, $GLOBALS['connL'], $binding);

    $resp = array();
    foreach ($resposta as $r) {
        $nome = explode(" ", $r['ng']);
        $ng = $r['pg'] == 'CV' ? $nome[0] : $r['ng'];
        $resp[$r['usuario']] = $r['pg'] . " $ng";
    }
    return $resp;
}

function qtdEtapasPorServicos($servicos) {
    $qtd = 0;
    foreach ($servicos as $dia) {
        if (array_key_exists('operacionais', $dia)) {
            foreach ($dia['operacionais'] as $o) {
                $o['etapa'] ? $qtd++ : null;
            }
        }
        if (array_key_exists('risaer', $dia)) {
            foreach ($dia['risaer'] as $r) {
                $r['tipo_etapa'] > 1 ? $qtd++ : null;
            }
        }
    }
    return $qtd;
}

function pegarComentariosUsuarios($usuarios, $grupo) {
    $sql = "SELECT a.id, a.texto, em.usuario "
            . "FROM efetivo_mes as em "
            . "JOIN anotacoes as a "
            . "ON a.operador = em.id "
            . "WHERE em.usuario IN(";
    $var = array();
    $binding[] = "";
    foreach ($usuarios as $id) {
        $var[] = "?";
        $binding[0] .= "s";
        $binding[] = $id;
    }

    $sql .= implode(",", $var) . ") AND em.grupo_escalas = ? ";
    $binding[0] .= "i";
    $binding[] = $grupo;
    $resp = sql_busca($sql, $GLOBALS['conn'], $binding);
    $resposta = array();
    foreach ($resp as $r) {
        $resposta[$r['usuario']] = array('id' => $r['id'], 'texto' => $r['texto']);
    }
    return $resposta;
}

function verificaHabilitacaoValidaUsuarios($usuarios, $orgID, $ano, $mes, $unidade) {
    $sub1 = "(SELECT MONTH(h2.dt_validade) FROM habilitacoes h2 WHERE h2.habilitacaoID = h.habilitacaoID)";
    $sub2 = "(SELECT YEAR(h2.dt_validade) FROM habilitacoes h2 WHERE h2.habilitacaoID = h.habilitacaoID)";

    $sub4 = "(CASE WHEN"
            . " $sub2 > ? "
            . "THEN"
            . "  TRUE "
            . "ELSE"
            . " (CASE WHEN"
            . "   $sub2 = ? "
            . " THEN"
            . "    (CASE WHEN"
            . "      $sub1 >= ? "
            . "    THEN"
            . "     TRUE "
            . "    ELSE"
            . "     FALSE"
            . "    END) "
            . " ELSE"
            . "  FALSE"
            . " END)"
            . "END)";

    $sql = "SELECT MAX(h.dt_validade) AS validade, h.cadastroID as usuario "
            . "FROM habilitacoes h "
            . "WHERE h.cadastroID IN (";
    $var = array();
    $binding[] = "";
    foreach ($usuarios as $id) {
        $var[] = "?";
        $binding[0] .= "s";
        $binding[] = $id;
    }

    $sql .= implode(",", $var) . ") AND h.setorID = ? AND h.unidadeID = ? AND h.deletedat IS NULL AND $sub4 GROUP BY h.cadastroID ";
    $binding[0] .= "isiii";
    $binding[] = $orgID;
    $binding[] = $unidade;
    $binding[] = $ano;
    $binding[] = $ano;
    $binding[] = $mes;

    $resp = sql_busca($sql, $GLOBALS['connL'], $binding);
    $resposta = array();
    foreach ($resp as $r) {
        $resposta[$r['usuario']] = $r['validade'];
    }
    return $resposta;
}

function checaValidadeHabilitacaoUsuario($hab, $mes, $ano) {
    $anoMes = "$ano$mes";
    $inicio = 1;
    $fim = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
    $erroHab = array();
    if ($hab == null) {//habilitação vencida no mês todo
        $erroHab = range($inicio, $fim);
    } else {
        $habEx = explode("-", $hab);
        $anoMesHab = $habEx[0] . $habEx[1];
        if ($anoMesHab < $anoMes) {
            $erroHab = range($inicio, $fim);
        } else if ($anoMes == $anoMesHab) {
            $inicio = $habEx[2] + 1;
            $inicio = $inicio > $fim ? 1 : $inicio;
            $erroHab = range($inicio, $fim);
        }
    }
    return $erroHab;
}

function checaFolgasConsecultivasGeral($servicosOpr, $mes, $ano, $maxFolgasConsecutivas, $afastamentos, $erroHab) {

    $servicos = array();
    foreach ($servicosOpr as $dia => $s) {
        if (array_key_exists('operacionais', $s) || array_key_exists('risaer', $s)) {
            $servicos[] = $dia;
        }
    }

    $errosfolgas = array();
    $servicosOrdenados = array();
    foreach ($servicos as $s) {
        $servicosOrdenados[] = $s;
    }
    //adiciono os afastamentos
    foreach ($afastamentos as $af) {
        $servicosOrdenados[] = $af;
    }
    //adiciono os dia de habilitacao vencida
    foreach ($erroHab as $e) {
        $servicosOrdenados[] = $e;
    }

    //ordeno os dias e deixo apenas valores unicos
    $servicosOrdenados = array_unique($servicosOrdenados);
    sort($servicosOrdenados);

    $qtdDiasMes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

    $k = 1;
    $lim = $qtdDiasMes;

    while ($k <= $lim) {
        $qtd = 0;
        while ($k <= $qtdDiasMes && !in_array($k, $servicosOrdenados)) {
            $qtd++;
            $k++;
        }
        if ($qtd > $maxFolgasConsecutivas) {
            array_splice($errosfolgas, sizeof($errosfolgas), 0, range($k - $qtd, $k - 1));
        }
        $k++;
    }
    return array('errosfolgas' => $errosfolgas, 'servicosOrdenados' => $servicosOrdenados);
}

///////////////////////////////////////////////////////////////////////////////////////////////
function checaCombinacoesTurnos($verificarCombinacaoTurnos, $combinacoesTurnos) {
    if (sizeof($verificarCombinacaoTurnos) <= 1) {
        $erroCombinacaoTurnos = false;
    } else {
        $errosDeCombinacoesPossiveis = array();
        foreach ($combinacoesTurnos as $ct) {
            $erroCombinacaoTurnos = true;

            if (sizeof($verificarCombinacaoTurnos) == 2) {
                if (
                        ($verificarCombinacaoTurnos[0] == $ct[0] && $verificarCombinacaoTurnos[1] == $ct[1]) ||
                        ($verificarCombinacaoTurnos[0] == $ct[1] && $verificarCombinacaoTurnos[1] == $ct[0])
                ) {
                    $erroCombinacaoTurnos = false;
                }
            }

            $errosDeCombinacoesPossiveis[] = $erroCombinacaoTurnos;
        }

        $erroCombinacaoTurnos = !in_array(false, $errosDeCombinacoesPossiveis);
    }
    return $erroCombinacaoTurnos;
    //
}

function listaEscalas($grupo) {
    $sql = "SELECT * "; //deixe sempre  um espaco no final
    $sql .= "FROM escalas "; //deixe sempre  um espaco no final
    $sql .= "WHERE grupo_escalas = ? "; //deixe sempre  um espaco no final;
    $binding = array('i', $grupo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
    //
}

function informacoesEscala($id, $tipo, $grupo) {
    $sql = "SELECT es.id, es.nome, es.legenda, es.grupo_escalas, es.ch_maxima, es.ch_minima, es.qtd_svc, es.soma_geral, pe.publicada, "
            . "rc.turno1, "
            . "rc.turno2, "
            . "tu1.legenda as leg_turno1, "
            . "tu2.legenda as leg_turno2 "
            . " ";
    $sql .= "FROM escalas es ";
    $sql .= "LEFT JOIN publicacao_escala as pe ";
    $sql .= "ON pe.escala = es.id AND pe.tipo = ? "
            . "LEFT JOIN restricoes_combinacoes_turno as rc "
            . "ON rc.grupo_escalas = ? "
            . "LEFT JOIN turnos as tu1 "
            . "ON tu1.id = rc.turno1 "
            . "LEFT JOIN turnos as tu2 "
            . "ON tu2.id = rc.turno2 ";
    $sql .= "WHERE es.id = ? ";
    $binding = array('iii', $tipo, $grupo, $id);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
    //
}

function pegarlistaCombinacoes($grupo) {
    $sql = "SELECT rc.turno1, "
            . "rc.turno2, "
            . "tu1.legenda as leg_turno1, "
            . "tu2.legenda as leg_turno2 "
            . ""
            . "FROM restricoes_combinacoes_turno as rc "
            . ""
            . "JOIN turnos as tu1 "
            . "ON tu1.id = rc.turno1 "
            . ""
            . "JOIN turnos as tu2 "
            . "ON tu2.id = rc.turno2 "
            . ""
            . "WHERE rc.grupo_escalas = ? ";
    $binding = array('i', $grupo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
    //
}

function pegarDadosTipoEscala($grupo, $tipo) {
    $sql = "SELECT id, trocas_liberadas "
            . "FROM tipo_escala "
            . "WHERE grupo_escalas = ? AND tipo = ? ";
    $binding = array('ii', $grupo, $tipo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
    //
}

function pegarDiasAfastamentosMes($afastOpr, $ano, $mes) {
    $anoMes = "$ano$mes";
    $afastMes = array();
    foreach ($afastOpr as $af) {
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
    return $afastMes;
    //
}

function pegarDadosGrupo($grupo) {
    $sql = "SELECT * ";
    $sql .= "FROM grupos_escalas ";
    $sql .= "WHERE id = ? ";
    $binding = array('i', $grupo);
    $r = sql_busca($sql, $GLOBALS['conn'], $binding);
    return $r[0];
    //
}

function pegarIdTipoEscala($grupo, $tipo) {
    $sql = "SELECT id "
            . "FROM tipo_escala "
            . "WHERE grupo_escalas = ? AND tipo = ? ";
    $binding = array('ii', $grupo, $tipo);
    return sql_busca($sql, $GLOBALS['conn'], $binding);
    //
}
