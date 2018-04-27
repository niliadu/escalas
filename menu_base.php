<div class="col-xs-12">
    <ul class="nav nav-tabs">
        <?php        
        if ($sessao['modulo'] != null) {
            include $sessao['raiz'] . 'modulos/' . $sessao['modulo'] . '/menu.php';
        }
        ?>
        <li class="dropdown pull-right">
            <a href="<?php echo $sessao['raiz_html'];?>logout.php"><span class="glyphicon glyphicon-log-out" title='Retornar ao SGPO' aria-hidden="true"></span></a>
        </li>

    </ul>
</div>
