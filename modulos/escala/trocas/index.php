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
    <title>Trocas Operador</title>
    <?php
    $itemMenu = 2;
    include $sessao['raiz'] . 'scripts_css.php';
    include $sessao['raiz'] . 'cabecalho.php';
    ?>
    <link rel="stylesheet" type="text/css" href='<?php echo $sessao['raiz_html']; ?>bootstrap/css_proprio/individual_mini.css'>
</head>
<body>
    <div class="container">
        <div class="row collapse in" id='cabecalho_subs'>
            <div class=" col-xs-12">
                <h3>Trocas Operador</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="row">
                    <div col-xs-12>
                        <div class="col-xs-3" align="left" id='calendario'>
                            <?php include $sessao['raiz'] . 'calendario.php'; ?>
                        </div>
                        <div id="display_tipo_escala" class="row col-xs-2 y-overflow" style="display: none;"></div>
                        <div class="col-xs-2 pull-right" id="loader" align="right"></div>
                    </div>
                </div>        
            </div>
        </div>
        <br>
        <div class="row col-xs-12">
            <div class="tabbable" id="tabs-trocas">
                <ul class="nav nav-tabs">
                    <li id='li-lancamento' class="active">
                        <a  href="#panel-lancamento" data-toggle="tab" onclick="verificarSeTrocaEstaLiberada();">Lançamento</a>
                    </li>
                    <li id='li-lancadas'>
                        <a  href="#panel-lancadas" data-toggle="tab" onclick="carregarTrocasLancadas();">Lançadas</a>
                    </li>
                    <li id='li-concluidas'>
                        <a  href="#panel-concluidas" data-toggle="tab" onclick="carregarTrocasConcluidas();">Concluídas</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="panel-lancamento">
                        <div class="row">
                            <div class="col-xs-12" id="alerta-erro"></div>
                            <div class="row col-xs-12">
                                <br>
                                <div class="row col-xs-12">
                                    <div class="col-xs-6" id="proponente"></div>
                                    <div class="col-xs-6" id="escala-individual-proponente"></div>
                                </div>
                                <div class="col-xs-12" align='center'>
                                    <hr width='95%'>
                                </div>
                                <div class="row col-xs-12">
                                    <div class="col-xs-6" id="proposto"></div>
                                    <div class="col-xs-6" id="escala-individual-proposto"></div>
                                </div>
                                <div class="col-xs-12" align='left'>
                                    <div class="row col-xs-3">
                                        <button id='btn-analisar' style="display: none;" class="btn btn-warning" onclick="analisarTroca();">Analisar Proposição</button>
                                    </div>
                                </div>
                                <div id='analise-proponente' class="col-xs-12"></div>
                                <div id='analise-proposto' class="col-xs-12"></div>
                                <div class="col-xs-12" align='left'>
                                    <div class="row col-xs-3">
                                        <button id='btn-salvar' style="display: none;" class="btn btn-success" onclick="salvarTroca();">Salvar Proposição</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane " id="panel-lancadas"></div>
                    <div class="tab-pane" id="panel-concluidas"></div>
                </div>
            </div>

        </div>
    </div>
</body>

<script>

///////////////////////////
    ativarPlugins();
    var definitiva = false;
    $("#ano-mes-picker").datetimepicker().on('changeDate', function () {
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

    function verificarTipoEscala() {
        resetarHTML();

        ano = parseInt($("#ano-mes-picker").attr('ano'));
        mes = parseInt($("#ano-mes-picker").attr('mes'));
        orgao = "<?php echo $sessao['orgao_usu_id']; ?>";
        mesNome = correcaoMes.meses[mes];
        ajax.sync.obj({
            dados: {ano: ano, mes: mes, orgao: orgao, item: 'tipos_escala_param'},
            endereco: 'checar.php',
            sucesso: function (dados) {
                if (!dados.existe) {
                    bootbox.alert("Ainda não existem escalas para visualização no mês em questão");
                } else {
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
                    bootbox.alert("Ainda não existem escalas para visualização no mês em questão");
                } else {
                    carregarSelecaoTipos(dados.tipos);
                }
            }
        });
    }

    function carregarSelecaoTipos(tipos) {
        ajax.sync.html({
            dados: {tipos: tipos, item: 'tipos_escala'},
            endereco: 'carregar.php',
            sucesso: function (dados) {
                $("#display_tipo_escala").html(dados);
                $("#display_tipo_escala").attr('style', "display:block;");
                ativarPlugins();
                verificarSeDefinitva();
            }
        });
    }
    function verificarSeDefinitva() {
        vals = [];
        $("#tipo_escala option").each(function () {
            vals.push($(this).attr('value'));
        });
        definitiva = $.inArray('4', vals) === -1 ? false : true;
        if (definitiva) {
            $("#panel-lancamento").removeClass('active');
            $("#panel-lancadas").removeClass('active');
            $("#panel-concluidas").addClass('active');
            $("#li-lancamento").removeClass('active');
            $("#li-lancadas").removeClass('active');
            $("#li-concluidas").addClass('active');

        }
        idPanel = $('.tab-pane.active').attr('id');
        switch (idPanel) {
            case "panel-lancamento":
                verificarSeTrocaEstaLiberada();
                break;
            case "panel-lancadas":
                carregarTrocasLancadas();
                break;
            case "panel-concluidas":
                carregarTrocasConcluidas();
                break;
        }
    }
    function verificarSeTrocaEstaLiberada() {
        if (definitiva) {
            bootalerta.erro("#alerta-erro", "Não é mais possível lançar trocas para este mês.")
        } else {
            tipo = parseInt($("#tipo_escala").val());
            grupo = parseInt($("#ano-mes-picker").attr('grupo'));
            ajax.sync.obj({
                dados: {grupo: grupo, tipo: tipo, item: 'trocas_liberadas'},
                endereco: 'checar.php',
                sucesso: function (dados) {
                    if (!dados.existe) {
                        bootalerta.erro("#alerta-erro", "As trocas ainda não estão liberadas para este tipo de escala.")
                    } else {
                        checarSeProponenteEstaNoEfetivo();
                    }
                }
            });
        }
    }

    function checarSeProponenteEstaNoEfetivo() {
        usuario = "<?php echo $sessao['usu']; ?>";
        usuarioNome = "<?php echo $sessao['pg_nome']; ?>";
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        ajax.sync.obj({
            dados: {grupo: grupo, usuario: usuario, usuarioNome: usuarioNome, item: 'alocacao_efetivo'},
            endereco: 'checar.php',
            sucesso: function (dados) {
                if (!dados.existe) {
                    bootalerta.erro("#alerta-erro", dados.texto)
                } else {
                    carregarEscalaProponente();
                }
            }
        });
    }

    function carregarEscalaProponente() {
        usuario = "<?php echo $sessao['usu']; ?>";
        usuarioNome = "<?php echo $sessao['pg_nome']; ?>";
        tipo = parseInt($("#tipo_escala").val());
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));

        ajax.sync.html({
            dados: {usuario: usuario, usuarioNome: usuarioNome, grupo: grupo, tipo: tipo, item: 'proponente'},
            endereco: 'carregar.php',
            reativar: true,
            sucesso: function (dados) {
                $("#proponente").html(dados);
            }
        });

        checarPublicacaoProponente();
    }

    function checarPublicacaoProponente() {
        $("#alerta-erro2").html("");
        usuario = "<?php echo $sessao['usu']; ?>";
        usuarioNome = "<?php echo $sessao['pg_nome']; ?>";
        tipo = parseInt($("#tipo_escala").val());
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        ajax.sync.obj({
            dados: {usuario: usuario, usuarioNome: usuarioNome, grupo: grupo, tipo: tipo, item: 'publicacao'},
            endereco: 'checar.php',
            sucesso: function (dados) {
                if (!dados.existe) {
                    bootalerta.erro("#alerta-erro2", dados.texto)
                } else {
                    if (dados.aviso) {
                        bootalerta.aviso("#alerta-erro2", dados.textoAviso);
                    }
                    carregarTurnos('proponente');
                    carregarEscalaIndividual('proponente');
                    carregarListaProposto();
                }
            }
        });
    }



    function carregarTurnos(pp) {
        escala = $("#escala-" + pp).val();
        ajax.sync.html({
            dados: {escala: escala, pp: pp, item: 'turnos'},
            endereco: 'carregar.php',
            reativar: true,
            sucesso: function (dados) {
                $("#turnos-" + pp).html(dados);
            }
        });
    }

    function  carregarEscalaIndividual(pp) {
        if (pp == 'proponente') {
            usuario = "<?php echo $sessao['usu']; ?>";
            usuarioNome = "<?php echo $sessao['pg_nome']; ?>";
        } else if (pp = 'proposto') {
            usuario = $("#usuario-proposto").val();
            usuarioNome = $("#usuario-proposto option:selected").html();
        }
        tipo = parseInt($("#tipo_escala").val());
        nomeTipo = $("#tipo_escala option:selected").html();
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        ajax.sync.html({
            dados: {grupo: grupo, tipo: tipo, nomeTipo: nomeTipo, usuario: usuario, nomeOperador: usuarioNome, botao: false, item: 'escala'},
            endereco: '<?php echo $sessao['raiz_html']; ?>modulos/escala/individual/carregar.php',
            sucesso: function (dados) {
                $("#escala-individual-" + pp).html(dados);
            }
        });
    }

    function carregarListaProposto() {
        tipo = parseInt($("#tipo_escala").val());
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        proponente = "<?php echo $sessao['usu']; ?>";

        ajax.sync.html({
            dados: {usuario: usuario, proponente: proponente, grupo: grupo, tipo: tipo, item: 'proposto'},
            endereco: 'carregar.php',
            reativar: true,
            sucesso: function (dados) {
                $("#proposto").html(dados);
            }
        });
    }

    function carregarEscalasProposto() {
        tipo = parseInt($("#tipo_escala").val());
        usuario = $("#usuario-proposto").val();
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        ajax.sync.html({
            dados: {usuario: usuario, grupo: grupo, tipo: tipo, item: 'proposto_escalas'},
            endereco: 'carregar.php',
            sucesso: function (dados) {
                $("#escalas-proposto").html(dados);
                carregarTurnos('proposto')
                carregarEscalaIndividual('proposto');
            }
        });
    }

    function checarPublicacaoProposto() {
        $("#alerta-erro3").html("");
        $("#escalas-proposto").html("");
        $("#turnos-proposto").html("");

        usuario = $("#usuario-proposto").val();
        usuarioNome = $("#usuario-proposto option:selected").html();
        tipo = parseInt($("#tipo_escala").val());
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));

        ajax.sync.obj({
            dados: {usuario: usuario, usuarioNome: usuarioNome, grupo: grupo, tipo: tipo, item: 'publicacao'},
            endereco: 'checar.php',
            sucesso: function (dados) {
                if (!dados.existe) {
                    bootalerta.erro("#alerta-erro3", dados.texto)
                    $("#btn-analisar").attr('style', 'display:none;');
                } else {
                    if (dados.aviso) {
                        bootalerta.aviso("#alerta-erro3", dados.textoAviso);
                    }
                    carregarEscalasProposto();
                    $("#btn-analisar").attr('style', 'display:block;');
                }
            }
        });
    }

    function analisarTroca() {
        $("#analise-proponente").html("");
        $("#analise-proposto").html("");
        $("#btn-salvar").attr("style", 'display:none;');

        proponente = $("#usuario-proponente").attr('usuario');
        nomePE = $("#usuario-proponente").html();
        escalaPE = $("#escala-proponente").val();
        legendaEscalaPE = $("#escala-proponente option:selected").html();
        diaPE = $("#dia-proponente").val();
        turnoPE = $("#turno-proponente").val();
        legendaTurnoPE = $("#turno-proponente option:selected").html();

        proposto = $("#usuario-proposto").val();
        nomePO = $("#usuario-proposto option:selected").html();
        escalaPO = $("#escala-proposto").val();
        legendaEscalaPO = $("#escala-proposto option:selected").html();
        diaPO = $("#dia-proposto").val();
        turnoPO = $("#turno-proposto").val();
        legendaTurnoPO = $("#turno-proposto option:selected").html();

        tipo = parseInt($("#tipo_escala").val());
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));

        ajax.sync.html({
            dados: {
                proponente: proponente,
                nomePE: nomePE,
                escalaPE: escalaPE,
                legendaEscalaPE: legendaEscalaPE,
                diaPE: diaPE,
                turnoPE: turnoPE,
                legendaTurnoPE: legendaTurnoPE,
                diaPO: diaPO,
                turnoPO: turnoPO,
                legendaTurnoPO: legendaTurnoPO,
                escalaPO: escalaPO,
                proposto: proposto,
                tipo: tipo,
                grupo: grupo,
                item: 'analise'
            },
            endereco: 'carregar.php',
            sucesso: function (dados) {
                $("#analise-proponente").html(dados);
            }
        });

        ajax.sync.html({
            dados: {
                proponente: proposto,
                nomePE: nomePO,
                escalaPE: escalaPO,
                legendaEscalaPE: legendaEscalaPO,
                diaPE: diaPO,
                turnoPE: turnoPO,
                legendaTurnoPE: legendaTurnoPO,
                diaPO: diaPE,
                turnoPO: turnoPE,
                legendaTurnoPO: legendaTurnoPE,
                escalaPO: escalaPE,
                proposto: proponente,
                tipo: tipo,
                grupo: grupo,
                item: 'analise'
            },
            endereco: 'carregar.php',
            sucesso: function (dados) {
                $("#analise-proposto").html(dados);

                ativarBtnSalvar = true;
                $(".analise-alert").each(function () {
                    if ($(this).hasClass('alert-danger')) {
                        ativarBtnSalvar = false;
                    }
                });
                if (ativarBtnSalvar) {
                    $("#btn-salvar").attr('style', 'display:block;');
                }
            }
        });
    }

    function salvarTroca() {
        proponente = $("#usuario-proponente").attr('usuario');
        escalaPE = $("#escala-proponente").val();
        diaPE = $("#dia-proponente").val();
        turnoPE = $("#turno-proponente").val();

        proposto = $("#usuario-proposto").val();
        escalaPO = $("#escala-proposto").val();
        diaPO = $("#dia-proposto").val();
        turnoPO = $("#turno-proposto").val();

        tipo = parseInt($("#tipo_escala").val());
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));

        ajax.sync.obj({
            dados: {
                proponente: proponente,
                escalaPE: escalaPE,
                diaPE: diaPE,
                turnoPE: turnoPE,
                proposto: proposto,
                escalaPO: escalaPO,
                diaPO: diaPO,
                turnoPO: turnoPO,
                grupo: grupo,
                tipo: tipo,
                item: 'troca'
            },
            endereco: 'inserir.php',
            sucesso: function (dados) {
                resetarAnalise();
                if (dados.id == 0) {
                    bootalerta.erro("#analise-proponente", "Houve um erro! A troca não foi lançada.");
                } else {
                    bootalerta.sucesso("#analise-proponente", "Troca lançada com sucesso!");
                    if (tipo == 2) {
                        efetivarTrocasPossiveis();
                    }
                }
            }
        });

    }
    function efetivarTrocasPossiveis() {
        tipo = parseInt($("#tipo_escala").val());
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        ajax.async.obj({
            dados: {grupo: grupo, tipo: tipo, item: 'lancar_trocas_possiveis'},
            endereco: 'carregar.php',
            sucesso: function (dados) {
                if (dados.qtd > 0) {
                    bootbox.alert("Foram efetivadas automaticamente (" + dados.qtd + ") trocas.")
                }
            }
        });
    }

    function resetarAnalise() {
        $("#analise-proponente").html("");
        $("#analise-proposto").html("");
        $("#btn-salvar").attr("style", "display:none;");
    }

    function resetarHTML() {
        $("#display_tipo_escala").attr('style', 'display:none;');
        $("#proponente").html("");
        $("#escala-individual-proponente").html("");
        $("#proposto").html("");
        $("#escala-individual-proposto").html("");
        $("#btn-analisar").attr("style", "display:none;");
        $("#btn-salvar").attr("style", "display:none;");
        $("#panel-lancadas").html("");
        $("#panel-concluidas").html("");
        $("#alerta-erro").html("");
    }

    function carregarTrocasLancadas() {
        if (definitiva) {
            $("#panel-lancadas").html("<div id='erro-lancadas'></div>")
            bootalerta.erro("#erro-lancadas", "Não é mais possível vizualizar trocas deste mês.")
        } else {
            tipo = parseInt($("#tipo_escala").val());
            grupo = parseInt($("#ano-mes-picker").attr('grupo'));
            proponente = "<?php echo $sessao['usu']; ?>";

            ajax.sync.html({
                dados: {tipo: tipo, grupo: grupo, proponente: proponente, item: 'lancadas'},
                endereco: 'carregar.php',
                sucesso: function (dados) {
                    $("#panel-lancadas").html(dados);
                }
            });
        }
    }

    function carregarTrocasConcluidas() {
        tipo = parseInt($("#tipo_escala").val()) == 4 ? 3 : parseInt($("#tipo_escala").val());
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        proponente = "<?php echo $sessao['usu']; ?>";

        ajax.sync.html({
            dados: {tipo: tipo, grupo: grupo, proponente: proponente, item: 'concluidas'},
            endereco: 'carregar.php',
            sucesso: function (dados) {
                $("#panel-concluidas").html(dados);
            }
        });
    }

    function excluirTroca(trocaID) {
        bootbox.confirm({
            message: "Deseja realmente cancelar esta troca?",
            buttons: {
                confirm: {
                    label: 'Sim',
                    className: 'btn-success'
                },
                cancel: {
                    label: 'Não',
                    className: 'btn-danger'
                }
            },
            callback: function (result) {
                if (result) {
                    bootbox.prompt({
                        title: "Motivo:",
                        inputType: 'text',
                        callback: function (result) {
                            if (result != null) {
                                ajax.async.obj({
                                    dados: {
                                        trocaID: trocaID,
                                        texto: result,
                                        item: 'excluir'
                                    },
                                    endereco: 'inserir.php',
                                    sucesso: function (dados) {
                                        if (dados[1].id > 0) {
                                            carregarTrocasLancadas();
                                        } else {
                                            bootbox.alert("Houve um erro na exclusão da troca.");
                                        }
                                    }
                                });
                            }
                        }
                    });
                }
            }
        });

    }

    $(document).on('change', '#tipo_escala', function () {
        resetarHTML();
        $("#panel-lancadas").html("");
        $("#panel-concluidas").html("");
        $("#display_tipo_escala").attr('style', 'display:block;');
        verificarSeDefinitva();
    });

    $(document).on('change', '#escala-proponente', function () {
        carregarTurnos('proponente');
        resetarAnalise();
    });

    $(document).on('change', '#usuario-proposto', function () {
        $("#btn-analisar").attr('style', 'display:none;');
        $("#escala-individual-proposto").html("");
        checarPublicacaoProposto();
        resetarAnalise();
    });

    $(document).on('change', '#escala-proposto', function () {
        carregarEscalaIndividual('proposto');
        carregarTurnos('proposto');
    });

    $(document).on('change', '.dia_turno', function () {
        resetarAnalise();
    });

</script>