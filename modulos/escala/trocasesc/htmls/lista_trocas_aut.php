<div class="row col-xs-6">
    <br>
    <?php
    if (empty($trocas)) {
        ?>
        <div class="alert alert-info" >
            <h5><b>Não existem trocas autorizadas.</b></h5>
        </div>
        <?php
    } else {
        foreach ($trocas as $t) {
            $corErro = array();

            $resp1 = analisarTroca(false, $grupo, $t['usuPE'], $t['escalaPE'], $t['diaPO'], $t['tipo'], $t['diaPE'], $t['turnoPE'], $t['turnoPO'], $t['turnoLegPE'], $t['escalaLegPE'], $t['turnoLegPO'], $t['escalaPO'], $t['usuPO']);
            $corErro[] = $resp1['corErro'];
            $textoErroPE = $resp1['textoErro'];

            $resp2 = analisarTroca(false, $grupo, $t['usuPO'], $t['escalaPO'], $t['diaPE'], $t['tipo'], $t['diaPO'], $t['turnoPO'], $t['turnoPE'], $t['turnoLegPO'], $t['escalaLegPO'], $t['turnoLegPE'], $t['escalaPE'], $t['usuPE']);
            $corErro[] = $resp2['corErro'];
            $textoErroPO = $resp2['textoErro'];

            $cor = in_array('danger', $corErro) ? 'danger' : (in_array('warning', $corErro) ? 'warning' : 'success');
//            print_r($textoErroPE);
            ?>
            <div class="alert bg-primary" style="color:black;">
                <div class="row">
                    <div class="col-xs-6">
                        <h5><b>Proponente:</b> <?php echo $usuarios[$t['usuPE']]['pg'] . " " . $usuarios[$t['usuPE']]['ng']; ?></h5>
                        <h5><b>Dia:</b> <?php echo $t['diaPE']; ?></h5>
                        <h5><b>Turno:</b> <?php echo $t['turnoLegPE']; ?></h5>
                    </div>
                    <div class="col-xs-6">
                        <h5><b>Proposto:</b> <?php echo $usuarios[$t['usuPO']]['pg'] . " " . $usuarios[$t['usuPO']]['ng']; ?></h5>
                        <h5><b>Dia:</b> <?php echo $t['diaPO']; ?></h5>
                        <h5><b>Turno:</b> <?php echo $t['turnoLegPO']; ?></h5>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="panel-group" id="panel-infos-<?php echo $t['id']; ?>">
                            <?php
                            if ($cor != 'success') {
                                ?>
                                <div class="panel panel-<?php echo $corErro[0]; ?>">
                                    <div class="panel-heading">
                                        <a class="panel-title collapsed" data-toggle="collapse" data-parent="#panel-infos-<?php echo $t['id']; ?>" href="#panel-erro-pe-<?php echo $t['id']; ?>" style="color:black;">
                                            Erros e Restrições - <?php echo $usuarios[$t['usuPE']]['pg'] . " " . $usuarios[$t['usuPE']]['ng']; ?>
                                        </a>
                                    </div>
                                    <div id="panel-erro-pe-<?php echo $t['id']; ?>" class="panel-collapse collapse">
                                        <div class="panel-body" style="color:black;">
                                            <?php
                                            foreach ($textoErroPE as $tepe) {
                                                echo "<h6>$tepe</h6>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-<?php echo $corErro[1]; ?>">
                                    <div class="panel-heading">
                                        <a class="panel-title collapsed" data-toggle="collapse" data-parent="#panel-infos-<?php echo $t['id']; ?>" href="#panel-erro-po-<?php echo $t['id']; ?>" style="color:black;">
                                            Erros e Restrições - <?php echo $usuarios[$t['usuPO']]['pg'] . " " . $usuarios[$t['usuPO']]['ng']; ?>
                                        </a>
                                    </div>
                                    <div id="panel-erro-po-<?php echo $t['id']; ?>" class="panel-collapse collapse">
                                        <div class="panel-body" style="color:black;">
                                            <?php
                                            foreach ($textoErroPO as $tepo) {
                                                echo "<h6>$tepo</h6>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            $historico = pegarHistoricoTroca($t['id']);
                            if (!empty($historico)) {
                                ?>
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <a class="panel-title collapsed" data-toggle="collapse" data-parent="#panel-infos-<?php echo $t['id']; ?>" href="#panel-historico-<?php echo $t['id']; ?>" style="color:black;">
                                            Histórico
                                        </a>
                                    </div>
                                    <div id="panel-historico-<?php echo $t['id']; ?>" class="panel-collapse collapse">
                                        <div class="panel-body" style="color:black;">
                                            <table class="table table-condensed table-striped">
                                                <thead>
                                                <th>Status</th>
                                                <th>Dia</th>
                                                <th>Hora</th>
                                                <th>Responsável</th>
                                                <th>Observações</th>
                                                </thead> 
                                                <tbody>
                                                    <?php
                                                    foreach ($historico as $h) {
                                                        switch ($h['status']) {
                                                            case 1:
                                                                $sts = "Lançada";
                                                                break;
                                                            case 2:
                                                                $sts = "Autorizada";
                                                                break;
                                                            case 3:
                                                                $sts = "Efetivada";
                                                                break;
                                                            case 4:
                                                                $sts = "Efetivada automáticamente";
                                                                break;
                                                            case 5:
                                                                $sts = "Excluída";
                                                                break;
                                                            case 6:
                                                                $sts = "Rejeitada";
                                                                break;
                                                        }
                                                        $dia = date("d-m-Y", strtotime($h['data']));
                                                        $hora = date("H:i", strtotime($h['data']));
                                                        $responsavel = $t['status'] == 4 ? "Automática" : $usuarios[$h['usuario']]['pg'] . " " . $usuarios[$h['usuario']]['ng'];
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $sts; ?></td>
                                                            <td><?php echo $dia; ?></td>
                                                            <td><?php echo $hora; ?></td>
                                                            <td><?php echo $responsavel; ?></td>
                                                            <td><?php echo $h['texto']; ?></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            if ($t['status'] < 3) {
                                ?>
                                <div class="row col-xs-12">
                                    <br>
                                    <div class="col-xs-1 pull-right">
                                        <button class="btn btn-danger" onclick="excluirTroca(<?php echo $t['id']; ?>);">
                                            <span class="glyphicon glyphicon-remove"></span>
                                        </button>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }
    ?>
</div>