<div class="row">   
    <div class="col-xs-6 col-xs-offset-3">
        <label>Folgas Consecutivos:</label> 
        <select id="folgas_restricoes" class="form-control"> 
            <?php
            for ($i = 1; $i <= 10; $i++) {
                $selected = '';
                if ($i == $restricoes['folgas']) {
                    $selected = 'selected';
                }
                echo "<option $selected>$i</option>";
            }
            ?>
            <option value='11' <?php echo $restricoes['folgas'] == 11 ? "selected" : ""; ?> >Não Aplicável</option>
        </select>
        <br>
        <br>
    </div>
</div>
<div class="row">   
    <div class="col-xs-6 col-xs-offset-3">
        <label>Máximo de trocas:</label> 
        <select id="trocas_restricoes" class="form-control"> 
            <?php
            for ($i = 0; $i <= 10; $i++) {
                $selected = '';
                if ($i == $restricoes['trocas']) {
                    $selected = 'selected';
                }
                echo "<option $selected>$i</option>";
            }
            ?>
            <option value='11' <?php echo $restricoes['trocas'] == 11 ? "selected" : ""; ?> >Não Aplicável</option>
        </select>
        <br> 
        <br>
    </div>
</div>
<div class="row">   
    <div class="col-xs-12" id="alerta-erro"></div> 
    <button class="btn btn-success pull-right" onclick="alterarConsec();">MODIFICAR</button> 
</div> 
