<ul class="nav nav-tabs">
    <?php
    foreach ($escalas as $i => $es) {
        $active = ($i == 0) ? "class='active'" : "";
        ?>
        <li 
    <?php echo "$active><a href='#panel-escala-" . $es['legenda'] . "' data-toggle='tab' onclick=\"recarregarEscala(" . $es['id'] . ",'" . $es['legenda'] . "');\">" . $es['nome'] . "</a>"; ?>
    </li>        
<?php } ?>
</ul>
<div class="tab-content">
    <br>
    <?php
    foreach ($escalas as $i => $es) {
        $active = ($i == 0) ? " active" : "";
        echo "<div class='tab-pane" . $active . "' id='panel-escala-" . $es['legenda'] . "'></div>";
    }
    ?>  

</div>
