<?php
//escala home
$temModulo = true;
session_start();
include $_SESSION['raiz'] . "funcoes.php";
acesso("escala", -1);
header("Location: " . $sessao['raiz_html'] . "modulos/escala/individual/");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Escalas</title>
        <?php
        $itemMenu = -1;
        include $sessao['raiz'] . 'scripts_css.php';
        include $sessao['raiz'] . 'cabecalho.php';
        ?>
    </head>
    <body>
        <div class="container">

        </div>
    </body>
</html>