<div class="row">  
    <div class="col-xs-12">
        <a class="btn pull-right" onclick="abrirAjuda('texto');" title="ajuda">
            <span class=" glyphicon glyphicon-question-sign" aria-hidden="true"></span>
        </a>
        <h6 align="center"><b>Operador:</b> <?php echo $operador; ?></h6>
    </div>
    <br>
    <br>
    <br>    
    <div class="col-md-12">         
        <h6><b>Dia:</b> <?php echo $dados['dia']; ?></h6>
        <div class="row col-xs-12">
            <div class="row col-xs-12">
                <label><b>Texto:</b></label>
                <input type="text" class="form-control" id="texto" maxlength="10" value="<?php echo $textosMostrar; ?>">
            </div>
        </div>        
        <br>
        <br>        
        <br>
        <br>        
        <div class="col-md-12" id="alerta-erro"></div> 
        <button class="btn btn-success pull-right" onclick="gravarTexto(<?php echo $dados['efetivo_mes']; ?>,<?php echo $dados['dia']; ?>,<?php echo $dados['tipo_escala']; ?>, <?php echo $dados['idEscala']; ?>, '<?php echo $dados['legEscala']; ?>', '<?php echo $textosMostrar; ?>', '<?php echo implode("-", array_column($infoTextos, 'id')); ?>')">Salvar</button>
    </div>
</div>