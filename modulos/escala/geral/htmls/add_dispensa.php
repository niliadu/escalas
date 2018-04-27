<h6 align="center"><b>Operador:</b> <?php echo $operador; ?></h6>
<div class="row">   
    <div class="col-xs-12">         
        <h6><b>Dia:</b> <?php echo $dados['dia']; ?></h6>
        <div class="row col-xs-4">
            <label><b>Turno:</b></label>
            <select id="turno-sair" class="form-control"> 
                <?php
                foreach ($turnos as $ind => $t) {
                    echo "<option value='" . $t['id'] . "' idsvc='" . $svc_id[$ind] . "' >" . $t['legenda'] . "</option>";
                }
                ?>
            </select>
        </div>
        <div class=" row col-xs-12">
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
            <button class="btn btn-success pull-right" onclick="gravarDispensa(<?php echo $dados['operador']; ?>,<?php echo $dados['dia']; ?>, '<?php echo $chMensal; ?>', <?php echo $dados['idEscala']; ?>, '<?php echo $dados['legEscala']; ?>', <?php echo $dados['tipo_escala']; ?>);">Dispensar</button>
        </div>
    </div>
</div>