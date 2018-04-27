<?php

$temModulo = true;
session_start();
include $_SESSION['raiz'] . "funcoes.php";
$item = $post['item'];

if ($item == 'par') {

    $mes = $post['mes'];
    $ano = $post['ano'];
    $orgao = $post['orgao'];
    $unidade = $post['unidade'];
//
//    //verifica se ja existe o parametro deste mês
    $resposta = checarGrupoEscala($mes, $ano, $orgao, $unidade);
    if (sizeof($resposta) > 0) {
        $resposta = array("existe" => true, 'grupo' => $resposta[0]['id']);
    } else {
        $resposta = array("existe" => false);
    }
    echo json_encode($resposta);
} else if ($item == "add_turno") {

    $nome = mb_strtoupper($post['nome'], "UTF-8");
    $legenda = mb_strtoupper($post['legenda'], "UTF-8");
    $grupo = $post['grupo'];

    $erro = false;
    $textoErro = "O turno não foi salvo.<br><br>Os Seguintes erros foram encontrados:<br>";

    $resposta = buscarTurno('nome', $nome, $grupo);
    if (sizeof($resposta) > 0) {
        $erro = true;
        $textoErro .= "&nbsp;&nbsp;- Já existe um turno com este nome;<br>";
    }

    $resposta = buscarRISAER('nome', $nome, $grupo);
    if (sizeof($resposta) > 0) {
        $erro = true;
        $textoErro .= "&nbsp;&nbsp;- Já existe um turno com este nome;<br>";
    }

    $resposta = buscarTurno('legenda', $legenda, $grupo);

    if (sizeof($resposta) > 0) {
        $erro = true;
        $textoErro .= "&nbsp;&nbsp;- Já existe um turno com esta legenda;<br>";
    }

    $resposta = buscarRISAER('legenda', $legenda, $grupo);

    if (sizeof($resposta) > 0) {
        $erro = true;
        $textoErro .= "&nbsp;&nbsp;- Já existe um turno com esta legenda;<br>";
    }

    $resposta['existe'] = $erro;
    $resposta['texto'] = $textoErro;

    echo json_encode($resposta);
} else if ($item == "alterar_turno") {

    $nome = mb_strtoupper($post['nome'], "UTF-8");
    $legenda = mb_strtoupper($post['legenda'], "UTF-8");
    $grupo = $post['grupo'];
    $id = $post['id'];

    $erro = false;
    $textoErro = "O turno não foi salvo.<br><br>Os Seguintes erros foram encontrados:<br>";

    //checar se o nome do turno já existe
    $resposta = buscarTurnoDiferente('nome', $nome, $grupo, $id);
    if (sizeof($resposta) > 0) {
        $erro = true;
        $textoErro .= "&nbsp;&nbsp;- Já existe um turno com este nome;<br>";
    }

    //checar se o nome do turno já existe
    $resposta = buscarRISAER('nome', $nome, $grupo, $id);
    if (sizeof($resposta) > 0) {
        $erro = true;
        $textoErro .= "&nbsp;&nbsp;- Já existe um serviço com este nome;<br>";
    }

    //checar se o nome do turno já existe
    $resposta = buscarTurnoDiferente('legenda', $legenda, $grupo, $id);
    if (sizeof($resposta) > 0) {
        $erro = true;
        $textoErro .= "&nbsp;&nbsp;- Já existe um turno com esta legenda;<br>";
    }

    //checar se o nome do turno já existe
    $resposta = buscarRISAER('legenda', $legenda, $grupo, $id);
    if (sizeof($resposta) > 0) {
        $erro = true;
        $textoErro .= "&nbsp;&nbsp;- Já existe um serviço com esta leganda;<br>";
    }

    $resposta['existe'] = $erro;
    $resposta['texto'] = $textoErro;

    echo json_encode($resposta);
} else if ($item == 'remover_turno') {
    $turno = $post['id'];
    $escalasComTurno = buscarEscalasComTurno($turno);
    $erro = false;
    foreach ($escalasComTurno as $esc) {
        $resposta = qtdTurnos($esc['escala']);

        $qtd = $resposta[0]['qtd'];
        if ($qtd < 2) {
            $erro = true;
        }
    }

    $resposta['existe'] = $erro;
    $resposta['texto'] = "O turno não poderá ser deletado, pois ele é único em uma escala existente!";
    echo json_encode($resposta);
} else if ($item == "add_escala") {

    $nome = mb_strtoupper($post['nome'], "UTF-8");
    $legenda = mb_strtoupper($post['legenda'], "UTF-8");
    $grupo = $post['grupo'];

    $erro = false;
    $textoErro = "A escala não foi salva.<br><br>Os Seguintes erros foram encontrados:<br>";

    $resposta = buscarEscala('nome', $nome, $grupo);
    if (sizeof($resposta) > 0) {
        $erro = true;
        $textoErro .= "&nbsp;&nbsp;- Já existe uma escala com este nome;<br>";
    }

    //checar se a legenda da escala já existe

    $resposta = buscarEscala('legenda', $legenda, $grupo);
    if (sizeof($resposta) > 0) {
        $erro = true;
        $textoErro .= "&nbsp;&nbsp;- Já existe uma escala com esta legenda;<br>";
    }

    $resposta['existe'] = $erro;
    $resposta['texto'] = $textoErro;

    echo json_encode($resposta);
} else if ($item == "alterar_escala") {

    $nome = mb_strtoupper($post['nome'], "UTF-8");
    $legenda = mb_strtoupper($post['legenda'], "UTF-8");
    $grupo = $post['grupo'];
    $id = $post['id'];

    $erro = false;
    $textoErro = "A escala não foi salva.<br><br>Os Seguintes erros foram encontrados:<br>";

    //checar se o nome da escala já existe
    $resposta = buscarEscalaDiferente('nome', $nome, $grupo, $id);
    if (sizeof($resposta) > 0) {
        $erro = true;
        $textoErro .= "&nbsp;&nbsp;- Já existe uma escala com este nome;<br>";
    }

    //checar se a legenda da escala já existe
    $resposta = buscarEscalaDiferente('legenda', $legenda, $grupo, $id);
    if (sizeof($resposta) > 0) {
        $erro = true;
        $textoErro .= "&nbsp;&nbsp;- Já existe uma escala com esta legenda;<br>";
    }

    $resposta['existe'] = $erro;
    $resposta['texto'] = $textoErro;

    echo json_encode($resposta);
} else if ($item == 'add_efetivo') {
    $usuario = $post['usuario'];
    $legenda = mb_strtoupper($post['legenda'], "UTF-8");
    $grupo = $post['grupo'];

    $erro = false;
    $textoErro = "O efetivo não foi salvo.<br><br>Os Seguintes erros foram encontrados:<br>";
    //checar se o usuario já está inserido no efetivo
    $resposta = checarUsuEfetivo($usuario, $grupo);

    if (sizeof($resposta) > 0) {
        $erro = true;
        $textoErro .= "&nbsp;&nbsp;- Já existe este operador no efetivo;<br>";
    }

    //checar se existe um usuario com esta legenda no efetivo
    $resposta = checarLegEfetivo($legenda, $grupo);

    if (sizeof($resposta) > 0) {
        $erro = true;
        $textoErro .= "&nbsp;&nbsp;- Esta legenda já pertence a um operador no efetivo;<br>";
    }

    $resposta['existe'] = $erro;
    $resposta['texto'] = $textoErro;

    echo json_encode($resposta);
} else if ($item == "alterar_efetivo") {

    $legenda = mb_strtoupper($post['legenda'], "UTF-8");
    $grupo = $post['grupo'];
    $id = $post['id'];
    $escalas = $post['escalas'];

    $erro = false;
    $textoErro = "O efetivo não foi modificado.<br><br>Os Seguintes erros foram encontrados:<br>";

    //checar se existe operador com legenda
    $resposta = checarLegEfetivoDiferente($legenda, $grupo, $id);
    if (sizeof($resposta) > 0) {
        $textoErro .= "&nbsp;&nbsp;- Já existe um operador com esta legenda;<br>";
        $resposta['existe'] = true;
        $resposta['texto'] = $textoErro;
        echo json_encode($resposta);
    } else {

        //checar se saiu de alguma escala
        $resposta = buscarEfetivoEscala($id);
        $aviso = false;
        $textoAviso = "As seguintes escalas serão deletadas: <br>";
        foreach ($resposta as $esc) {
            if (!in_array($esc['escala'], $escalas)) {
                $aviso = true;
                $textoAviso .= "-" . $esc['legenda'] . "<br>";
            }
        }
        $textoAviso .= "<br>Tem certeza que deseja deletar essas escalas?<br>Todos os serviços alocados para esse operador serão deletados!";
        $resposta['existe'] = false;
        $resposta['aviso'] = $aviso;
        $resposta['texto'] = $textoAviso;
    }
    echo json_encode($resposta);
} else if ($item == "add_combinacao" || $item == "alterar_combinacao") {
    $grupo = $post['grupo'];
    if ($item == "alterar_combinacao") {
        $id = $post['id'];
        $resposta = listaCombinacoesDiferente($grupo, $id);
    } else {
        $resposta = listaCombinacoes($grupo);
    }
    $combinacoes = $resposta;
    $turno1 = $post['turno1'];
    $turno2 = $post['turno2'];

    $existe = false;
    foreach ($combinacoes as $c) {
        unset($c['grupo_escalas']);
        unset($c['id']);
        if ($turno1 == $c['turno1'] && $turno2 == $c['turno2'] || $turno1 == $c['turno2'] && $turno2 == $c['turno1']) {
            $existe = true;
        }
    }
    if ($existe) {
        $resposta['existe'] = true;
        $resposta['texto'] = "Esta combinação de turnos já existe";
    }
    echo json_encode($resposta);
} else if ($item == "add_risaer") {

    $nome = mb_strtoupper($post['nome'], "UTF-8");
    $legenda = mb_strtoupper($post['legenda'], "UTF-8");
    $grupo = $post['grupo'];

    $erro = false;
    $textoErro = "O serviço não foi salvo.<br><br>Os Seguintes erros foram encontrados:<br>";

    $resposta = buscarTurno('nome', $nome, $grupo);


    if (sizeof($resposta) > 0) {
        $erro = true;
        $textoErro .= "&nbsp;&nbsp;- Já existe um turno com este nome;<br>";
    }

    $resposta = buscarRISAER('nome', $nome, $grupo);


    if (sizeof($resposta) > 0) {
        $erro = true;
        $textoErro .= "&nbsp;&nbsp;- Já existe um serviço com este nome;<br>";
    }

    $resposta = buscarTurno('legenda', $legenda, $grupo);

    if (sizeof($resposta) > 0) {
        $erro = true;
        $textoErro .= "&nbsp;&nbsp;- Já existe um turno com esta legenda;<br>";
    }

    $resposta = buscarRISAER('legenda', $legenda, $grupo);

    if (sizeof($resposta) > 0) {
        $erro = true;
        $textoErro .= "&nbsp;&nbsp;- Já existe um serviço com esta legenda;<br>";
    }

    $resposta['existe'] = $erro;
    $resposta['texto'] = $textoErro;

    echo json_encode($resposta);
} else if ($item == "alterar_risaer") {

    $nome = mb_strtoupper($post['nome'], "UTF-8");
    $legenda = mb_strtoupper($post['legenda'], "UTF-8");
    $grupo = $post['grupo'];
    $id = $post['id'];

    $erro = false;
    $textoErro = "O serviço não foi salvo.<br><br>Os Seguintes erros foram encontrados:<br>";

    $resposta = buscarRISAERDiferente('nome', $nome, $grupo, $id);
    if (sizeof($resposta) > 0) {
        $erro = true;
        $textoErro .= "&nbsp;&nbsp;- Já existe um serviço com este nome;<br>";
    }
    $resposta = buscarTurno('nome', $nome, $grupo);

    if (sizeof($resposta) > 0) {
        $erro = true;
        $textoErro .= "&nbsp;&nbsp;- Já existe um turno com este nome;<br>";
    }

    $resposta = buscarRISAERDiferente('legenda', $legenda, $grupo, $id);
    if (sizeof($resposta) > 0) {
        $erro = true;
        $textoErro .= "&nbsp;&nbsp;- Já existe um servico com esta legenda;<br>";
    }
    $resposta = buscarTurno('legenda', $legenda, $grupo);

    if (sizeof($resposta) > 0) {
        $erro = true;
        $textoErro .= "&nbsp;&nbsp;- Já existe um turno com esta legenda;<br>";
    }

    $resposta['existe'] = $erro;
    $resposta['texto'] = $textoErro;

    echo json_encode($resposta);
} else if ($item == 'limpar') {
    $grupo = $post['grupo'];
    $tiposEscala = pegarTiposEscala($grupo);
    $bloquear = false;

    if (sizeof($tiposEscala) > 0) {
        $tiposEscala = array_column($tiposEscala, 'tipo');
        $maiorTipo = max($tiposEscala);
        if ($maiorTipo > 1) {
            $bloquear = true;
        }
    }
    $resposta['bloquear'] = $bloquear;
    echo json_encode($resposta);
}
////////////////////////////////////////////////////////////////////////////////////////////
include $sessao['raiz'] . 'closeConn.php';
///////////////////////////////////////////////////////////////////////////////////////////////
?>