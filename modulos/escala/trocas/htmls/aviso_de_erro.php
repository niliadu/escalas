<br>
<div class="analise-alert alert alert-dismissable alert-<?php echo $corErro; ?>" style="color: black;">
    <h4><b>O(A) <?php echo $nomePE; ?>:</b></h4>
    <?php
    foreach ($textoErro as $te) {
        echo "<h4>$te</h4>";
    }
    ?>
</div>