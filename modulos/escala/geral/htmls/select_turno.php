<div id="<?php echo "div-select-$div-$prox"; ?>" class="row col-xs-12 div-select">   
    <div class="row col-xs-6">
        <br>
        <a class="remove label label-default" onclick="delSelectTurno('<?php echo "div-select-$div-$prox"; ?>')">
            <i class="glyphicon glyphicon-remove"></i>                    
        </a>        
        <select class="form-control turSelSeq select" escleg="<?php echo $div; ?>" title="selecione o(s) turno(s)" > 
            <option value='folga'>FOLGA</option>
            <?php
            foreach ($turnosEscalas as $j => $te) {
                if ($j == $div) {
                    foreach ($te as $i => $t) {                        
                        if ($i !== 'turnos') {                            
                            if ($i !== 'combinacoes') {
                                echo "<option value='" . $t['id'] . "'>" . $t['legenda'] . "</option>";
                            } else {
                                foreach ($t as $c) {
                                    echo "<option value='" . $c['ids'] . "'>" . str_replace("-", "", $c['legendas']) . "</option>";
                                }
                            }
                        }
                    }
                }
            }
            ?>            
        </select>
    </div>
</div>