<?php
//escala home

$temModulo = true;

/////////////////////////////////////////////////
session_start();
include $_SESSION['raiz'] . "funcoes.php";
acesso('escala', -1);
/////////////////////////////////////////////////
?>
<head>
    <meta charset="utf-8">
    <title>Escala Geral</title>
    <?php
    $itemMenu = 1;
    include $raiz . 'scripts_css.php';
    include $raiz . 'cabecalho.php';
    ?>
</head>
<body>

    <div  class="container">
        <div class="row collapse in" id='cabecalho_subs'>
            <div class=" col-xs-12">
                <h3>Escala Geral<button id="btn_cabecalho" class="btn pull-right">MENU PRINCIPAL</button></h3>
            </div>
        </div>
        <div class="row">
            <div id="cont-geral" class="col-xs-12">
                <div class="row">
                    <div class="col-xs-3" align="left">
                        <?php include $raiz . 'calendario.php'; ?>
                    </div>
                    <div id="display_tipo_escala" class="row col-xs-2 y-overflow" style="display: none;"> 
                    </div>
                    <div class="col-xs-2 pull-right" id="loader" align="right"></div>
                    <div class="row">
                        <div class="col-sm-12">
                            <br>
                            <div id="display_escalas" class="col-xs-12">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="tabbable" id="tabs-escalas"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>            
            </div>
        </div>
    </div>
</body>

<script>
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //esconder o menu para dar mais espacço de visualização da pagina
    $("#cabecalho").collapse('hide');
    $("#btn_cabecalho").on("click", function () {
        $("#cabecalho").collapse('show');
        $("#cabecalho_subs").collapse('hide');
    });

    $(document).on('click', function () {
        if ($(this).attr('id') != "cabecalho") {
            $("#cabecalho").collapse('hide');
            $("#cabecalho_subs").collapse('show');
        }
    });

    var definitiva = false;
    ativarPlugins();

    $("#ano-mes-picker").datetimepicker().on('changeDate', function () {
        $("#quantitativo_geral").attr('style', 'display:none;');
        $("#tabs-escalas").attr('style', 'display:none;');
        data = $(this).datetimepicker('getDate').toString();
        data = data.split(" ");
        mesA = data[1];
        mesNome = correcaoMes.mesesA[mesA];
        mesNum = correcaoMes.mesesI[mesA];
        ano = data[3];
        $("#ano-mes-picker").attr('mes', mesNum);
        $("#ano-mes-picker").attr('ano', ano);
        $("#ano-mes-texto").html(mesNome + " - " + ano);
        verificarTipoEscala();
    });
    verificarTipoEscala();

//    $.lockfixed("#display_quantitativo_escalas", {offset:{top:0}});
//    $.lockfixed("#display_escalas", {offset:{top:140, bottom:300}});
    //$("#teste5").sticky({topSpacing: 100});


    function verificarTipoEscala() {
        $("#display_tipo_escala").attr('style', 'display:none;')
        ano = parseInt($("#ano-mes-picker").attr('ano'));
        mes = parseInt($("#ano-mes-picker").attr('mes'));
        orgao = "<?php echo $_SESSION['orgao_usu_id']; ?>";
        unidade = "<?php echo $_SESSION['unidade_usu_id']; ?>";
        ajax.sync.obj({
            dados: {ano: ano, mes: mes, orgao: orgao, unidade: unidade, item: 'tipos_escala_param'},
            endereco: 'checar.php',
            sucesso: function (dados) {
                if (!dados.existe) {
                    bootbox.alert("Não foram encontrados parâmentros para o mês e ano selecionado.<br>Os parâmetros deverão ser criados antes!");
                } else {
                    $("#tabs-escalas").attr('style', 'display:block;');
                    $("#quantitativo_geral").attr('style', 'display:block;');
                    carregarTiposEscala(dados.grupo);
                }
            }
        });
    }

    function carregarTiposEscala(grupo) {
        $("#ano-mes-picker").attr('grupo', grupo);
        ajax.sync.obj({
            dados: {grupo: grupo, item: 'tipos_escala'},
            endereco: 'checar.php',
            sucesso: function (dados) {
                if (!dados.existe) {
                    ajax.sync.html({
                        dados: {grupo: grupo, item: 'branco_preenchida'},
                        endereco: 'carregar.php',
                        sucesso: function (dados) {
                            bootbox.dialog({
                                message: dados,
                                title: "Criar Escala"
                            });
                        }
                    });
                } else {
                    definitiva = $.inArray(4, dados.tipos) === -1 ? false : true;
                    carregarSelecaoTipos(dados.tipos);
                }
            }
        });
    }

    function carregarSelecaoTipos(tipos) {
        ajax.sync.html({
            dados: {tipos: tipos, item: 'tipos_escala'},
            endereco: 'carregar.php',
            reativar: true,
            sucesso: function (dados) {
                $("#display_tipo_escala").html(dados);
                ativarPlugins();
                $("#display_tipo_escala").attr('style', "display:block;");
                carregarEscalas();
            }
        });
    }

    $(document).on('change', '#tipo_escala', function () {
        tipo = $("#tipo_escala").val();
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        if (tipo == 5 || tipo == 6 || tipo == 7) {
            ajax.sync.obj({
                dados: {grupo: grupo, tipo: tipo, item: 'escalas'},
                endereco: 'criar.php',
                sucesso: function (dados) {
                    verificarTipoEscala();
                }
            });
        } else {
            carregarEscalas();
        }
    });

    $(document).on('change', "#opcao", function () {
        if ($(this).val() == 'todas') {
            $('#porEscala').attr('style', "display:none;");
            $('#geral').attr('style', "display:block;");
        } else {
            $('#porEscala').attr('style', "display:block;");
            $('#geral').attr('style', "display:none;");
        }
    });

    function carregarEscalas() {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        ajax.sync.html({
            dados: {grupo: grupo, item: 'abas_escalas'},
            endereco: 'carregar.php',
            sucesso: function (dados) {
                $("#tabs-escalas").html(dados);
            }
        });

        ajax.sync.obj({
            dados: {grupo: grupo, item: 'pegar_escalas'},
            endereco: 'carregar.php',
            sucesso: function (dados) {
                gerarEscalas(dados);
            }
        });
    }

    //var d = new Date();
    //var n = d.getTime();

    function gerarEscalas(escalas) {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        tipo = $("#tipo_escala").val();
        ajax.sync.obj({
            dados: {definitiva: definitiva, grupo: grupo, tipo: tipo, id: escalas[0].id, item: 'escalas'},
            endereco: 'carregar.php',
            sucesso: function (dados) {
                //d = new Date();
                //n = d.getTime();
                //console.log(n);
                gerarTabelaEscala('panel-escala-' + escalas[0].legenda, dados);
                $(".comments").editable();
            }
        });
    }

    function recarregarEscala(id, leg) {
        escala = [{id: id, legenda: leg}];
        gerarEscalas(escala);
    }

    function carregarPrevistaBranco(grupo) {
        ajax.sync.obj({
            dados: {grupo: grupo, item: 'escala_branco'},
            endereco: 'inserir.php',
            sucesso: function (dados) {
                bootbox.hideAll();
                carregarSelecaoTipos(0);
            }
        });
    }

    function carregarPrevistaPreenchida(grupo) {
        ajax.sync.html({
            dados: {grupo: grupo, item: 'escala_preenchida'},
            endereco: 'carregar.php',
            reativar: true,
            sucesso: function (dados) {
                bootbox.hideAll();
                bootbox.dialog({
                    message: dados,
                    title: "Sequência a ser Preenchida"
                });
            }
        });
    }

    function adicionarTurnoSequencia(div) {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        prefixo = "#selects-turnos-";
        prefixo2 = "div-select-" + div + "-";
        prox = $(prefixo + div + ">.div-select").length + 1;
        $(prefixo + div + ">.div-select").each(function () {
            if ((prefixo2 + prox) == this.id) {
                prox++;
            }
        });
        ajax.sync.html({
            dados: {prox: prox, grupo: grupo, item: 'select_turno', div: div},
            endereco: 'carregar.php',
            reativar: true,
            sucesso: function (dados) {
                $(prefixo + div).append(dados);
            }
        });
    }

    function delSelectTurno(elemento) {
        $("#" + elemento).remove();
    }

    function preencherEscalaSequencia(divs) {
        turnos = {};
        var arrayDivs = divs.split(",");
        $(".turSelSeq").each(function () {
            if (typeof $(this).attr('escleg') !== 'undefined') {
                escleg = $(this).attr('escleg');
                valor = $(this).val();
                if ($.inArray(escleg, arrayDivs) != -1) {
                    if (!(escleg in turnos)) {
                        turnos[escleg] = [];
                        turnos[escleg].push(valor);
                    } else {
                        turnos[escleg].push(valor);
                    }
                }
            }
        });
        if (!($.isEmptyObject(turnos))) {
            erro = false;
            $.each(turnos, function (key) {
                if ($.inArray("", turnos[key]) != -1) {
                    erro = true;
                    return false;
                }
            });
            if (!erro) {
                $('#alerta-erro').html("");
                ajax.sync.obj({
                    dados: {turnos: turnos, grupo: grupo, item: 'preencher_escalas'},
                    endereco: 'inserir.php',
                    sucesso: function (dados) {
                        bootbox.hideAll();
                        carregarTiposEscala(grupo);
                    }
                });
            } else {
                bootalerta.erro("#alerta-erro", "Selecione todos os turnos, se for em branco selecione FOLGA.");
            }
        } else {
            bootalerta.erro("#alerta-erro", "Selecione todos os turnos, se for em branco selecione FOLGA.");
        }
    }

    ///////////////////////////////////////////////
    function colunasQtd(diasMes) {
        col = [];
        col.push({data: 'qtd'});
        col.push({data: 'turnos'});
        for (i = 1; i <= diasMes; i++) {
            dia = ("d" + i);
            col.push({data: dia});
        }
        col.push({data: 'btn'});
        return col;
    }
    function colunasEsc(diasMes, qtdTurnos) {
        col = [];
        col.push({data: '1'});
        col.push({data: '2'});
        for (i = 1; i <= diasMes; i++) {
            dia = ("d" + i);
            col.push({data: dia});
        }
        col.push({data: "qtd_svc"});
        for (i = 1; i <= qtdTurnos; i++) {
            turnos = "qt" + i;
            col.push({data: turnos});
        }
        col.push({data: "ch_mes"});
        col.push({data: "ch_cht"});
        return col;
    }


    function larguraColunasQtd(diasMes, qtdTurnos) {
        larg = [];
        larg.push(30);
        larg.push(160);
        for (i = 2; i < (diasMes + 2); i++) {
            larg.push(27);
        }
        if (qtdTurnos < 4) {
            larg.push(230);
        } else {
            larg.push(25 * qtdTurnos + 130);
        }
        return larg;
    }
    function larguraColunasEsc(diasMes, qtdTurnos) {
        larg = [];
        larg.push(30);
        larg.push(160);
        for (i = 2; i < (diasMes + 2); i++) {
            larg.push(27);
        }
        larg.push(30);
        ///////////////largura minima para a celula de equalizacao
        if (qtdTurnos == 1) {
            larg.push(80);
        } else if (qtdTurnos == 2) {
            larg.push(40);
            larg.push(40);
        } else {
            for (i = 0; i < qtdTurnos; i++) {
                larg.push(30);
            }
        }
        /////////////////////////////////////
        larg.push(50);
        larg.push(50);
        return larg;
    }
    function textoCabecalho(diasMes) {
        texto = [];
        texto.push("QTD");
        texto.push("TURNOS");
        for (i = 1; i <= diasMes; i++) {
            dia = ("0" + i).slice(-2);
            texto.push(dia);
        }
        texto.push("");
        return texto;
    }

    function mesclarCelulasQtd(diasMes, qtdTurnos) {
        cels = [];
        cels.push({row: 0, col: (diasMes + 2), rowspan: qtdTurnos, colspan: 1});
        return cels;
    }

    function mesclarCelulasEsc(diasMes, qtdTurnos, qtdTurnosGeral) {
        cels = [];
        cels.push({row: 0, col: (diasMes + 2), rowspan: 1, colspan: (qtdTurnos + 3)});
        cels.push({row: 1, col: (diasMes + 2), rowspan: qtdTurnosGeral, colspan: (qtdTurnos + 3)});
        cels.push({row: qtdTurnosGeral + 1, col: 0, rowspan: 1, colspan: (diasMes + qtdTurnos + 5)});
        cels.push({row: qtdTurnosGeral + 2, col: (diasMes + 2), rowspan: 1, colspan: (qtdTurnos + 3)});
        cels.push({row: qtdTurnosGeral + 3, col: (diasMes + 2), rowspan: qtdTurnos, colspan: (qtdTurnos + 3)});
        cels.push({row: qtdTurnos + qtdTurnosGeral + 3, col: 0, rowspan: 2, colspan: 1});
        cels.push({row: qtdTurnos + qtdTurnosGeral + 3, col: 1, rowspan: 2, colspan: 1});
        cels.push({row: qtdTurnos + qtdTurnosGeral + 3, col: (diasMes + 2), rowspan: 2, colspan: 1});
        cels.push({row: qtdTurnos + qtdTurnosGeral + 3, col: (diasMes + 3), rowspan: 1, colspan: qtdTurnos});
        cels.push({row: qtdTurnos + qtdTurnosGeral + 3, col: (diasMes + 3 + qtdTurnos), rowspan: 1, colspan: 2});
        return cels;
    }

    //
    function menuModificacoesEscala(idDiv, diasMes) {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        var disable = [true, true, true];
        menu = {
            callback: function (key, options) {
                linha = options['end']['row'];
                coluna = options['end']['col'];
                dadosTotal = ($("#" + idDiv).handsontable('getData'))[linha];
                dados = dadosTotal[coluna];
                operador = dadosTotal[1]['atr']['valor'];
                legenda = dadosTotal[0]['valor'];
                chMensal = dadosTotal[dadosTotal.length - 2]['valor'];
                if (key == "operacional:remanejamento") {
                    ajax.sync.html({
                        dados: {chMensal: chMensal, legenda: legenda, operador: operador, dados: dados, diasMes: diasMes, item: 'remanejamento'},
                        endereco: 'carregar.php',
                        reativar: true,
                        sucesso: function (dados) {
                            bootbox.dialog({
                                message: dados,
                                title: "Remanejamento"
                            });
                        }
                    });
                } else if (key == "operacional:escalacao") {
                    ajax.sync.html({
                        dados: {chMensal: chMensal, legenda: legenda, operador: operador, dados: dados, item: 'escalacao'},
                        endereco: 'carregar.php',
                        reativar: true,
                        sucesso: function (dados) {
                            bootbox.dialog({
                                message: dados,
                                title: "Escalação"
                            });
                        }
                    });
                } else if (key == "operacional:dispensa") {
                    ajax.sync.html({
                        dados: {chMensal: chMensal, legenda: legenda, operador: operador, dados: dados, item: 'dispensa'},
                        endereco: 'carregar.php',
                        reativar: true,
                        sucesso: function (dados) {
                            bootbox.dialog({
                                message: dados,
                                title: "Dispensa"
                            });
                        }
                    });
                } else if (key == "texto") {
                    ajax.sync.html({
                        dados: {legenda: legenda, operador: operador, dados: dados, item: 'texto'},
                        endereco: 'carregar.php',
                        reativar: true,
                        sucesso: function (dados) {
                            bootbox.dialog({
                                size: 'small',
                                message: dados,
                                title: "Texto"
                            });
                        }
                    });
                } else if (key == "risaer:escalar") {
                    ajax.sync.html({
                        dados: {operador: operador, dados: dados, grupo: grupo, item: 'risaer_escalar'},
                        endereco: 'carregar.php',
                        reativar: true,
                        sucesso: function (dados) {
                            bootbox.dialog({
                                message: dados,
                                title: "RISAER"
                            });
                        }
                    });
                } else if (key == "risaer:dispensar") {
                    ajax.sync.html({
                        dados: {operador: operador, dados: dados, item: 'risaer_dispensar'},
                        endereco: 'carregar.php',
                        reativar: true,
                        sucesso: function (dados) {
                            bootbox.dialog({
                                message: dados,
                                title: "RISAER"
                            });
                        }
                    });
                }
            },
            items: {
                "operacional": {
                    key: "operacional",
                    name: "OPERACIONAL",
                    "submenu": {
                        "items": [
                            {
                                key: "operacional:remanejamento",
                                "name": "REMANEJAMENTO",
                                disabled: function () {
                                    linha = ($("#" + idDiv).handsontable('getSelected'))[2];
                                    coluna = ($("#" + idDiv).handsontable('getSelected'))[3];
                                    dados = ($("#" + idDiv).handsontable('getData'));
                                    menuFlag = typeof dados[linha][coluna]['menu'] !== 'undefined' ? false : true;
                                    if (dados[linha][coluna] != null) {
                                        if (dados[linha][coluna]['svc_id'] == "") {
                                            return true;
                                        } else {
                                            disable[0] = menuFlag;
                                            return menuFlag;
                                        }
                                    } else {
                                        disable[0] = menuFlag;
                                        return menuFlag;
                                    }
                                }
                            },
                            {
                                key: "operacional:escalacao",
                                "name": "ESCALAÇÃO",
                                disabled: function () {
                                    linha = ($("#" + idDiv).handsontable('getSelected'))[2];
                                    coluna = ($("#" + idDiv).handsontable('getSelected'))[3];
                                    dados = ($("#" + idDiv).handsontable('getData'));
                                    if (typeof dados[linha][coluna]['menu'] !== 'undefined') {
                                        if (!dados[linha][coluna]['menu']) {
                                            return true;
                                        } else {
                                            disable[1] = false;
                                            return false;
                                        }
                                    } else {
                                        return true;
                                    }
                                }
                            },
                            {
                                key: "operacional:dispensa",
                                "name": "DISPENSA",
                                disabled: function () {
                                    linha = ($("#" + idDiv).handsontable('getSelected'))[2];
                                    coluna = ($("#" + idDiv).handsontable('getSelected'))[3];
                                    dados = ($("#" + idDiv).handsontable('getData'));
                                    menuFlag = typeof dados[linha][coluna]['menu'] !== 'undefined' ? false : true;
                                    if (dados[linha][coluna] != null) {
                                        if (dados[linha][coluna]['svc_id'] == "") {
                                            return true;
                                        } else {
                                            disable[2] = menuFlag;
                                            return menuFlag;
                                        }
                                    } else {
                                        disable[2] = menuFlag;
                                        return menuFlag;
                                    }
                                }
                            }
                        ]
                    },
                    disabled: function () {
                        if (disable[0] && disable[1] && disable[2]) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                },
                "texto": {
                    name: "TEXTO",
                    disabled: function () {
                        linha = ($("#" + idDiv).handsontable('getSelected'))[2];
                        coluna = ($("#" + idDiv).handsontable('getSelected'))[3];
                        dados = ($("#" + idDiv).handsontable('getData'));
                        menuFlag = typeof dados[linha][coluna]['menu'] !== 'undefined' ? false : true;
                        return menuFlag;
                    }
                },
                "risaer": {
                    key: "risaer",
                    name: "RISAER",
                    disabled: function () {
                        linha = ($("#" + idDiv).handsontable('getSelected'))[2];
                        coluna = ($("#" + idDiv).handsontable('getSelected'))[3];
                        dados = ($("#" + idDiv).handsontable('getData'));
                        menuFlag = typeof dados[linha][coluna]['menu'] !== 'undefined' ? false : true;
                        return menuFlag;
                    },
                    "submenu": {
                        "items": [
                            {
                                key: "risaer:escalar",
                                "name": "ESCALAR"
                            },
                            {
                                key: "risaer:dispensar",
                                "name": "DISPENSAR"
                            }
                        ]
                    }
                }
            }
        };
        return menu;
    }

    function gravarRemanejamento(operador, diaDisp, chMensal, idEscala, legEscala, tipoEscala) {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        turnoDisp = $("#turno-sair").val();
        motivo = $("#motivo").val();
        diaEsc = $("#dia-entrar").val();
        turnoEsc = $("#turno-entrar").val();
        idSvc = $("#turno-sair option:selected").attr('idsvc');
        if (motivo == "") {
            bootalerta.erro("#alerta-erro", "O motivo não pode ficar em branco");
        } else if (diaDisp == diaEsc && turnoEsc == turnoDisp) {
            bootalerta.erro("#alerta-erro", "Os dias e turnos do remanejamento são os mesmos");
        } else {
            $("#alerta-erro").html("");
            ajax.sync.obj({
                dados: {
                    diaEsc: diaEsc,
                    grupo: grupo,
                    diaDisp: diaDisp,
                    operador: operador,
                    chMensal: chMensal,
                    idEscala: idEscala,
                    turnoEsc: turnoEsc,
                    turnoDisp: turnoDisp,
                    tipoEscala: tipoEscala,
                    item: "add_remanejamento_restricoes"
                },
                endereco: 'checar.php',
                sucesso: function (dados) {
                    if (dados.afastamento) {
                        bootbox.alert(dados.texto);
                        prosseguir = false;
                    } else if (dados.existe) {
                        bootbox.confirm(dados.texto, function (result) {
                            if (result) {
                                prosseguirRemanejamento(idSvc, diaEsc, turnoEsc, idEscala, legEscala);
                            }
                        });
                    } else {
                        prosseguirRemanejamento(idSvc, diaEsc, turnoEsc, idEscala, legEscala);
                    }
                }
            });
        }
    }

    function prosseguirRemanejamento(idSvc, diaEsc, turnoEsc, idEscala, legEscala) {
        ajax.sync.obj({
            dados: {idSvc: idSvc, diaEsc: diaEsc, turnoEsc: turnoEsc, item: "remanejar"},
            endereco: 'alterar.php',
            sucesso: function (dados) {
                bootbox.hideAll();
                bootbox.alert("Serviço remanejado com sucesso!");
                carregarTabelaQtdGeral();
                escala = [{id: idEscala, legenda: legEscala}];
                gerarEscalas(escala);
            }
        });
    }

    function prosseguirEscalacao(operador, tipoEscala, diaEsc, turnoEsc, idEscala, legEscala) {
        ajax.sync.obj({
            dados: {operador: operador, tipoEscala: tipoEscala, diaEsc: diaEsc, turnoEsc: turnoEsc, item: "escalar"},
            endereco: 'alterar.php',
            sucesso: function (dados) {
                bootbox.hideAll();
                bootbox.alert("Serviço escalado com sucesso!");
                carregarTabelaQtdGeral();
                escala = [{id: idEscala, legenda: legEscala}];
                gerarEscalas(escala);
            }
        });
    }

    function gravarEscalacao(operador, diaEsc, tipoEscala, chMensal, idEscala, legEscala) {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        turnoEsc = $("#turno-entrar").val();
        motivo = $("#motivo").val();
        if (motivo == "") {
            bootalerta.erro("#alerta-erro", "O motivo não pode ficar em branco");
        } else {
            $("#alerta-erro").html("");
            ajax.sync.obj({
                dados: {
                    diaEsc: diaEsc,
                    grupo: grupo,
                    operador: operador,
                    chMensal: chMensal,
                    idEscala: idEscala,
                    turnoEsc: turnoEsc,
                    tipoEscala: tipoEscala,
                    item: "add_escalacao_restricoes"
                },
                endereco: 'checar.php',
                sucesso: function (dados) {
                    if (dados.existe) {
                        bootbox.confirm(dados.texto, function (result) {
                            if (result) {
                                prosseguirEscalacao(operador, tipoEscala, diaEsc, turnoEsc, idEscala, legEscala);
                            }
                        });
                    } else {
                        prosseguirEscalacao(operador, tipoEscala, diaEsc, turnoEsc, idEscala, legEscala);
                    }
                }
            });
        }
    }

    function apagarRisaer(escID, escLeg) {
        servico = $("#risaer-sair").val();
        ajax.sync.obj({
            dados: {
                servico: servico,
                item: "remover_risaer"
            },
            endereco: 'remover.php',
            sucesso: function (dados) {
                bootbox.hideAll();
                bootbox.alert("Serviço removido com sucesso!");
                recarregarEscala(escID, escLeg);
            }
        });
    }

    function gravarRisaer(usuario, efetivo_mes, tipo_escala, dia, idEscala, legEscala) {
        $("#alerta-erro").html("");
        servico = $("#risaer-entrar").val();
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        ajax.sync.obj({
            dados: {
                usuario: usuario,
                dia: dia,
                servico: servico,
                grupo: grupo,
                item: "add_risaer"
            },
            endereco: 'checar.php',
            sucesso: function (dados) {
                if (dados.existe) {
                    bootbox.confirm(dados.texto, function (result) {
                        if (result) {
                            prosseguirEscalacaoRISAER(servico, efetivo_mes, tipo_escala, dia, idEscala, legEscala);
                        }
                    });
                } else {
                    prosseguirEscalacaoRISAER(servico, efetivo_mes, tipo_escala, dia, idEscala, legEscala);
                }
            }
        });
    }

    function prosseguirEscalacaoRISAER(servico, efetivo_mes, tipo_escala, dia, idEscala, legEscala) {
        ajax.sync.obj({
            dados: {servico: servico, efetivo_mes: efetivo_mes, tipo_escala: tipo_escala, dia: dia, item: "inserir_risaer"},
            endereco: 'inserir.php',
            sucesso: function (dados) {
                bootbox.hideAll();
                bootbox.alert("Serviço escalado com sucesso!");
                recarregarEscala(idEscala, legEscala);
            }
        });
    }

    function prosseguirDispensa(idSvc, idEscala, legEscala) {
        ajax.sync.obj({
            dados: {idSvc: idSvc, item: "dispensar"},
            endereco: 'alterar.php',
            sucesso: function (dados) {
                bootbox.hideAll();
                bootbox.alert("Serviço dispensado com sucesso!");
                carregarTabelaQtdGeral();
                escala = [{id: idEscala, legenda: legEscala}];
                gerarEscalas(escala);
            }
        });
    }

    function gravarDispensa(operador, diaDisp, chMensal, idEscala, legEscala, tipoEscala) {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        motivo = $("#motivo").val();
        turnoDisp = $("#turno-sair").val();
        idSvc = $("#turno-sair option:selected").attr('idsvc');
        if (motivo == "") {
            bootalerta.erro("#alerta-erro", "O motivo não pode ficar em branco");
        } else {
            $("#alerta-erro").html("");
            ajax.sync.obj({
                dados: {
                    diaDisp: diaDisp,
                    grupo: grupo,
                    operador: operador,
                    chMensal: chMensal,
                    idEscala: idEscala,
                    turnoDisp: turnoDisp,
                    tipoEscala: tipoEscala,
                    item: "add_dispensa_restricoes"
                },
                endereco: 'checar.php',
                sucesso: function (dados) {
                    if (dados.existe) {
                        bootbox.confirm(dados.texto, function (result) {
                            if (result) {
                                prosseguirDispensa(idSvc, idEscala, legEscala);
                            }
                        });
                    } else {
                        prosseguirDispensa(idSvc, idEscala, legEscala);
                    }
                }
            });
        }
    }

    function gravarTexto(efetivo_mes, dia, tipoEscala, idEscala, legEscala, textosAntes, textosIds) {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        texto = $("#texto").val();
        $("#alerta-erro").html("");
        ajax.sync.obj({
            dados: {texto: texto, grupo: grupo, item: "add_texto"},
            endereco: 'checar.php',
            sucesso: function (dados) {
                if (dados.existe) {
                    bootalerta.erro("#alerta-erro", dados.texto);
                } else {
                    ajax.sync.obj({
                        dados: {textosAntes: textosAntes, texto: texto, efetivo_mes: efetivo_mes, dia: dia, tipoEscala: tipoEscala, textosIds: textosIds, item: "texto"},
                        endereco: 'alterar.php',
                        sucesso: function (dados) {
                            bootbox.hideAll();
                            bootbox.alert("Texto salvo com sucesso!");
                            escala = [{id: idEscala, legenda: legEscala}];
                            gerarEscalas(escala);
                        }
                    });
                }
            }
        });

    }

//Custom editor:necessario para as mudancas de valores que são passados por objetos
    var myEditor = Handsontable.editors.TextEditor.prototype.extend();
    myEditor.prototype.beginEditing = function (initialValue) {
        Handsontable.editors.TextEditor.prototype
                .beginEditing.apply(this, [this.originalValue.valorEd]);
    };
    myEditor.prototype.saveValue = function (val) {
//        console.log(this.originalValue);
        idAnt = this.originalValue.svc_id;
        novoValorCel = val[0][0];
        escalaId = this.originalValue.idEscala;
        escalaLeg = this.originalValue.legEscala;
        dia = this.originalValue.dia;
        operador = this.originalValue.operador;
        tipo = this.originalValue.tipo_escala;
        este = this;
        turnoId = this.originalValue.turno_id;
        turn_esc_id = this.originalValue.te_id;
        if (this.originalValue.valorEd != val[0][0]) {
            //faz as celulas ficarem nao editaveis ate o fim do ajax
            this.instance.updateSettings({
                cells: function (row, col, prop) {
                    return propriedadesCelulaEscEdicao(row, col, prop, this);
                }
            });
            if (typeof turnoId !== "undefined") {
                novoValorCel = Number.isNaN(parseInt(novoValorCel)) ? 0 : parseInt(novoValorCel);
                ajax.sync.cel({
                    dados: {
                        novoValorCel: novoValorCel,
                        turnoId: turnoId,
                        item: "atualizar_celula_qtd"
                    },
                    endereco: "alterar.php",
                    sucesso: function (dados) {
                        escala = [{id: escalaId, legenda: escalaLeg}];
                        gerarEscalas(escala);
                    },
                    erro: function () {
                        //retorna a edicao das celulas apos a edicao
                        este.instance.updateSettings({
                            cells: function (row, col, prop) {
                                return propriedadesCelulaEsc(row, col, prop, this);
                            }
                        });
                        ///////////////////////////////////////////////////
                    }
                });
            } else if (typeof turn_esc_id !== "undefined") {
                novoValorCel = Number.isNaN(parseInt(novoValorCel)) ? 0 : parseInt(novoValorCel);
                ajax.sync.cel({
                    dados: {
                        novoValorCel: novoValorCel,
                        turn_esc_id: turn_esc_id,
                        item: "atualizar_celula_qtd_esc"
                    },
                    endereco: "alterar.php",
                    sucesso: function (dados) {
                        escala = [{id: escalaId, legenda: escalaLeg}];
                        gerarEscalas(escala);
                    },
                    erro: function () {
                        //retorna a edicao das celulas apos a edicao
                        este.instance.updateSettings({
                            cells: function (row, col, prop) {
                                return propriedadesCelulaEsc(row, col, prop, this);
                            }
                        });
                        ///////////////////////////////////////////////////
                    }
                });
            } else {
                ///////////////////////////////////////////////////
                ajax.sync.cel({
                    dados: {
                        idsAnteriores: idAnt,
                        dia: dia,
                        operador: operador,
                        tipo: tipo,
                        novoValorCel: novoValorCel,
                        escalaId: escalaId,
                        item: "atualizar_celula"
                    },
                    endereco: "alterar.php",
                    sucesso: function (dados) {
                        escala = [{id: escalaId, legenda: escalaLeg}];
                        gerarEscalas(escala);
                    },
                    erro: function () {
                        //retorna a edicao das celulas apos a edicao
                        este.instance.updateSettings({
                            cells: function (row, col, prop) {
                                return propriedadesCelulaEsc(row, col, prop, this);
                            }
                        });
                        ///////////////////////////////////////////////////
                    }
                });
            }
        }
    };
///////////////////////////////////////////////////////////////////////////////////

    function propriedadesCelulaEsc(row, col, prop, tabela) {
        var cellProperties = {editor: myEditor};
        cellProperties.renderer = estiloGeralCelulas;

        celula = tabela.instance.getDataAtCell(row, col);
        if (celula != null) {
            cellProperties.readOnly = !celula.editavel;
        }

        return cellProperties;
    }

    function propriedadesCelulaEscEdicao(row, col, prop, tabela) {
        var cellProperties = {editor: myEditor};
        cellProperties.readOnly = true;
        cellProperties.renderer = estiloGeralCelulas;
        return cellProperties;
    }

    function estiloGeralCelulas(instance, td, row, col, prop, value, cellProperties) {





        if (value != null && typeof value === 'object') {
            classes = (value.classes !== "undefined") ? value.classes : "";
            html = (value.html !== "undefined") ? value.html : "";
            atr = (value.atr !== "undefined") ? value.atr : null;

            value = value.valor;

        } else {
            classes = "";
            html = "";
            atr = null;
        }
        Handsontable.renderers.TextRenderer.apply(this, arguments);
        td.style.color = "black";
        $(td).addClass(classes);
        $(td).append(html);
        if (atr != null) {
            for (var at in atr) {
                $(td).attr(at, atr[at]);
            }
        }

        /////adiciona o rotulo para a celula que for necessario

        if ($(td).hasClass('rotulo')) {
            $('body').append("<div id='larguraVer' style='display:none; font-size:x-small; text-align:center;'></div>");
            conteudo = $(td).text();
            $("#larguraVer").html(conteudo);
            conteudoLarg = $("#larguraVer").width();
            largura = $(td).width();
            if (conteudoLarg > largura) {
                $(td).append("<span class='rotulotexto'>" + conteudo + "</span>");
            }
            $("#larguraVer").remove();
        }
    }

/////////////////////////////////////////////////////////////////////
    function gerarTabelaEscala(idDiv, dados) {
        tipo = $("#tipo_escala").val();
        $("#" + idDiv).handsontable({
            data: dados.valores,
            minCols: (dados.diasMes + dados.qtdTurnos + 5),
            maxCols: (dados.diasMes + dados.qtdTurnos + 5),
            minRows: (dados.qtdTurnos + dados.qtdOpr + 2 + dados.qtdEquipes),
            maxRows: (dados.qtdTurnos + dados.qtdOpr + 2 + dados.qtdEquipes),
            columns: colunasEsc(dados.diasMes, dados.qtdTurnos),
            colWidths: larguraColunasEsc(dados.diasMes, dados.qtdTurnos), // can also be a number or a function            
            fixedRowsTop: (dados.qtdTurnos + dados.qtdTurnosGeral + 5),
            currentRowClassName: 'linhaColSel',
            currentColClassName: 'linhaColSel',
            outsideClickDeselects: true,
            contextMenu: menuModificacoesEscala(idDiv, dados.diasMes),
            mergeCells: mesclarCelulasEsc(dados.diasMes, dados.qtdTurnos, dados.qtdTurnosGeral),
            cells: function (row, col, prop) {
                return propriedadesCelulaEsc(row, col, prop, this);
            },
            afterRender: function () {
                ativarPlugins();

            }
        });
//        if (tipo == 3) {
//<?php // include $raiz . "bootstrap/js_proprios/menu_direito_corrente.php";                                                              ?>
//        }

    }

    //////////////////////////////////////////////////
    function turnosSoma(geral, idTurnoPrincipal, escalaId) {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        if (geral) {
            ajax.sync.html({
                dados: {item: 'turnos_soma_geral', turnoPrinc: idTurnoPrincipal, grupo: grupo},
                endereco: 'carregar.php',
                reativar: true,
                sucesso: function (dados) {
                    bootbox.dialog({
                        title: "Turnos para soma geral",
                        message: dados
                    });
                }
            });
        } else {
            ajax.sync.html({
                dados: {item: 'turnos_soma_escala', turnoPrinc: idTurnoPrincipal, escalaId: escalaId, grupo: grupo},
                endereco: 'carregar.php',
                reativar: true,
                sucesso: function (dados) {
                    bootbox.dialog({
                        title: "Turnos para soma",
                        message: dados
                    });
                }
            });
        }
    }

    $(document).on('change', '#select_esc_soma_geral', function () {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        valor = $(this).val();
        escalaId = parseInt($(this).attr('escid'));
        escalaLeg = $(this).attr('escleg');
        escala = [{id: escalaId, legenda: escalaLeg}];
        ajax.sync.obj({
            dados: {grupo: grupo, valor: valor, item: 'select_soma'},
            endereco: 'alterar.php',
            sucesso: function (dados) {
                gerarEscalas(escala);
            }
        });
    });

    function atualizarTurnsoSoma(geral, idTurnoPrinc, id, escalaLeg) {
        turnosSec = $("#sel_turnso_soma").val();
        if (turnosSec == "") {
            turnosSec = "vazio";
        }
        ajax.sync.obj({
            dados: {turnosSec: turnosSec, idTurnoPrinc: idTurnoPrinc, geral: geral, item: 'turnos_soma'},
            endereco: 'alterar.php',
            sucesso: function (dados) {
                bootbox.hideAll();
                carregarTabelaQtdGeral();
                if (!geral) {
                    escala = [{id: id, legenda: escalaLeg}];
                    gerarEscalas(escala);
                }
            }
        });
    }

    function abrirAjuda(tipo) {
        if (tipo == 'preencher') {
            bootbox.alert("<br><h5 align='justify'>Insira uma sequência de turnos que corresponderá ao padrão " +
                    "a ser seguido pela primeira equipe das escalas. As demais " +
                    "equipes irão seguir esse mesmo padrão, porém deslocada de " +
                    "um dia em relação à equipe anterior. Escolha se deseja seguir " +
                    "um padrão para todas as escalas ou selecione um padrão para cada " +
                    "escala. Se alguma(s) escala(s) ficar(em) com padrão em branco, " +
                    "essa(s) será(ão) gerada(s) em branco. Cada seletor de turnos" +
                    " corresponderá aos serviços de um dia. Para adicionar turnos" +
                    " em outro dia, adicione seletores em Adicionar" +
                    ".</h5>");
        } else if ('texto') {
            bootbox.alert("<br><h5 align='justify'> Insira textos diferentes separados pelo " +
                    "caracter hífen(-). Cada texto separado por hífen será apresentado agrupado na célula.</h5>");
        }
    }
    function modificarStatusTroca(grupo, tipo, valor, escalaId, escalaLeg, este) {
        $(este).removeAttr('onclick');
        ajax.sync.obj({
            dados: {grupo: grupo, tipo: tipo, valor: valor, item: 'status_troca'},
            endereco: 'alterar.php',
            sucesso: function (dados) {
                escala = [{id: escalaId, legenda: escalaLeg}];
                gerarEscalas(escala);
            }
        });
    }

    function modificarPublicadaEscala(id, escalaLeg, tipo, publicada, valor, este) {
        $(este).removeAttr('onclick');
        ajax.sync.obj({
            dados: {id: id, tipo: tipo, publicada: publicada, valor: valor, item: 'status_publicada'},
            endereco: 'alterar.php',
            sucesso: function (dados) {
                escala = [{id: id, legenda: escalaLeg}];
                gerarEscalas(escala);
            }
        });
    }

    function pegarComentario(em_id) {
        resultado = "";
        ajax.sync.obj({
            dados: {em_id: em_id, tipo: tipo, item: 'pegar_comentario'},
            endereco: 'checar.php',
            sucesso: function (dados) {
                if (dados.existe) {
                    resultado = dados.comentario;
                }
            }
        });
        return resultado;
    }

    function salvarComentario(em_id, tipo, escala, texto) {
        ajax.sync.obj({
            dados: {em_id: em_id, tipo: tipo, texto: texto, item: 'comentario'},
            endereco: 'inserir.php',
            sucesso: function (dados) {
                if (dados.removeu) {
                    $("#" + em_id + "_" + tipo + "_" + escala).removeClass('show-comments');
                } else {
                    $("#" + em_id + "_" + tipo + "_" + escala).addClass('show-comments');
                }
            }
        });
        $("#" + em_id + "_" + tipo + "_" + escala).editable('hide');
    }

    function chamarComentario(em_id, tipo, escala) {
        $("#" + em_id + "_" + tipo + "_" + escala).editable('show');
    }

    function retirarInformacaoDoAfastamento(tdref, idsADeletar, escLeg, escID) {
        td = $(tdref);
        bootbox.confirm(
                "As informações do dia estão apenas como aviso dos turnos ou informações adicionais que foram apagadas na inserção do afastamento.<br><br>Deseja removê-la definitivamente?",
                function (result) {
                    if (result) {
                        ajax.sync.obj({
                            dados: {ids: idsADeletar, item: 'informacoes_afastamento'},
                            endereco: 'remover.php',
                            sucesso: function (dados) {
                                td.removeAttr('onclick');//necessario pois no recarregamento da escala o onclick permanecia
                                //mesmo com os novos valores em carregar
                                recarregarEscala(escID, escLeg);

                            }
                        });
                    }
                });
    }


    function mostrarAfastamentos(emID) {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        ajax.sync.html({
            dados: {item: 'lista_afastastamentos', em_id: emID, grupo: grupo},
            endereco: 'carregar.php',
            reativar: true,
            sucesso: function (dados) {
                $('body').click();
                bootbox.dialog({
                    title: "Afastamentos",
                    message: dados
                });
            }
        });
    }
</script>