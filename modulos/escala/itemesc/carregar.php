
<?php

$temModulo = true;
session_start();
include $_SESSION['raiz'] . 'funcoes.php';
////////////////////////////////////////////////////////////////////////////////////////
$item = $post['item'];

if ($item == 'tipos_escala') {
    include 'htmls/lista_tipos.php';
} else if ($item == 'escalas') {
    $grupo = $post['grupo'];

    $escalas = listaEscalas($grupo);

    include './htmls/lista_escalas.php';
} else if ($item == 'item') {
    $escalas = $post['escalas'];
    $tipo = $post['tipo'];



    $svcs = pegarservicosOperadoresNasEscalas($escalas, $tipo);
    $usuarios = array_unique(array_column($svcs, 'usuario'));
    $nomesUsu = pegarNomeCompletoOperadores($usuarios);

    foreach ($svcs as $svc) {
        $dadosUsu[$svc['usuario']]['nome'] = $nomesUsu[$svc['usuario']];
        $dadosUsu[$svc['usuario']]['funcao'] = $svc['funcao'];
        $dadosUsu[$svc['usuario']]['mes'] = $svc['mes'];
        $dadosUsu[$svc['usuario']]['ano'] = $svc['ano'];
        $dadosUsu[$svc['usuario']]['turnos'][$svc['turno']]['nome'] = $svc['t_nome'];
        $dadosUsu[$svc['usuario']]['turnos'][$svc['turno']]['inicio'] = $svc['t_ini'];
        $dadosUsu[$svc['usuario']]['turnos'][$svc['turno']]['termino'] = $svc['t_term'];
        $dadosUsu[$svc['usuario']]['turnos'][$svc['turno']]['dias'][] = $svc['dia'];
    }
    
}

////////////////////////////////////////////////////////////////////////////////////////////
include $sessao['raiz'] . 'closeConn.php';
///////////////////////////////////////////////////////////////////////////////////////////////
?>