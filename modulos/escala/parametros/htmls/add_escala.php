<div class="row">   
    <div class="col-xs-12">  
        <label>Nome da Escala:</label> 
        <input type="text" class="form-control" id="nome-esc" maxlength="45"> 
        <br> 
        <label>Legenda:</label> 
        <input type="text" class="form-control" id="legenda-esc" maxlength="45"> 
        <br> 
        <label>Turnos:</label> 
        <select id="turno-esc" class="form-control" title="selecione os turnos" multiple> 
            <?php
            foreach ($turnos as $i => $t) {
                echo "<option value='" . $turnos[$i]['id'] . "'>" . $turnos[$i]['legenda'] . "</option>";
            }
            ?>
        </select>
        <br> 
        <br> 
        <br> 
        <label>Quantidade de Serviços:</label>&nbsp&nbsp 
        <input id="svc" type="text"/>         
        <br>
        <br>
        <br> 
        <label>Limites de carga horária:</label>&nbsp&nbsp 
        <input id="ch" type="text"/> 
        <br> 
        <br> 
        <br>         
        <div class="col-xs-12" id="alerta-erro"></div> 
        <button class="btn btn-success pull-right" onclick="gravarEscala();">ADICIONAR</button> 
    </div> 
</div>