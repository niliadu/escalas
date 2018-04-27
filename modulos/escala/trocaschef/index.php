<?php
//escala home
$temModulo = true;

/////////////////////////////////////////////////
session_start();
include $_SESSION['raiz'] . "funcoes.php";
acesso('escala', 1);
/////////////////////////////////////////////////
?>
<head>
    <meta charset="utf-8">
    <title>Trocas Chefia</title>
    <?php
    $itemMenu = 5;
    include $sessao['raiz'] . 'scripts_css.php';
    include $sessao['raiz'] . 'cabecalho.php';
    ?>
    <link rel="stylesheet" type="text/css" href='<?php echo $sessao['raiz_html'];?>bootstrap/css_proprio/individual_mini.css'>
</head>
<body>
    <div class="container">
        <div class="row collapse in" id='cabecalho_subs'>
            <div class=" col-xs-12">
                <h3>Trocas Chefia</h3>
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
        <div class="row">
            <div class="tabbable" id="tabs-trocas">
                <ul class="nav nav-tabs">
                    <li id='li-autorizadas' class="active">
                        <a href="#panel-autorizadas" data-toggle="tab" onclick="carregarTrocasAutorizadas();">Autorizadas</a>
                    </li>
                    <li id='li-concluidas'>
                        <a href="#panel-concluidas" data-toggle="tab" onclick="carregarTrocasConcluidas();">Concluídas</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="panel-autorizadas"></div>
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
            $("#panel-autorizadas").removeClass('active');
            $("#panel-concluidas").addClass('active');
            $("#li-autorizadas").removeClass('active');
            $("#li-concluidas").addClass('active');

        }
        idPanel = $('.tab-pane.active').attr('id');
        switch (idPanel) {
            case "panel-autorizadas":
                carregarTrocasAutorizadas();
                break;
            case "panel-concluidas":
                carregarTrocasConcluidas();
                break;
        }
    }

    function carregarTrocasAutorizadas() {
        if (definitiva) {
            $("#panel-autorizadas").html("<div id='erro-autorizadas'></div>")
            bootalerta.erro("#erro-autorizadas", "Não é mais possível vizualizar trocas deste mês.")
        } else {
            tipo = parseInt($("#tipo_escala").val());
            grupo = parseInt($("#ano-mes-picker").attr('grupo'));

            ajax.sync.html({
                dados: {tipo: tipo, grupo: grupo, item: 'autorizadas'},
                endereco: 'carregar.php',
                sucesso: function (dados) {
                    $("#panel-autorizadas").html(dados);
                }
            });
        }
    }

    function carregarTrocasConcluidas() {
        tipo = parseInt($("#tipo_escala").val()) == 4 ? 3 : parseInt($("#tipo_escala").val());
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));

        ajax.sync.html({
            dados: {tipo: tipo, grupo: grupo, item: 'concluidas'},
            endereco: 'carregar.php',
            sucesso: function (dados) {
                $("#panel-concluidas").html(dados);
            }
        });
    }

    function autorizarTroca(trocaID, obrigatorio) {
        ajax.sync.html({
            dados: {trocaID: trocaID, obrigatorio: obrigatorio, novoStatus: 3, item: 'justificativa'},
            endereco: 'carregar.php',
            sucesso: function (dados) {
                bootbox.dialog({
                    title: "Justificativa",
                    message: dados
                });
            }
        });

    }

    function excluirTroca(trocaID) {
        ajax.sync.html({
            dados: {trocaID: trocaID, obrigatorio: 1, novoStatus: 6, item: 'justificativa'},
            endereco: 'carregar.php',
            sucesso: function (dados) {
                bootbox.dialog({
                    title: "Justificativa",
                    message: dados
                });
            }
        });
    }

    function atualizarTroca(obrigatorio, trocaID, novoStatus) {
        texto = $("#texto-justificativa").val();
        tipo = $("#tipo_escala").val();
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        autReij = novoStatus == 3 ? "efetivação" : 'rejeição';

        if (obrigatorio && texto == "") {
            bootalerta.erro("#erro-justficativa", "É necessário justificar a " + autReij + " desta troca.");
        } else {
            ajax.async.obj({
                dados: {
                    trocaID: trocaID,
                    texto: texto,
                    status: novoStatus,
                    grupo: grupo,
                    tipo: tipo,
                    item: 'novo_status'
                },
                endereco: 'inserir.php',
                sucesso: function (dados) {
                    bootbox.hideAll();
                    if (dados[1].id > 0) {
                        carregarTrocasAutorizadas();
                    } else {
                        bootbox.alert("Houve um erro na " + autReij + " da troca.");
                    }
                }
            });
        }
    }

    function resetarHTML() {
        $("#display_tipo_escala").attr('style', 'display:none;');
        $("#panel-lancadas").html("");
        $("#panel-autorizada").html("");
        $("#panel-concluidas").html("");
        $("#alerta-erro").html("");
    }

    $(document).on('change', '#tipo_escala', function () {
        resetarHTML();
        $("#panel-lancadas").html("");
        $("#panel-concluidas").html("");
        $("#display_tipo_escala").attr('style', 'display:block;');
        verificarSeDefinitva();
    });
</script>