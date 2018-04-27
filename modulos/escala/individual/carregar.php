
<?php

$temModulo = true;
session_start();
include $_SESSION['raiz'] . 'funcoes.php';
////////////////////////////////////////////////////////////////////////////////////////
$item = $post['item'];

if ($item == 'tipos_escala') {
    $tipos = $post['tipos'];
    //escala definitiva
    if (is_array($tipos) && in_array(4, $tipos)) {
        $tiposNomes = array(2 => "PRÉVIA", 4 => "DEFINITIVA");
    }
    //escala corrente
    else if (is_array($tipos) && in_array(3, $tipos)) {
        $tiposNomes = array(2 => "PRÉVIA", 3 => "CORRENTE");
    }
    //escala prevista
    else if (is_array($tipos) && in_array(2, $tipos)) {
        $tiposNomes = array(2 => "PRÉVIA");
    }
    include 'htmls/lista_tipos.php';
} else if ($item == 'lista_operadores') {

    $resposta = pegarListaUsuariosHabilitados();
    $operadores = array();
    foreach ($resposta as $r) {
        $primeiro = explode(" ", $r['ng'])[0];
        $r['ng'] = $r['pg'] == 'CV' ? $primeiro : $r['ng'];
        $operadores[$r['ordem']][$r['usuario_id']] = $r;
    }
    ksort($operadores);

    include './htmls/lista_operadores.php';
} else if ($item == 'escala') {
    $grupo = $post['grupo'];
    $tipo = $post['tipo'];
    $usuario = $post['usuario'];
    $nomeOperador = $post['nomeOperador'];
    $nomeTipo = $post['nomeTipo'];
    $botao = $post['botao'];

    $r = mesAnoGrupo($grupo);
    $mes = $r[0]['mes'];
    $ano = $r[0]['ano'];
    $afastamentos = pegarAfastamentosNoMes(array($usuario), $mes, $ano);

    $ep = pegarEscalasPublicadasGeral($grupo, $tipo);
    $escalasPublicadas = array();
    foreach ($ep as $e) {
        $escalasPublicadas[] = $e['escala'];
    }

    $resposta = pegarServicosOperador($grupo, $usuario, $tipo);

    $orgao = $sessao['orgao_usu_id'];
    $servicos = array();

    foreach ($resposta as $r) {
        $resp = pegarOrgaoId($r['orgao']);
        $on = str_replace("/", "-", $resp[0]['setor']);
        $textoOrgao = $orgao == $r['orgao'] ? "" : "[$on]";

        if (in_array($r['escala_id'], $escalasPublicadas)) {

            $r['texto'] != "" ? $servicos[$r['dia']]['textos'][] = "<b><i>" . $r['texto'] . "</i></b>" : null;

            $r['turno'] != null ? $servicos[$r['dia']]['turnos'][] = "<b>" . $r['turno'] . "</b>(" . $r['escala'] . "$textoOrgao)" : null;
        }
    }

    $resposta = mesAnoGrupo($grupo);
    $mes = $resposta[0]['mes'];
    $ano = $resposta[0]['ano'];


    include 'htmls/escala.php';
} else if ($item == 'informacoes') {
    $grupo = $post['grupo'];
    $tipo = $post['tipo'];
    $usuario = $post['usuario'];
    $nomeOperador = $post['nomeOperador'];
    $nomeTipo = $post['nomeTipo'];

    $r = mesAnoGrupo($grupo);
    $mes = $r[0]['mes'];
    $ano = $r[0]['ano'];
    $afastamentos = pegarAfastamentosNoMes(array($usuario), $mes, $ano);

    $inspecaoSaude = pegarInspencaoDeSaudeValidaUsuario($usuario, $mes, $ano);
    $situacaoInspecao = 1;
    if (!empty($inspecaoSaude)) {
        $dataValidade = new DateTime($inspecaoSaude[0]['validade']);

        $hoje = date("Ymd");
        $hojeO = new DateTime('now');
        $dataDiff = $hojeO->diff($dataValidade);
        $diasDoVencimento = $dataDiff->format("%a");

        $validadeC = str_replace("-", "", $inspecaoSaude[0]['validade']);
        if (($validadeC - $hoje) <= 0) {

            $situacaoInspecao = 2;
        } else if ($diasDoVencimento <= 45) {

            $situacaoInspecao = 3;
        }
    }

    include 'htmls/informacoes.php';
}

function pegarInspencaoDeSaudeValidaUsuario($usuario) {
    $s1 = "SELECT MAX(s.inspecao_dt) "
            . "FROM saude_novo as s "
            . "WHERE s.cadastroID = ? ";

    $sql = "SELECT s.inspecao_dt AS realizacao, s.letra, s.validade_dt as validade "
            . "FROM saude_novo as s "
            . "WHERE s.cadastroID = ? AND s.inspecao_dt IN($s1) ";
    $binding = array('ss', $usuario, $usuario);
    return sql_busca($sql, $GLOBALS['connL'], $binding);
}

////////////////////////////////////////////////////////////////////////////////////////////
include $sessao['raiz'] . 'closeConn.php';
///////////////////////////////////////////////////////////////////////////////////////////////
?>