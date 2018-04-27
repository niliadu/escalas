<?php
//lista tipos de escalas
?>

<div class="container">
    <h3>Turnos</h3>
    <h5>Gerenciamento dos Turnos</h5>
    <br>
    <div class="row">
        <div class="col-xs-12">
            <h5>
                <button class="btn btn-success btn-xs" onclick="adicionarTurn()"><!-- data-toggle="modal" data-target="#modal_inserir_grupo">-->
                    Adicionar &nbsp;<span class=" glyphicon glyphicon-plus" ></span>
                </button>
            </h5>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-xs-6">
            <div class="panel-group" id="lista_turnos">
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
                    <?php foreach ($turnos as $t) { ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="row">
                                    <a class="panel-title collapsed" data-toggle="collapse" data-parent="#lista_turnos" href="#turno_<?php echo $t['id']; ?>">
                                        <div class="col-xs-12">                    
                                            <div class=" col-xs-3" align= "center">
                                                <?php echo $t['legenda']; ?>
                                            </div>
                                            <div class=" col-xs-6" align= "center">
                                                <?php echo $t['nome']; ?>
                                            </div>
                                            <div class=" col-xs-3" align= "right">
                                                <div class=" col-xs-2" align= "right">
                                                    <button class="btn btn-warning btn-xs" onclick="edtTurn('<?php echo $t['id']; ?>')">
                                                        <span class="glyphicon glyphicon-pencil" ></span>
                                                    </button>
                                                </div>
                                                <div class=" col-xs-2" align= "right">
                                                    <button class="btn btn-danger btn-xs" onclick="delTurn('<?php echo $t['id']; ?>', '<?php echo $t['legenda']; ?>')">
                                                        <span class="glyphicon glyphicon-remove" ></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div id="turno_<?php echo $t['id']; ?>" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <div class="col-xs-12">
                                        <table class="table">
                                            <tbody id="dados_turno_<?php echo $t['id']; ?>">
                                                <tr>
                                                    <td>Início:</td><td><?php echo $t['inicio']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Término:</td><td><?php echo $t['termino']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Etapa cheia:</td><td><?php echo $t['etapa_full'] ? "SIM" : "NÃO"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Período:</td>
                                                    <td>
                                                        <?php
                                                        switch ($t['periodo']) {
                                                            case 0:
                                                                echo "Não Definido";
                                                                break;
                                                            case 1:
                                                                echo "Diurno";
                                                                break;
                                                            case 2:
                                                                echo "Noturno";
                                                                break;
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Pós-noturno:</td><td><?php echo $t['pos_noturno'] ? "SIM" : "NÃO"; ?></td>
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