<h6 align="center"><b>Operador:</b> <?php echo $operador; ?></h6>
<div class="row">
    <br>
    <div class="col-xs-5">         
        <div class="col-xs-10">
            <label><b>Do dia:</b></label>
            <select class="form-control"> 
                <?php echo "<option value='" . $dados['dia'] . "' selected>" . $dados['dia'] . "</option>"; ?>
            </select>
        </div>
        <div class="col-xs-10">
            <br>
            <label><b>Turno:</b></label>
            <select id="turno-sair" class="form-control"> 
                <?php
                foreach ($turnos as $ind => $t) {
                    echo "<option value='" . $t['id'] . "' idsvc='" . $svc_id[$ind] . "' >" . $t['legenda'] . "</option>";
                }
                ?>
            </select>
        </div>
    </div>
    <div class="col-xs-2" align='right'>
        <div class="col-xs-2" style="border-right:1px solid lightgray ;height:160"></div>
    </div>
    <div class="col-xs-5">
        <div class="col-xs-10">
            <label><b>Para o dia:</b></label>
            <select id="dia-entrar" class="form-control"> 
                <?php
                for ($d = 1; $d <= $diasMes; $d++) {
                    echo "<option value='" . $d . "'>" . $d . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-xs-10">
            <br>
            <label><b>Turno:</b></label>
            <select id="turno-entrar" class="form-control"> 
                <?php
                foreach ($turnosEscala as $t) {
                    echo "<option value='" . $t['id'] . "'>" . $t['legenda'] . "</option>";
                }
                ?>
            </select> 
        </div>
    </div>
    <div class="col-xs-12">
        <br>
        <label><b>Motivo:</b></label>
        <input type="text" class="form-control" id="motivo" name="motivo" list="definicao" placeholder="Selecione ou adicione o motivo" size="50">
        <datalist id="definicao">
            <option value="Necessidade Operacional">
            <option value="Particular de <?php echo $legenda; ?>">
        </datalist>
    </div>
    <br>
    <br>        
    <div class="col-xs-12" id="alerta-erro"></div>
    <div class="col-xs-12">
        <br>
        <button class="btn btn-success pull-right" onclick="gravarRemanejamento(<?php echo $dados['operador']; ?>,<?php echo $dados['dia']; ?>, '<?php echo $chMensal; ?>', <?php echo $dados['idEscala']; ?>, '<?php echo $dados['legEscala']; ?>', <?php echo $dados['tipo_escala']; ?>);">Remanejar</button>
    </div>
</div>
</div>
