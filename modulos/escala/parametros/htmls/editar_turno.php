<div class="row">   
    <div class="col-xs-12">  
        <div class="col-xs-12"> 
            <label>Nome:</label> 
            <input type="text" class="form-control" id="nome-turno" maxlength="45" value="<?php echo $resposta[0]['nome']; ?>"> 
            <br> 
            <label>Legenda:</label> 
            <input type="text" class="form-control" id="legenda-turno" maxlength="45" value="<?php echo $resposta[0]['legenda']; ?>"> 
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
                <input id="inicio-turno" type="text" class="form-control" style="display:none;" value="<?php echo $resposta[0]['inicio']; ?>"> 
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
                <input id="termino-turno" type="text" class="form-control" style="display:none;" value="<?php echo $resposta[0]['termino']; ?>"> 
                <span class="input-group-addon hora" >       
                    <?php echo substr($resposta[0]['termino'], 0, 5); ?>
                </span> 
            </div> 
        </div> 
    </div>
    <div class="col-xs-12">
        <div class="col-xs-3"> 
            <br> 
            <label>Etapa cheia:</label> 
            <br> 
            <select id="etapa-turno" data-width="auto"> 
                <option value="0"  <?php echo $resposta[0]['etapa_full'] ? : "selected"; ?>  >Não</option> 
                <option value="1"   <?php echo $resposta[0]['etapa_full'] ? "selected" : ""; ?>  >Sim</option> 
            </select> 
        </div> 
        <div class="col-xs-4"> 
            <br> 
            <label>Período:</label> 
            <br> 
            <select id="periodo-turno" data-width="auto"> 
                <option value="0" <?php echo $resposta[0]['periodo'] == 0 ? "selected":""; ?> >Não Definido</option> 
                <option value="1"  <?php echo $resposta[0]['periodo'] == 1 ? "selected":""; ?> >Diurno</option> 
                <option value="2"  <?php echo $resposta[0]['periodo'] == 2 ? "selected":""; ?> >Noturno</option> 
            </select> 
        </div> 
        <div class="col-xs-2"> 
            <br> 
            <label>Pós-noturno:</label> 
            <br> 
            <select id="pos_noturno-turno" data-width="auto"> 
                <option value="0" <?php echo $resposta[0]['pos_noturno'] ? : "selected"; ?>  >Não</option>
                <option value="1" <?php echo $resposta[0]['pos_noturno'] ? "selected" : ""; ?> >Sim</option> 
            </select> 
        </div> 
    </div>
    <div class="col-xs-12" id="alerta-erro"></div> 
    <div class="col-xs-12"> 
        <br> 
        <button class="btn btn-success pull-right" onclick="alterarTurno(<?php echo $id; ?>);">ALTERAR</button> 
    </div> 
</div>