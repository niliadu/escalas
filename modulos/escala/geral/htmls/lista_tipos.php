<select id="tipo_escala" class="form-control" data-style="btn-info btn-xs" data-live-search="false" data-size="false"> 
    <?php
    foreach ($tiposNomes as $i => $tn) {
        $selected = "";
        ($i == $itemSelected) ? $selected = "selected" : $selected = "";
        echo "<option value='" . $i . "' $selected>" . strtoupper($tn) . "</option>";
    }
    ?>
</select>
