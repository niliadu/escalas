<div class="row col-xs-6">
    <br>
    <?php
    if (empty($trocasEfetivadas) && empty($trocasExcluidas)) {
        ?>
        <div class="alert alert-info" >
            <h5><b>Não existem trocas concluídas.</b></h5>
        </div>
        <?php
    } else if (empty($trocasEfetivadas)) {
        ?>
        <h4>Efetivadas</h4>
        <div class="alert alert-info" >
            <h5><b>Não existem trocas Efetivadas.</b></h5>
        </div>
        <?php
    } else {
        ?>
        <h4>Efetivadas</h4>
        <?php
        foreach ($trocasEfetivadas as $t) {
            $cor = $t['status'] == 6 ? "alert-danger" : 'bg-primary';
            ?>
            <div class="alert <?php echo $cor;?>" style="color:black;">
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
                            $historico = pegarHistoricoTroca($t['id']);
                            if (!empty($historico)) {
                                $cor = $t['status'] == 6 ? "panel-danger" : 'panel-primary';
                                ?>
                                <div class="panel <?php echo $cor;?>">
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
                                                        $responsavel = $h['status'] == 4 ? "Automática" : $usuarios[$h['usuario']]['pg'] . " " . $usuarios[$h['usuario']]['ng'];
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
<div class="col-xs-6">
    <br>
    <?php
    if (empty($trocasExcluidas) && !empty($trocasEfetivadas)) {
        ?>
        <h4>Excluídas</h4>
        <div class="alert alert-info" >
            <h5><b>Não existem trocas excluídas.</b></h5>
        </div>
        <?php
    } else if (!empty($trocasExcluidas)) {
        ?>
        <h4>Excluídas</h4>
        <?php
        foreach ($trocasExcluidas as $t) {
            ?>
            <div class="alert alert-warning" style="color:black;">
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
                            $historico = pegarHistoricoTroca($t['id']);
                            if (!empty($historico)) {
                                ?>
                                <div class="panel panel-warning">
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
                                                        }
                                                        $dia = date("d-m-Y", strtotime($h['data']));
                                                        $hora = date("H:i", strtotime($h['data']));
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $sts; ?></td>
                                                            <td><?php echo $dia; ?></td>
                                                            <td><?php echo $hora; ?></td>
                                                            <td><?php echo $usuarios[$h['usuario']]['pg'] . " " . $usuarios[$h['usuario']]['ng']; ?></td>
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