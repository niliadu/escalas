<div class="row">   
    <div class="col-md-12">
        <textarea class="form-control" id="texto-justificativa"></textarea>
        <div id="erro-justficativa"></div>
        <br> 
        <br> 
        <button class="btn btn-<?php echo $cor; ?> pull-right" onclick="atualizarTroca(<?php echo $obrigatorio ? 1 : 0; ?>,<?php echo $trocaID; ?>, <?php echo $novoStatus; ?>);"><?php echo $novoStatus == 2 ? 'AUTORIZAR' : "REJEITAR"; ?></button>
    </div>
</div>