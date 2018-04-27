<?php
//lista tipos de escalas
?>

<div class="container">
    <h3>RISAER</h3>
    <h5>Gerenciamento dos Serviços RISAER</h5>
    <br>
    <div class="row">
        <div class="col-xs-12">
            <h5>
                <button class="btn btn-success btn-xs" onclick="adicionarRISAER()"><!-- data-toggle="modal" data-target="#modal_inserir_grupo">-->
                    Adicionar &nbsp;<span class=" glyphicon glyphicon-plus" ></span>
                </button>
            </h5>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-xs-6">
            <div class="panel-group" id="lista_risaer">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class=" col-xs-3" align= "center">
                                    <strong>LEGENDA</strong>
                                </div>
                                <div class=" col-xs-6" align= "center">
                                    <strong>NOME</strong>
                                </div>
                                <div class=" col-xs-3" align= "right"> 
                                    <div class=" col-xs-2" align= "right"></div>
                                    <div class=" col-xs-2" align= "right"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <?php foreach ($risaer as $r) { ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="row">
                                    <a class="panel-title collapsed" data-toggle="collapse" data-parent="#lista_risaer" href="#risaer_<?php echo $r['id']; ?>">
                                        <div class="col-xs-12">                    
                                            <div class=" col-xs-3" align= "center">
                                                <?php echo $r['legenda']; ?>
                                            </div>
                                            <div class=" col-xs-6" align= "center">
                                                <?php echo $r['nome']; ?>
                                            </div>
                                            <div class=" col-xs-3" align= "right">
                                                <div class=" col-xs-2" align= "right">
                                                    <button class="btn btn-warning btn-xs" onclick="edtRISAER('<?php echo $r['id']; ?>')">
                                                        <span class="glyphicon glyphicon-pencil" ></span>
                                                    </button>
                                                </div>
                                                <div class=" col-xs-2" align= "right">
                                                    <button class="btn btn-danger btn-xs" onclick="delRISAER('<?php echo $r['id']; ?>', '<?php echo $r['legenda']; ?>')">
                                                        <span class="glyphicon glyphicon-remove" ></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div id="risaer_<?php echo $r['id']; ?>" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <div class="col-xs-12">
                                        <table class="table">
                                            <tbody id="dados_risaer_<?php echo $r['id']; ?>">
                                                <tr>
                                                    <td>Início:</td><td><?php echo $r['inicio']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Término:</td><td><?php echo $r['termino']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Serviço de 24 horas ou mais:</td><td><?php echo $r['mais_q_24h'] ? "SIM" : "NÃO"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Tipop etapa:</td><td>
                                                        <?php
                                                        switch ($r['tipo_etapa']) {
                                                            case 1:
                                                                echo "SEM ETAPA";
                                                                break;
                                                            case 2:
                                                                echo "ETAPA 1X";
                                                                break;
                                                            case 3:
                                                                echo "ETAPA 5X";
                                                                break;
                                                            case 4:
                                                                echo "ETAPA 10X";
                                                                break;
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Descanso mínimo anterior:</td><td><?php echo $t['intervalo_antes']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Descanso mínimo posterior:</td><td><?php echo $t['intervalo_depois']; ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>