<h3>Órgãos</h3>
<br>
<div class="col-sm-3"></div>
<div class="col-sm-6">
    <?php
    if ($temSetor) {
        foreach ($orgaos as $i => $of) {
            ?>                
            <div class="alert alert-info">
                <a class="panel-title" href="<?php echo $sessao['raiz_html']; ?>home/session_modulo.php?modulo=escala&id=<?php echo $i; ?>&func=<?php echo $of['funcao']; ?>&orgao=<?php echo $of['nome']; ?>&unidade=<?php echo $of['unidade']; ?>&nome_unidade=<?php echo $of['nome_unidade']; ?>">
                    <h3><?php echo $of['nome']; ?></h3>
                    <h5><?php echo $of['nome_unidade']; ?></h5>
                </a>
            </div>                            
            <?php
        }
    } else {
        ?>                
        <div class="alert alert-info">
            <h3>Não existe nenhum Setor Alocado</h3>            
        </div>
        <?php
    }
    ?>
</div>
<div class="col-sm-3"></div>