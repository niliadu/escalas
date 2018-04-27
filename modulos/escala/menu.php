

<li <?php echo $itemMenu == 0 ? 'class="active"' : ''; ?>>
    <a href="<?php echo $sessao['raiz_html'];?>modulos/escala/individual/">Individual</a>
</li>
<li <?php echo $itemMenu == 1 ? 'class="active"' : ''; ?>>
    <a href="<?php echo $sessao['raiz_html'];?>modulos/escala/geral/">Escala Geral</a>
</li>
<li <?php echo $itemMenu == 2 ? 'class="active"' : ''; ?>>
    <a href="<?php echo $sessao['raiz_html'];?>modulos/escala/trocas/">Trocas Operador</a>
</li>
<?php
if ($func == 0 || $func == 1) {
    ?>
    <li <?php echo $itemMenu == 3 ? 'class="active"' : ''; ?>>
        <a href="<?php echo $sessao['raiz_html'];?>modulos/escala/parametros/">Parâmetros</a>
    </li>
    <li <?php echo $itemMenu == 4 ? 'class="active"' : ''; ?>>
        <a href="<?php echo $sessao['raiz_html'];?>modulos/escala/trocasesc/">Trocas Escalante</a>
    </li>
    <li <?php echo $itemMenu == 6 ? 'class="active"' : ''; ?>>
        <a href="<?php echo $sessao['raiz_html'];?>modulos/escala/itemesc/">Item Escala</a>
    </li>
    
    <?php
    if ($func == 1) {
        ?>
        <li <?php echo $itemMenu == 5 ? 'class="active"' : ''; ?>>
            <a href="<?php echo $sessao['raiz_html'];?>modulos/escala/trocaschef/">Trocas Chefia</a>
        </li>
        <?php
    }
}
