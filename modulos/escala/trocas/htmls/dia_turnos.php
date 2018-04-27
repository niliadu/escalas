<div class="row col-md-6">
    <div class="col-xs-12">
        <label>Dia:</label>
    </div>
    <div class="col-xs-12">
        <select class="selectpicker dia_turno" id="dia-<?php echo $pp; ?>">
            <?php
            $qtdDiasMes = date("t", strtotime("$ano-$mes-1"));
            for ($i = 1; $i <= $qtdDiasMes; $i++) {
                $dia0 = $i < 10 ? "0$i" : $i;
                echo "<option value='$i'>$dia0</option>";
            }
            ?>
        </select>
    </div>
</div>
<div class="row col-xs-1"></div><!--serve apenas para garantir uma separacao entre os selects do proposto-->
<div class="row col-md-6">
    <div class="col-xs-12">
        <label>Turno:</label>
    </div>
    <div class="col-xs-12">
        <select class="selectpicker dia_turno" id="turno-<?php echo $pp; ?>">
            <?php
            foreach ($turnos as $t) {
                echo "<option value='" . $t['id'] . "'>" . $t['legenda'] . "</option>";
            }
            ?>
        </select>
    </div>
</div>