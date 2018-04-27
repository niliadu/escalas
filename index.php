<?php

ini_set('default_charset', 'UTF-8');
error_reporting('E_ALL');
$inicio = true;
$temModulo = false;

session_start();
session_set_cookie_params(0);
$server = filter_input_array(INPUT_SERVER, FILTER_DEFAULT);
$complemento = explode("index", $server['REQUEST_URI']);

$_SESSION['raiz'] = str_replace("//", "/", $server['DOCUMENT_ROOT'] . $complemento[0]);
$_SESSION['raiz_html'] = $complemento[0];

include 'funcoes.php';

$passID = "423539fa-fda5-4562-954c5ba30da82f25";//8f29a595-b756-4ccc-a8abd8235069e932";//8f29a595-b756-4ccc-a8abd8235069e932";//"423539fa-fda5-4562-954c5ba30da82f25";//$get['passID'];//""; //filter_input(INPUT_COOKIE, "PASSID", FILTER_DEFAULT); //"8f29a595-b756-4ccc-a8abd8235069e932";

$retorno = checarPASSID($passID);

if (sizeof($retorno) > 0) {
    $r = $retorno[0];
    
    $_SESSION['usu'] = $r['cadastroID'];
    $_SESSION['pg_nome'] = $r['posto'] . " " . $r['ng'];
    $_SESSION['matricula_usu'] = $r['ORGAO_matricula'];
    $_SESSION['cpf_usu'] = $r['CPF'];
    $_SESSION['emailI_usu'] = $r['email_inst'];
    $_SESSION['emailE_usu'] = $r['email'];
    $_SESSION['modulo'] = null;
    $_SESSION['passID'] = $passID;
}else{
    unset($_SESSION['usu']);
}
header("Location: " . $_SESSION['raiz_html'] . "home/");
