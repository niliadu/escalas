<?php
$primeiroDiaDaSemana = date("w", strtotime("$ano-$mes-1"));
$diaImpressao = 1;
$diaImpressaoSvc = 1;
$qtdDiasMes = date("t", strtotime("$ano-$mes-1"));
$mesAnoNome = mb_strtoupper(strftime("%B de %Y", strtotime("$ano-$mes-1")), "UTF-8");

function servicosImpressao($dia) {

    $af = $GLOBALS['afastamentos'];
    $afastado = false;
    foreach ($af as $a) {
        $dia0 = $dia < 10 ? "0$dia" : $dia;
        $anoMesDia = $GLOBALS['ano'] . $GLOBALS['mes'] . $dia0;
        if ($a['inicio'] <= $anoMesDia && $a['termino'] >= $anoMesDia) {
            $afastado = true;
            break;
        }
    }

    if ($afastado) {
        $cel = "<b>--</b>";
    } else if (array_key_exists($dia, $GLOBALS['servicos'])) {
        $textos = array_key_exists('textos', $GLOBALS['servicos'][$dia]) ? implode("", $GLOBALS['servicos'][$dia]['textos']) : "";
        $turnos = array_key_exists('turnos', $GLOBALS['servicos'][$dia]) ? implode("<br>", $GLOBALS['servicos'][$dia]['turnos']) : "";

        $imp = array();
        $textos == "" ? null : $imp[] = $textos;
        $turnos == "" ? null : $imp[] = $turnos;
        $cel = implode("<br>", $imp);
    } else {
        $cel = "";
    }
    return $cel;
}

//print_r($servicos);
?>
<div class="row" id="escala-impressao">
    <div class="col-md-12" align='center'>
        <table class="largura bl" frame='box' id="tabela-impressao">
            <tr align='center' class="bb bl individual-cabecalho">
                <td  colspan="7"><?php echo "$nomeOperador - $mesAnoNome ($nomeTipo)" ?></td>
            </tr>
            <tr align='center' class="bb bl individual-cabecalho">
                <td>DOM</td>
                <td>SEG</td>
                <td>TER</td>
                <td>QUA</td>
                <td>QUI</td>
                <td>SEX</td>
                <td>SÁB</td>
            </tr>
            <tr align='right' >
                <?php
                for ($i = 0; $i < 7; $i++) {
                    if ($i < $primeiroDiaDaSemana) {
                        $cel = "";
                    } else {
                        $cel = $diaImpressao;
                        $diaImpressao++;
                    }
                    echo "<td>$cel</td>";
                }
                ?>
            </tr>
            <tr align='center' class="linhaServico bb">
                <?php
                for ($i = 0; $i < 7; $i++) {
                    if ($i < $primeiroDiaDaSemana) {
                        $cel = "";
                    } else {
                        $cel = servicosImpressao($diaImpressaoSvc);
                        $diaImpressaoSvc++;
                    }
                    echo "<td>$cel</td>";
                }
                ?>
            </tr>
            <tr align='right' >
                <?php
                for ($i = 0; $i < 7; $i++) {
                    if ($diaImpressao <= $qtdDiasMes) {
                        $cel = $diaImpressao;
                        $diaImpressao++;
                    } else {
                        $cel = "";
                    }
                    echo "<td>$cel</td>";
                }
                ?>
            </tr>
            <tr align='center' class="linhaServico bb">
                <?php
                for ($i = 0; $i < 7; $i++) {
                    if ($diaImpressaoSvc <= $qtdDiasMes) {
                        $cel = servicosImpressao($diaImpressaoSvc);
                        $diaImpressaoSvc++;
                    } else {
                        $cel = "";
                    }
                    echo "<td>$cel</td>";
                }
                ?>
            </tr>
            <tr align='right' >
                <?php
                for ($i = 0; $i < 7; $i++) {
                    if ($diaImpressao <= $qtdDiasMes) {
                        $cel = $diaImpressao;
                        $diaImpressao++;
                    } else {
                        $cel = "";
                    }
                    echo "<td>$cel</td>";
                }
                ?>
            </tr>
            <tr align='center' class="linhaServico bb">
                <?php
                for ($i = 0; $i < 7; $i++) {
                    if ($diaImpressaoSvc <= $qtdDiasMes) {
                        $cel = servicosImpressao($diaImpressaoSvc);
                        $diaImpressaoSvc++;
                    } else {
                        $cel = "";
                    }
                    echo "<td>$cel</td>";
                }
                ?>
            </tr>
            <tr align='right' >
                <?php
                for ($i = 0; $i < 7; $i++) {
                    if ($diaImpressao <= $qtdDiasMes) {
                        $cel = $diaImpressao;
                        $diaImpressao++;
                    } else {
                        $cel = "";
                    }
                    echo "<td>$cel</td>";
                }
                ?>
            </tr>
            <tr align='center' class="linhaServico bb">
                <?php
                for ($i = 0; $i < 7; $i++) {
                    if ($diaImpressaoSvc <= $qtdDiasMes) {
                        $cel = servicosImpressao($diaImpressaoSvc);
                        $diaImpressaoSvc++;
                    } else {
                        $cel = "";
                    }
                    echo "<td>$cel</td>";
                }
                ?>
            </tr>
            <tr align='right' >
                <?php
                for ($i = 0; $i < 7; $i++) {
                    if ($diaImpressao <= $qtdDiasMes) {
                        $cel = $diaImpressao;
                        $diaImpressao++;
                    } else {
                        $cel = "";
                    }
                    echo "<td>$cel</td>";
                }
                ?>
            </tr>
            <tr align='center' class="linhaServico bb">
                <?php
                for ($i = 0; $i < 7; $i++) {
                    if ($diaImpressaoSvc <= $qtdDiasMes) {
                        $cel = servicosImpressao($diaImpressaoSvc);
                        $diaImpressaoSvc++;
                    } else {
                        $cel = "";
                    }
                    echo "<td>$cel</td>";
                }
                ?>
            </tr>
            <?php
            if ($diaImpressao <= $qtdDiasMes) {
                ?>
                <tr align='right' >
                    <?php
                    for ($i = 0; $i < 7; $i++) {
                        if ($diaImpressao <= $qtdDiasMes) {
                            $cel = $diaImpressao;
                            $diaImpressao++;
                        } else {
                            $cel = "";
                        }
                        echo "<td>$cel</td>";
                    }
                    ?>
                </tr>
                <tr align='center' class="linhaServico bb">
                    <?php
                    for ($i = 0; $i < 7; $i++) {
                        if ($diaImpressaoSvc <= $qtdDiasMes) {
                            $cel = servicosImpressao($diaImpressaoSvc);
                            $diaImpressaoSvc++;
                        } else {
                            $cel = "";
                        }
                        echo "<td>$cel</td>";
                    }
                    ?>
                </tr>
                <?php
            }
            ?>
        </table>
    </div>
</div>
<?php
if ($botao == 'true') {
    ?>
    <div class="row">
        <br>
        <div class="col-md-6">
            <button class="btn btn-primary pull-right" onclick="impressao('folha');">Impressão Folha</button>
        </div>
        <div class="col-md-6">
            <button class="btn btn-primary pull-left" onclick="impressao('mini');">Impressão Miniatura</button>
        </div>
    </div>
    <?php
}
?>