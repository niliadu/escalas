<select id="tipo_escala" class="form-control" data-style="btn-info btn-xs" data-live-search="false" data-size="false"> 
    <?php
    $j = 1;
    foreach ($tiposNomes as $i => $tn) {
        $selected = ($j == sizeof($tiposNomes)) ? "selected" : "";
        echo "<option value='" . $i . "' $selected>" . strtoupper($tn) . "</option>";
        $j++;
    }
    ?>
</select>
