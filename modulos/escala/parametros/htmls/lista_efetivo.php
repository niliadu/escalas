<?php
//lista tipos de escalas
?>

<div class="container">
    <h3>Efetivo</h3>
    <h5>Gerenciamento do Efetivo</h5>
    <br>
    <div class="row">
        <div class="col-xs-12">
            <h5>
                <button class="btn btn-success btn-xs" onclick="adicionarEfetivo()"><!-- data-toggle="modal" data-target="#modal_inserir_grupo">-->
                    Adicionar &nbsp;<span class=" glyphicon glyphicon-plus" ></span>
                </button>
            </h5>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-xs-12">
            <div class="panel-group">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class=" col-xs-2" align= "center">
                                    <strong>LEGENDA</strong>
                                </div>
                                <div class=" col-xs-3" align= "left">
                                    <strong>OPERADOR</strong>
                                </div>
                                <div class=" col-xs-2" align= "center">
                                    <strong>ESCALAS</strong>
                                </div>
                                <div class=" col-xs-2" align= "center">
                                    <strong>FUNÇÃO</strong>
                                </div>    
                                <div class=" col-xs-2" align= "center">
                                    <strong>MANUTENÇÃO</strong>
                                </div>
                                <div class=" col-xs-1" align= "right"> 
                                    <div class=" col-xs-2" align= "right"></div>
                                    <div class=" col-xs-2" align= "right"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <?php
                    foreach ($efetivo as $id => $e) {
                        $cor = "default";
                        $title = array();
                        if (!array_key_exists($e['operador'], $usuariosHabilitados)) {
                            $cor = 'danger';
                            $title[] = "OPERADOR(A) NÃO POSSUI HABILITAÇÃO VÁLIDA NO ORGÃO.";
                        }
                        if ($e['escalas'][0] == "") {
                            $cor = "warning";
                            $title[] = "OPERADOR(A) NÃO ESTÁ ALOCADO(A) EM NENHUMA ESCALA.";
                        }
                        $title = implode("&nbsp;&nbsp;||&nbsp;&nbsp;", $title);
                        ?>

                        <div class="panel panel-<?php echo $cor; ?>" data-toggle="tooltip" title="<?php echo $title; ?>">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-12">                    
                                        <div class=" col-xs-2" align= "center">
                                            <?php echo $e['legenda']; ?>
                                        </div>
                                        <div class=" col-xs-3" align= "left">
                                            <?php echo $nome = $usuariosSistema[$e['operador']]['pg'] . " " . $usuariosSistema[$e['operador']]['ng']; ?>
                                        </div>
                                        <div class=" col-xs-2" align= "center">
                                            <?php echo implode(" - ", $e['escalas']); ?>
                                        </div>
                                        <div class=" col-xs-2" align= "center">
                                            <?php echo $e['funcao']; ?>
                                        </div> 
                                        <div class=" col-xs-2" align= "center">
                                            <?php echo $e['manutencao'] ? "SIM" : "NÃO";?>
                                        </div> 
                                        <div class=" col-xs-1" align= "right">
                                            <div class=" col-xs-2" align= "right">
                                                <button class="btn btn-warning btn-xs" onclick="edtEfetivo(<?php echo $id; ?>)">
                                                    <span class="glyphicon glyphicon-pencil" ></span>
                                                </button>
                                            </div>
                                            <div class=" col-xs-2" align= "right">
                                                <button class="btn btn-danger btn-xs" onclick="delEfetivo(<?php echo $id; ?>, '<?php echo $nome; ?>')">
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
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>