/* 
 * facilita a insercao de alertas dinamicos para as respostas do ajax
 */
jQuery.fn.repaint = function() {//este codigo serve para garantir q o item q sera animado ja foi desenhado pelo DOM
    // getting calculated width forces repaint (hopefully?!)
    this.width();
    // return chain
    return this;
};

bootalerta = {
    erro: function (alvo, texto) {
        html = '<br><div class="alert fade alert-dismissable alert-danger">' +
                '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
                '<h5>' + texto + '</h5>' +
                '</div>';
        $(alvo).html(html).repaint().children(".alert").addClass('in');
    },
    sucesso: function (alvo, texto) {
        html = '<br><div class="alert fade alert-dismissable alert-success">' +
                '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
                '<h5>' + texto + '</h5>' +
                '</div>';
        $(alvo).html(html).repaint().children(".alert").addClass('in');
    },
    aviso: function (alvo, texto) {
        html = '<br><div class="alert fade alert-dismissable alert-warning">' +
                '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
                '<h5>' + texto + '</h5>' +
                '</div>';
        $(alvo).html(html).repaint().children(".alert").addClass('in');
    },
    info: function (alvo, texto) {
        html = '<br><div class="alert fade alert-dismissable alert-info">' +
                '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
                '<h5>' + texto + '</h5>' +
                '</div>';
        $(alvo).html(html).repaint().children(".alert").addClass('in');
    }
};