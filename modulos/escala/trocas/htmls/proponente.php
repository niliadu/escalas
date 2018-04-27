<div class="col-xs-12">
    <h4 style="color:white;"><b>Proponente</b></h4>
</div>
<div class="alert bg-primary" col-xs-12>
    <h4 id='usuario-proponente' usuario='<?php echo $usuario; ?>'><?php echo $usuarioNome; ?></h4>
    <div class="row">
        <div class="col-xs-12" id="alerta-erro2"></div>          
    </div>
    <label>Escala:</label>
    <div class="row">
        <div class="col-xs-12">
            <select class="selectpicker" id="escala-proponente">
                <?php
                foreach ($escalas as $e) {
                    $disabled = in_array($e['escala'], $escalasPublicadas) ? "" : "disabled";
                    echo "<option value='" . $e['escala'] . "' $disabled>" . $e['legenda'] . "</option>";
                }
                ?>
            </select>
        </div>
    </div>
    <br>
    <div class="row" id="turnos-proponente"></div>
</div>