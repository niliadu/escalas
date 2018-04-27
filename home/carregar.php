<?php

$temModulo = false;
session_start();
include $_SESSION['raiz'] . 'funcoes.php';
$item = $post['item'];


if ($item == "funcoes") {

    $orgaos = array();
    $resposta = listaOrgaoFuncaoUsuario($sessao['cpf_usu']);
    $temSetor = false;
    if (sizeof($resposta) > 0) {
        foreach ($resposta as $resp) {
            if ($resp['org'] != null) {
                $temSetor = true;
                $funcOrg = explode(";", $resp['org']);
                foreach ($funcOrg as $fo) {
                    $temp = explode(",", $fo);
                    $func = $temp[0];
                    $org = pegarOrgaoId($temp[1]);                    
                    $uni = pegarUnidadeId($resp['unidadeID']);
                    $orgaos[$temp[1]]['funcao'] = $func;
                    $orgaos[$temp[1]]['nome'] = str_replace("/", "-", $org[0]['setor']);
                    $orgaos[$temp[1]]['unidade'] = $resp['unidadeID'];
                    $orgaos[$temp[1]]['nome_unidade'] = $uni[0]['unidade'];
                }
            }
        }
    }
    //verifica os órgãos que o usuário tem habilitação     
    $resposta = pegarHabilitacoesValidasUsuario($sessao['usu']);    
    if (sizeof($resposta) > 0) {
        foreach ($resposta as $resp) {
            if ($resp['setorID'] != null && !key_exists($resp['setorID'], $orgaos)) {
                $temSetor = true;
                $org = pegarOrgaoId($resp['setorID']);
                $uni = pegarUnidadeId($resp['unidadeID']);
                $orgaos[$resp['setorID']]['funcao'] = -1;
                $orgaos[$resp['setorID']]['nome'] = str_replace("/", "-", $org[0]['setor']);
                $orgaos[$resp['setorID']]['unidade'] = $resp['unidadeID'];
                $orgaos[$resp['setorID']]['nome_unidade'] = $uni[0]['unidade'];
            }
        }
    }        
    include 'htmls/orgao_funcao.php';    
}

include $sessao['raiz'] . "closeConn.php";
