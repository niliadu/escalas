<div class="row">   
    <div class="col-xs-12"> 
        <label>Primeiro Turno:</label> 
        <select id="turno1_restricoes" class="form-control" title="selecione o turno"> 
            <?php
            foreach ($turnos as $i => $t) {
                $selected = "";
                if ($t['id'] == $combinacao['turno1']) {
                    $selected = "selected";
                }

                echo "<option value='" . $t['id'] . "' $selected>" . $t['legenda'] . "</option>";
            }
            ?>
        </select> 
        <br>
        <br>
        <label>Segundo Turno:</label> 
        <select id="turno2_restricoes" class="form-control" title="selecione o turno"> 
            <?php
            foreach ($turnos as $i => $t) {
                $selected = "";
                if ($t['id'] == $combinacao['turno2']) {
                    $selected = "selected";
                }

                echo "<option value='" . $t['id'] . "' $selected>" . $t['legenda'] . "</option>";
            }
            ?>
        </select>
        <br> 
        <br> 
        <div class="col-xs-12" id="alerta-erro"></div> 
        <button class="btn btn-success pull-right" onclick="alterarCombinacao(<?php echo $id; ?>);">MODIFICAR</button> 
    </div> 
</div>