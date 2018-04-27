<div class="row">   
    <div class="col-xs-12"> 
        <label>Primeiro Turno:</label> 
        <select id="turno1_restricoes" class="form-control" title="selecione o turno"> 
            <?php
            foreach ($turnos as $t) {
                echo "<option value='" . $t['id'] . "'>" . $t['legenda'] . "</option>";
            }
            ?>
        </select> 
        <br>
        <br>
        <label>Segundo Turno:</label> 
        <select id="turno2_restricoes" class="form-control" title="selecione o turno"> 
            <?php
            foreach ($turnos as $t) {
                echo "<option value='" . $t['id'] . "'>" . $t['legenda'] . "</option>";
            }
            ?>
        </select>
        <br> 
        <br> 
        <div class="col-xs-12" id="alerta-erro"></div> 
        <button class="btn btn-success pull-right" onclick="gravarCombinacao();">ADICIONAR</button> 
    </div> 
</div>