<?php
//lista tipos de escalas
?>

<div class="container">
    <h3>Escalas</h3>
    <h5>Gerenciamento dos Tipos de Escalas</h5>
    <br>
    <div class="row">
        <div class="col-xs-12">
            <h5>
                <button class="btn btn-success btn-xs" onclick="adicionarEsc()"><!-- data-toggle="modal" data-target="#modal_inserir_grupo">-->
                    Adicionar &nbsp;<span class=" glyphicon glyphicon-plus" ></span>
                </button>
            </h5>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-xs-6">
            <div class="panel-group">
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
                <?php foreach ($escalas as $e) { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="row">
                                <a class="panel-title collapsed" data-toggle="collapse" data-parent="#lista_escalas" href="#escala_<?php echo $e['id']; ?>">
                                    <div class="col-xs-12">                   
                                        <div class=" col-xs-3" align= "center">
                                            <?php echo $e['legenda']; ?>
                                        </div>
                                        <div class=" col-xs-6" align= "center">
                                            <?php echo $e['nome']; ?>
                                        </div>                                        
                                        <div class=" col-xs-3" align= "right">
                                            <div class=" col-xs-2" align= "right">
                                                <button class="btn btn-warning btn-xs" onclick="edtTipoEsc('<?php echo $e['id']; ?>')">
                                                    <span class="glyphicon glyphicon-pencil" ></span>
                                                </button>
                                            </div>
                                            <div class=" col-xs-2" align= "right">
                                                <button class="btn btn-danger btn-xs" onclick="delTipoEsc('<?php echo $e['id']; ?>', '<?php echo $e['nome']; ?>')">
                                                    <span class="glyphicon glyphicon-remove" ></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div id="escala_<?php echo $e['id']; ?>" class="panel-collapse collapse">
                            <div class="panel-body">
                                <div class="col-xs-12">
                                    <table class="table">
                                        <tbody id="dados_escala_<?php echo $e['id']; ?>">
                                            <tr>
                                                <td>Turnos:</td><td><?php echo implode(" - ", $e['turnos']); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Carga Horária:</td><td><?php echo $e['ch_minima'] . "h - " . $e['ch_maxima'] . "h"; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Quantidade de Serviços:</td><td><?php echo $e['qtd_svc']; ?></td>
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