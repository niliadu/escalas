<div class="row">   
    <div class="col-xs-12">  
        <label>Operador:</label>
        <h5><?php echo $usuariosSistema[$operador['operador']]['pg'] . " " . $usuariosSistema[$operador['operador']]['ng']; ?></h5>
        <label>Legenda:</label> 
        <input type="text" class="form-control" id="legenda-efetivo" maxlength="45" value="<?php echo $operador['legenda']; ?>">
        <br>
        <label>Escalas:</label> 
        <select id="escalas-efetivo" class="form-control" title="selecione as escala" multiple> 
            <?php
            foreach ($escalas as $e) {
                $selected = '';
                if (in_array($e['id'], $operador['escalas'])) {
                    $selected = 'selected';
                }
                echo "<option value='" . $e['id'] . "' $selected>" . $e['legenda'] . "</option>";
            }
            ?>
        </select>
        <br>
        <br>
        <label>Função:</label> 
        <select id="funcao-efetivo" class="form-control" title="selecione uma função"> 
            <?php
            foreach ($funcoes as $f) {
                $selected = '';
                if ($operador['funcao'] == $f['id']) {
                    $selected = 'selected';
                }
                echo "<option value='" . $f['id'] . "' $selected>" . $f['nome'] . "</option>";
            }
            ?>
        </select> 
        <br>
        <br>
        <label>Menutenção:</label> 
        <select id="manutencao-efetivo" class="form-control"> 
            <option value=0 <?php echo $operador['manutencao'] ? '' : 'selected' ?>>NÃO</option>
            <option value=1 <?php echo $operador['manutencao'] ? 'selected' : '' ?>>SIM</option>            
        </select> 
        <br> 
        <br> 
        <div class="col-xs-12" id="alerta-erro"></div> 
        <button class="btn btn-success pull-right" onclick="alterarEfetivo(<?php echo $id; ?>, false);">MODIFICAR</button> 
    </div> 
</div>