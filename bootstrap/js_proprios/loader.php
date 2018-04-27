<script>
//criacao do loader para as chamadas ajax onde necessario

    imgLoading = "<?php echo $sessao['raiz_html']; ?>imagens/loading.gif";
    loader = {
        show: function (alvo) {
            $(alvo).html("<img src='" + imgLoading + "'/>");
        },
        hide: function (alvo) {
            $(alvo).html("");
        }
    };
</script>