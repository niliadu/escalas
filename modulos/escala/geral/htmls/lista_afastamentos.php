<h5><?php echo $nome; ?></h5>

<?php
foreach ($afastamentos as $a) {
    $anoIni = substr($a['inicio'], 0, 4);
    $mesIni = substr($a['inicio'], 4, 2);
    $diaIni = substr($a['inicio'], 6, 2);

    $anoTer = substr($a['termino'], 0, 4);
    $mesTer = substr($a['termino'], 4, 2);
    $diaTer = substr($a['termino'], 6, 2);
    ?>
    <div class="alert bg-primary">
        <h6>
            <b>De:</b> <?php echo "$diaIni-$mesIni-$anoIni"; ?> <b>à</b>  <?php echo "$diaTer-$mesTer-$anoTer"; ?>
            <br>
            <b>Tipo:</b> <?php echo $a['tipo']; ?>
        </h6>
    </div>
    <?php
}
?>

