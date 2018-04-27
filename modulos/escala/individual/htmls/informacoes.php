<div class="panel-group" >
    <div class="panel panel-primary">
        <div class="panel-heading">
            <div class="row">
                <div class="col-xs-12">
                    <strong>INFORMAÇÕES</strong>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="panel panel-default">
            <div class="panel-heading" id="informacoes" style="overflow-y: scroll;">
                <div class="row">
                    <div class="col-xs-12">
                        <?php
                        if (!empty($afastamentos)) {
                            ?>
                            <div class="alert alert-info" style='color:black;'>
                                <h4>Afastamentos</h4>
                                <?php
                                foreach ($afastamentos as $a) {
                                    $ini = substr($a['inicio'], 6, 2) . "-" . substr($a['inicio'], 4, 2) . "-" . substr($a['inicio'], 0, 4);
                                    $ter = substr($a['termino'], 6, 2) . "-" . substr($a['termino'], 4, 2) . "-" . substr($a['termino'], 0, 4);
                                    $linhas[] = "<h6>" . $a['tipo'] . "<br>De $ini a $ter</h6>";
                                }
                                echo implode("<hr>", $linhas);
                                ?>
                            </div>

                            <?php
                        }

                        if ($situacaoInspecao > 1) {
                            $cor = $situacaoInspecao == 2 ? "danger" : "warning";
                            $validade = explode('-', $inspecaoSaude[0]['validade']);
                            ?>
                            <div class="alert alert-<?php echo $cor; ?>" style='color:black;'>
                                <h4>Inspeção de saúde</h4>
                                <h6>
                                    Letra: <?php echo $inspecaoSaude[0]['letra']; ?>
                                    <br>
                                    Validade: <?php echo $ini = $validade[2] . "-" . $validade[1] . "-" . $validade[0]; ?>
                                    <br>
                                    <?php
                                    switch ($situacaoInspecao) {
                                        case 2:
                                            echo "Está vencida a $diasDoVencimento dias.";
                                            break;
                                        case 3:
                                            echo "Faltam $diasDoVencimento dias para o vencimento da inspenção de saúde.<br>É necessário agendá-la!";
                                    }
                                    ?>
                                </h6>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>