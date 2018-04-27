<label>Escala:</label>
<div class="row">
    <div class="col-xs-12">
        <select class="selectpicker" id="escala-proposto">
            <?php
            foreach ($escalas as $e) {
                $disabled = in_array($e['escala'], $escalasPublicadas) ? "" : "disabled";
                echo "<option value='" . $e['escala'] . "' $disabled>" . $e['legenda'] . "</option>";
            }
            ?>
        </select>
    </div>
</div>