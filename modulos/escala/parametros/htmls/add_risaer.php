<div class="row">   
    <div class="col-xs-12">  
        <div class="col-xs-12">
            <label>Nome:</label> 
            <input type="text" class="form-control" id="nome-risaer" maxlength="45"> 
            <br> 
            <label>Legenda:</label> 
            <input type="text" class="form-control" id="legenda-risaer" maxlength="45"> 
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
                <input id="inicio-risaer" type="text" class="form-control" style="display:none;"> 
                <span class="input-group-addon hora" > 
                    -- : -- 
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
                <input id="termino-risaer" type="text" class="form-control" style="display:none;"> 
                <span class="input-group-addon hora" > 
                    -- : --
                </span> 
            </div> 
        </div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-4"> 
            <br> 
            <label>Serviço de 24 horas ou mais:</label> 
            <select id="maiorq24" data-width="auto"> 
                <option value="0">NÃO</option> 
                <option value="1">SIM</option> 
            </select> 
        </div> 
    </div>
    <div class="col-xs-12">
        <div class="col-xs-3"> 
            <br> 
            <label>Tipo etapa:</label> 
            <br> 
            <select id="etapa-risaer" data-width="auto"> 
                <option value="1">SEM ETAPA</option> 
                <option value="2">ETAPA 1X</option> 
                <option value="3">ETAPA 5X</option> 
                <option value="4">ETAPA 10X</option> 
            </select> 
        </div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-5">
            <br>
            <label>Descanso anteriror:</label> 
            <input class="form-control soNumero" id="danterior-risaer" >
            <br> 
            <label>Descanso posterior:</label> 
            <input class="form-control soNumero" id="dposterior-risaer"> 
        </div>
    </div>
</div>
<div class="col-xs-12" id="alerta-erro"></div>
<div class="row">
    <div class="col-xs-12"> 
        <br> 
        <button class="btn btn-success pull-right" onclick="gravarRISAER();">ADICIONAR</button> 
    </div> 
</div>