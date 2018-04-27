<div class="row">   
    <div class="col-xs-12">  
        <label>Nome da Escala:</label> 
        <input type="text" class="form-control" id="nome-esc" maxlength="45" value="<?php echo $escala['nome']; ?>"> 
        <br> 
        <label>Legenda:</label> 
        <input type="text" class="form-control" id="legenda-esc" maxlength="45" value="<?php echo $escala['legenda']; ?>"> 
        <br> 
        <label>Turnos:</label> 
        <select id="turno-esc" class="form-control" title="selecione os turnos" multiple> 
            <?php
            foreach ($turnos as $t) {
                $selected = '';
                if (in_array($t['id'], $escala['turnosId'])) {
                    $selected = 'selected';
                }
                echo "<option value='" . $t['id'] . "' $selected> " . $t['legenda'] . "</option>";
            }
            ?>
        </select> 
        <br> 
        <br> 
        <br> 
        <label>Quantidade de Serviços:</label>&nbsp&nbsp 
        <input id="svc" type="text"
               data-slider-min='0' 
               data-slider-max='31'
               data-slider-step='1'
               data-slider-value='<?php echo $escala['qtd_svc']; ?>'
               data-slider-tooltip = 'always'
               />         
        <br>
        <br>
        <br> 
        <label>Limites de carga horária:</label>&nbsp&nbsp 
        <input id="ch" type="text"
               data-slider-min='100' 
               data-slider-max='200'
               data-slider-range='true'
               data-slider-value='[<?php echo $escala['ch_minima'] . ',' . $escala['ch_maxima']; ?>]'
               data-slider-tooltip = 'always'
               /> 
        <br> 
        <br> 
        <br>        
        <div class="col-xs-12" id="alerta-erro"></div> 
        <button class="btn btn-success pull-right" onclick="alterarEscala(<?php echo $escala['id']; ?>);">ALTERAR</button> 
    </div> 
</div> 