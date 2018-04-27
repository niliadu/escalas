<?php
$get = filter_input_array(INPUT_GET,FILTER_DEFAULT);
$m = $get['modulo'];
$func = $get['func'];
$org = $get['orgao'];
$id = $get['id'];
$uni = $get['unidade'];
$uni_nome = $get['nome_unidade'];
session_start();
$_SESSION['modulo'] = $m;
$_SESSION['funcao_usu'] = $func;
$_SESSION['orgao_usu_nome'] = $org;
$_SESSION['orgao_usu_id'] = $id;
$_SESSION['unidade_usu_id'] = $uni;
$_SESSION['unidade_usu_nome'] = $uni_nome;
header("Location: ".$_SESSION['raiz_html']."modulos/$m/");


