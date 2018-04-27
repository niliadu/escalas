<select id="escalas_sel" class="form-control" data-style="btn-primary btn-xs" data-live-search="false" data-size="false" multiple title="Selecione as escalas desejadas">
    <?php
    foreach ($escalas as $escala) {
        echo "<option value='" . $escala['id'] . "' title='" . $escala['legenda'] . "'>" . $escala['nome'] . "</option>";
    }
    ?>
</select>
