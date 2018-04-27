<?php
//lista restricoes
?>

<div class="container">
    <h3>Restrições</h3>
    <h5>Gerenciamento das Restrições</h5>
    <br>
    <div class="row">
        <div class="col-xs-8">
            <div class="panel-group" id="lista_turnos">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class=" col-xs-5" align= "center">
                                    <strong>Folgas Consecutivas</strong>
                                </div>
                                <div class=" col-xs-5" align= "center">
                                    <strong>Máximo de Trocas</strong>
                                </div>
                                <div class=" col-xs-2" align= "right"> 
                                    <div class=" col-xs-2" align= "right"></div>
                                    <div class=" col-xs-2" align= "right"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-12">                    
                                    <div class=" col-xs-5" align= "center">
                                        <?php echo $restricoes['folgas'] == 11 ? "Não Aplicável" : $restricoes['folgas']; ?>
                                    </div>
                                    <div class=" col-xs-5" align= "center">
                                        <?php echo $restricoes['trocas'] == 11 ? "Não Aplicável" : $restricoes['trocas']; ?>
                                    </div>
                                    <div class=" col-xs-2" align= "right">
                                        <div class=" col-xs-2" align= "right">
                                            <button class="btn btn-warning btn-xs" onclick="edtRest()">
                                                <span class="glyphicon glyphicon-pencil" ></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <h5>
                <button class="btn btn-success btn-xs" onclick="adicionarComb()">
                    Adicionar &nbsp;<span class=" glyphicon glyphicon-plus" ></span>
                </button>
            </h5>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-4">
            <div class="panel-group">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class=" col-xs-8" align= "center">
                                    <strong>Combições </strong>
                                </div>
                                <div class=" col-xs-4" align= "right"> 
                                    <div class=" col-xs-2" align= "right"></div>
                                    <div class=" col-xs-2" align= "right"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                $combinacao = $restricoes['comb'];
                foreach ($combinacao as $c) {
                    if ($c['turno1']['id'] != null && $c['turno2']['id'] != null) {
                        ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-12">                    
                                        <div class=" col-xs-8" align= "center">
                                            <?php echo $c['turno1']['leg'] . " - " . $c['turno2']['leg']; ?>
                                        </div>
                                        <div class=" col-xs-4" align= "right">
                                            <div class=" col-xs-2" align= "right">
                                                <button class="btn btn-warning btn-xs" onclick="edtComb('<?php echo $c['id']; ?>')">
                                                    <span class="glyphicon glyphicon-pencil" ></span>
                                                </button>
                                            </div>
                                            <div class=" col-xs-2" align= "right">
                                                <button class="btn btn-danger btn-xs" onclick="delComb('<?php echo $c['id']; ?>')">
                                                    <span class="glyphicon glyphicon-remove" ></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>