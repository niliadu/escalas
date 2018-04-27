<?php

//home
$temModulo = false;
session_start();

include $_SESSION['raiz'] . "funcoes.php";
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Órgãos</title>
        <link rel="shortcut icon" href="<?php echo $sessao['raiz_html']; ?>imagens/c3icon.png">
        <?php
        include $sessao['raiz'] . 'scripts_css.php';
        include $sessao['raiz'] . 'cabecalho.php';
        ?>
    </head>
    <body>
        <div class="container">
            <div id='orgaos' class="row" align="center"></div>
        </div>
    </body>
</html>
<script>
    ajax.sync.html({
        dados: {item: 'funcoes'},
        endereco: 'carregar.php',
        sucesso: function (dados) {
            $("#orgaos").html(dados);
        }
    });
</script>