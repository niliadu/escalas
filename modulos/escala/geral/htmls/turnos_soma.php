<div class="row">   
    <div class="col-md-12">
        <label>Turnos:</label>
        <br>
        <select id="sel_turnso_soma"   data-size="false" multiple> 
            <?php
            foreach ($turnos as $t) {
                if (($geral && $t['id'] == $turnoPrinc) || (!$geral && $t['te_id'] == $turnoPrinc)) {
                    $selected = "selected";
                    $disabled = "disabled";
                } else {
                    $selected = (in_array($t['id'], $turnosSec)) ? "selected" : "";
                    $disabled = "";
                }
                if ($geral) {
                    echo "<option value='" . $t['id'] . "' $selected $disabled title='" . $t['legenda'] . "'>" . $t['nome'] . "</option>";
                } else {
                    echo "<option value='" . $t['te_id'] . "' $selected $disabled title='" . $t['legenda'] . "'>" . $t['nome'] . "</option>";
                }
            }
            ?>
        </select>
        <br> 
        <br> 
        <button class="btn btn-success pull-right" onclick="atualizarTurnsoSoma(<?php echo $geral ? 1 : 0; ?>,<?php echo $turnoPrinc; ?>, <?php echo $geral ? "''" : $escalaId; ?>, <?php echo $geral ? "''" : "'" . $turnos[0]['escala_legenda'] . "'"; ?>);">ATUALIZAR</button>
    </div>
</div>