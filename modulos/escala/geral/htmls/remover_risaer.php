<h6 align='center'><b>Operador:</b> <?php echo $operador; ?></h6> 
<div class="row">
    <div class="col-xs-12">         
        <h6><b>Dia:</b> <?php echo $dados['dia']; ?></h6>
        <div class="row col-xs-4">
            <label><b>Serviço RISAER:</b></label>
            <select id="risaer-sair" class="form-control"> 
                <?php
                foreach ($risaer as $r) {
                    echo "<option value='" . $r['id'] . "'>" . $r['legenda'] . "</option>";
                }
                ?>
            </select> 
        </div>        
        <br>
        <br>        
        <div class="col-xs-12" id="alerta-erro"></div>
        <div class="col-xs-12">
            <br>
            <button class="btn btn-success pull-right" onclick="apagarRisaer(<?php echo $dados['idEscala'];?>,'<?php echo $dados['legEscala'];?>');">Dispensar</button>
        </div>
    </div>
</div>