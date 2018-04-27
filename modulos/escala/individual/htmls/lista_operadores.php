<select id="operador" class="form-control" title="selecione um operador" data-live-search="true"> 
    <?php
    foreach ($operadores as $ordem) {
        foreach ($ordem as $o) {
            $selected = $o['usuario_id'] == $sessao['usu'] ? 'selected' : "";
            echo "<option value='" . $o['usuario_id'] . "' $selected>" . $o['pg'] . " " . $o['ng'] . "</option>";
        }
    }
    ?>
</select> 