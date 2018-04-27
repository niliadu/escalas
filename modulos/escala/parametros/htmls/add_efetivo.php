<div class="row">   
    <div class="col-xs-12">  
        <label>Operador:</label> 
        <select id="operador-efetivo" class="form-control" title="selecione um operador" data-live-search="true"> 
            <?php
            foreach ($usuariosSistema as $ordem => $valores) {
                foreach ($valores as $i => $of) {
                    echo "<option value='" . $i . "'>" . $of['pg'] . " " . $of['ng'] . "</option>";
                }
            }
            ?>
        </select> 
        <br>
        <br> 
        <label>Legenda:</label> 
        <input type="text" class="form-control" id="legenda-efetivo" maxlength="45"> 
        <br>
        <label>Escalas:</label> 
        <select id="escalas-efetivo" class="form-control" title="selecione as escalas" multiple> 
            <?php
            foreach ($escalas as $e) {
                echo "<option value='" . $e['id'] . "'>" . $e['legenda'] . "</option>";
            }
            ?>
        </select>
        <br>
        <br>
        <label>Função:</label> 
        <select id="funcao-efetivo" class="form-control" title="selecione uma função"> 
            <?php
            foreach ($funcoes as $f) {
                echo "<option value='" . $f['id'] . "'>" . $f['nome'] . "</option>";
            }
            ?>
        </select> 
        <br>
        <br>
        <label>Manutenção:</label> 
        <select id="manutencao-efetivo" class="form-control"> 
            <option value=0 selected>NÃO</option>
            <option value=1>SIM</option>
        </select> 
        <br> 
        <br> 
        <div class="col-xs-12" id="alerta-erro"></div> 
        <button class="btn btn-success pull-right" onclick="gravarEfetivo();">ADICIONAR</button> 
    </div> 
</div>