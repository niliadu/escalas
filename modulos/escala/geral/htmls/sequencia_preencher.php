<div class="row">       
    <div class="col-xs-12">
        <a class="btn pull-right" onclick="abrirAjuda('preencher');" title="ajuda">
            <span class=" glyphicon glyphicon-question-sign" aria-hidden="true"></span>
        </a>
        <label for='opcao'>Preencher:</label>
        <select id="opcao">
            <option value="todas">Todas as Escalas</option>
            <option value="por_escala">Por Escala</option>
        </select>
    </div>
    <div id="geral" class="col-xs-12">
        <div class="row col-xs-12">
            <h5>
                <button class="btn btn-success btn-xs" onclick="adicionarTurnoSequencia('geral')">
                    Adicionar &nbsp;<span class=" glyphicon glyphicon-plus" ></span>
                </button>            
            </h5>        
        </div>    
        <label for="#selects-turnos-geral">Turnos:</label> 
        <div id="selects-turnos-geral" class="row col-md-12"></div>
        <br> 
        <div class="col-md-12" > 
            <br> 
            <button class="btn btn-success pull-right" style="margin-left: 10px" onclick="preencherEscalaSequencia('geral');">PREENCHER</button>         
        </div> 
    </div>
    
    
    
    <div id="porEscala" class="col-sm-12" style="display: none;">
        <div class="row">
            <div class="col-sm-12">
                <br>
                <div class="tabbable" id="tabs-seqEscalas">
                    <ul class="nav nav-tabs">
                        <?php
                        foreach ($escalas as $i => $es) {
                            $active = ($i == 0) ? "class='active'" : "";
                            echo "<li " . $active . "> "
                            . "<a href='#panel-" . $es['id'] . "' data-toggle='tab'>" . $es['nome'] . "</a>"
                            . "</li>";
                        }
                        ?>                        
                    </ul>
                    <div class="tab-content">
                        <?php
                        $escLegendas = "";
                        foreach ($escalas as $i => $es) {
                            $escLegendas = ($i == 0) ? $es['id'] : $escLegendas . ',' . $es['id'];
                            $active = ($i == 0) ? "active" : "";
                            ?>
                            <div class='tab-pane <?php echo $active; ?>' id='panel-<?php echo $es['id']; ?>'>
                                <div class='row col-sm-12'>
                                    <h5>
                                        <button class='btn btn-success btn-xs' onclick='adicionarTurnoSequencia("<?php echo $es['id']; ?>")'>
                                            Adicionar &nbsp;<span class=' glyphicon glyphicon-plus' ></span>
                                        </button> 
                                    </h5>        
                                </div>
                                <label>Turnos:</label> 
                                <div id="selects-turnos-<?php echo $es['id']; ?>" class="row col-md-12"></div>                        
                            </div>
                            <?php
                        }
                        ?>    
                    </div>
                    <br> 
                    <br>                 
                    <div class="col-md-12" > 
                        <br> 
                        <button class="btn btn-success pull-right" style="margin-left: 10px" onclick="preencherEscalaSequencia('<?php echo $escLegendas; ?>');">PREENCHER</button>         
                    </div> 
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12" id="alerta-erro"></div>     
</div> 
