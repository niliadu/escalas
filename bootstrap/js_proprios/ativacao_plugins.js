/* 
 * todos os plugins que precisam de uma chamada apos um ajax para surtir efeito serão chamados nessa funcao
 */

function ativarPlugins() {
    $('select').selectpicker({size: 5});

    $('[data-toggle="tooltip"]').tooltip();

    $('#ano-mes-picker').datetimepicker({
        format: 'mm-yyyy',
        autoclose: 1,
        startView: 4,
        minView: 3,
        forceParse: true
    });

    $('.clockpicker').clockpicker({
        donetext: 'Ok',
    });
    $(".clockpicker").on("change", function () {
        hora = $(this).children('input').val();
        $(this).children('.hora').html(hora);
    });
}

