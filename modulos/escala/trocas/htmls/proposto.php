<div class="col-xs-12">
    <h4 style="color:white;"><b>Proposto</b></h4>
</div>
<div class="alert alert-dismissable bg-primary">
    <div class="row">
        <div class="col-xs-12">
            <select id="usuario-proposto" class="form-control" title="selecione um operador" data-live-search="true"> 
                <?php
                foreach ($operadores as $ordem) {
                    foreach ($ordem as $o) {
                        if ($o['usuario_id'] != $proponente) {
                            echo "<option value='" . $o['usuario_id'] . "'>" . $o['pg'] . " " . $o['ng'] . "</option>";
                        }
                    }
                }
                ?>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12" id="alerta-erro3"></div>          
    </div>
    <div id="escalas-proposto"></div>
    <br>
    <div class="row" id="turnos-proposto"></div>
</div>
