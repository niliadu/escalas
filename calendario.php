<?php
$ano = date('Y');
$mes = date('m');
$mesAno = mb_strtoupper(strftime('%B - %Y ', strtotime('today')), "UTF-8");
?>
<div class='input-group date' id='ano-mes-picker' mes="<?php echo $mes; ?>" ano='<?php echo $ano; ?>' grupo=''>
    <span class="input-group-addon alert-info" id="span-ano-mes">
        <span class="glyphicon glyphicon-calendar"></span>
    </span>
    <input type='text' class="form-control" style="display: none;"/>
    <span class="btn input-group-addon alert-info" onclick="$('#span-ano-mes').click();" id='ano-mes-texto'>
        <?php echo $mesAno; ?>
    </span>
</div>