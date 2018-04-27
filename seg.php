<?php

if (!$temModulo) {
    $sessao['modulo'] = null;
    $sessao['funcao_usu'] = null;
} else {
    $func = $sessao['funcao_usu'];
}
if (!isset($inicio) && !isset($sessao['usu']) && !isset($servico)) {
    unset($_SESSION);
    unset($_COOKIE['PASSID']);
    session_destroy();
    header('location: http://servicos.decea.intraer/lpna/gerencial');
    die();
}

function acesso($mod, $tipoPag) {
    if ($GLOBALS['sessao']['modulo'] == $mod) {
        $f = $GLOBALS['sessao']['funcao_usu'];
        if ($tipoPag > $f || $f > 1) {
            header("Location: " . $sessao['raiz_html'] . "modulos/$mod/");
        }
    } else {
        header('Location: ' . $sessao['raiz_html'] . 'home/');
    }
}
