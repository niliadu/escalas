<div class="row">   
    <div class="col-xs-12">  
        <div class="col-xs-12">
            <label>Nome:</label> 
            <input type="text" class="form-control" id="nome-risaer" maxlength="45" value="<?php echo $resposta[0]['nome']; ?>"> 
            <br> 
            <label>Legenda:</label> 
            <input type="text" class="form-control" id="legenda-risaer" maxlength="45" value="<?php echo $resposta[0]['legenda']; ?>"> 
        </div>
    </div> 
    <div class="col-xs-12">
        <div class="col-xs-4"> 
            <br> 
            <label>Início:</label> 
            <div class="input-group clockpicker" data-autoclose="true"> 
                <span class="input-group-addon" > 
                    <span class="glyphicon glyphicon-time" > </span> 
                </span> 
                <input id="inicio-risaer" type="text" class="form-control" style="display:none;" value="<?php echo $resposta[0]['inicio']; ?>"> 
                <span class="input-group-addon hora" > 
                    <?php echo substr($resposta[0]['inicio'], 0, 5); ?>
                </span> 
            </div> 
        </div> 
        <div class="col-xs-4"> 
            <br> 
            <label>Término:</label> 
            <div  class="input-group clockpicker" data-autoclose="true"> 
                <span class="input-group-addon" > 
                    <span class="glyphicon glyphicon-time"> </span> 
                </span> 
                <input id="termino-risaer" type="text" class="form-control" style="display:none;" value="<?php echo $resposta[0]['termino']; ?>"> 
                <span class="input-group-addon hora" > 
                    <?php echo substr($resposta[0]['termino'], 0, 5); ?>
                </span> 
            </div> 
        </div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-4"> 
            <br> 
            <label>Serviço de 24 horas ou mais:</label> 
            <select id="maiorq24" data-width="auto"> 
                <option value="0" <?php echo $resposta[0]['mais_q_24h'] == 0 ? "selected" : ""; ?>>NÃO</option> 
                <option value="1" <?php echo $resposta[0]['mais_q_24h'] == 1 ? "selected" : ""; ?>>SIM</option> 
            </select> 
        </div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-3"> 
            <br> 
            <label>Tipo etapa:</label> 
            <br> 
            <select id="etapa-risaer" data-width="auto"> 
                <option value="1" <?php echo $resposta[0]['tipo_etapa'] == 1 ? "selected" : ""; ?>>SEM ETAPA</option> 
                <option value="2" <?php echo $resposta[0]['tipo_etapa'] == 2 ? "selected" : ""; ?>>ETAPA 1X</option> 
                <option value="3" <?php echo $resposta[0]['tipo_etapa'] == 3 ? "selected" : ""; ?>>ETAPA 5X</option> 
                <option value="4" <?php echo $resposta[0]['tipo_etapa'] == 4 ? "selected" : ""; ?>>ETAPA 10X</option> 
            </select> 
        </div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-5">
            <br>
            <label>Descando anteriror:</label> 
            <input type="text" class="form-control soNumero" id="danterior-risaer" maxlength="45" value="<?php echo $resposta[0]['intervalo_antes']; ?>">
            <br> 
            <label>Descando posterior:</label> 
            <input type="text" class="form-control soNumero" id="dposteiror-risaer" maxlength="45" value="<?php echo $resposta[0]['intervalo_depois']; ?>"> 
        </div>
    </div>
</div>
<div class="col-xs-12" id="alerta-erro"></div>
<div class="row">
    <div class="col-xs-12"> 
        <br> 
        <button class="btn btn-success pull-right" onclick="alterarRISAER(<?php echo $id; ?>);">ALTERAR</button> 
    </div> 
</div>