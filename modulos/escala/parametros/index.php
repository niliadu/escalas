<?php
//escala home
$temModulo = true;

/////////////SEGURANCA////////////////////////////////////
session_start();
include $_SESSION['raiz'] . "funcoes.php";
acesso('escala', 0);
/////////////////////////////////////////////////
?>
<head>
    <meta charset="utf-8">
    <title>Parâmetros</title>
    <?php
    $itemMenu = 3;
    include $sessao['raiz'] . 'scripts_css.php';
    include $sessao['raiz'] . 'cabecalho.php';
    ?>
</head>
<body>
    <div class="container">
        <div class="row">
            <div col-xs-12>
                <div class="col-xs-2" align="left" id='calendario'><?php include $sessao['raiz'] . 'calendario.php'; ?></div>
                <div class="col-xs-2 pull-right" id="loader" align="right"></div>
            </div>
            <div  class="col-xs-12">
                <div class="row">
                    <br>                    
                    <div id="display_param" class="col-xs-12" style="display: none;">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="tabbable" id="tabs-parametros">
                                    <ul class="nav nav-tabs">
                                        <li class="active">
                                            <a href="#panel-turnos" data-toggle="tab" onclick="carregarParametros('turnos');">Turnos</a>
                                        </li>
                                        <li>
                                            <a href="#panel-escalas" data-toggle="tab" onclick="carregarParametros('escalas');">Escalas</a>
                                        </li>
                                        <li>
                                            <a href="#panel-efetivo" data-toggle="tab" onclick="carregarParametros('efetivo');">Efetivo</a>
                                        </li>
                                        <li>
                                            <a href="#panel-restricoes" data-toggle="tab" onclick="carregarParametros('restricoes');">Restrições</a>
                                        </li>
                                        <li>
                                            <a href="#panel-risaer" data-toggle="tab" onclick="carregarParametros('risaer');">Serviços RISAER</a>
                                        </li>
                                        <li class="pull-right" id='limpar-li'>
                                            <a id='limpar-a' href="#" >Limpar Grupo</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="panel-turnos"></div>
                                        <div class="tab-pane" id="panel-escalas"></div>
                                        <div class="tab-pane" id="panel-efetivo"></div>
                                        <div class="tab-pane" id="panel-restricoes"></div>
                                        <div class="tab-pane" id="panel-risaer"></div>
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
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ativarPlugins();
    //datepicker para a seleção do mês ano//////////////////////////////////////////////////////

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
        mostrarParam();
    });
    mostrarParam();
    function mostrarParam() {
        $("#display_param").attr('style', 'display:none;')
        ano = parseInt($("#ano-mes-picker").attr('ano'));
        mes = parseInt($("#ano-mes-picker").attr('mes'));
        orgao = <?php echo $_SESSION['orgao_usu_id']; ?>;
        unidade = '<?php echo $_SESSION['unidade_usu_id']; ?>';
        mesNome = correcaoMes.meses[mes];
        //verifica se existe um parametro no bd para aquele mes-ano
        ajax.async.obj({
            dados: {ano: ano, mes: mes, orgao: orgao, unidade: unidade, item: 'par'},
            endereco: 'checar.php',
            sucesso: function (dados) {
                if (!dados.existe) {
                    mostrarDialogoCriacao(ano, mes, mesNome, orgao, unidade);
                } else {
                    $("#ano-mes-picker").attr('grupo', dados.grupo);
                    carregarParametros('turnos');
                    chegarSePodeLimparGrupo();
                }
            }
        });
    }

    function mostrarDialogoCriacao(ano, mes, mesNome, orgao, unidade) {
        bootbox.dialog({
            message: "Não existem paramêtros para as escalas de " + mesNome + " de " + ano + ".<br>O que deseja fazer?",
            buttons: {
                success: {
                    label: "Copiar existente",
                    className: "btn-primary",
                    callback: function () {
                        criarParametros("copiar", ano, mes, orgao, unidade);
                    }
                },
                danger: {
                    label: "Criar em branco",
                    className: "btn-danger",
                    callback: function () {
                        bootbox.confirm("Deseja realmente criar os parâmetros em branco?", function (confirmacao) {
                            if (confirmacao) {
                                criarParametros("branco", ano, mes, orgao, unidade);
                            } else {
                                mostrarDialogoCriacao(ano, mes, mesNome, orgao, unidade);
                            }
                        });
                    }
                }
            }
        });
    }
    function criarParametros(escolha, ano, mes, orgao, unidade) {
        ajax.async.obj({
            dados: {ano: ano, mes: mes, orgao: orgao, unidade: unidade, escolha: escolha},
            endereco: 'criar.php',
            sucesso: function (dados) {
                if (!dados.existe) {
                    bootbox.alert("Não foram encontrados parâmentros já existentes.<br>Os parâmetros serão criados em branco.")
                    criarParametros("branco", ano, mes, orgao);
                } else {
                    $("#ano-mes-picker").attr('grupo', dados.grupo);
                    carregarParametros('turnos');
                    chegarSePodeLimparGrupo();
                }
            }
        });
    }

    function carregarParametros(tipo) {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        ajax.sync.html({
            dados: {grupo: grupo, item: 'lista_' + tipo},
            endereco: 'carregar.php',
            reativar: true,
            sucesso: function (dados) {
                $("#panel-" + tipo).html(dados);
            }
        });
        $("#display_param").attr('style', "display:block;");
    }


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    function adicionarTurn() {
        ajax.sync.html({
            dados: {item: 'add_turno'},
            endereco: 'carregar.php',
            reativar: true,
            sucesso: function (dados) {
                bootbox.dialog({
                    title: "Adicionar um Turno",
                    message: dados
                });
                $('.clockpicker').clockpicker({
                    donetext: 'Ok',
                });
                $(".clockpicker").on("change", function () {
                    hora = $(this).children('input').val();
                    $(this).children('.hora').html(hora);
                });
            }
        });
    }

    function adicionarEsc() {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        ajax.sync.html({
            dados: {grupo: grupo, item: 'add_escala'},
            endereco: 'carregar.php',
            reativar: true,
            sucesso: function (dados) {
                bootbox.dialog({
                    title: "Adicionar uma Escala",
                    message: dados
                });
                $("#svc").slider({min: 0, max: 31, step: 1, value: 19, tooltip: 'always'});
                $("#ch").slider({min: 100, max: 200, range: true, value: [120, 180], tooltip: 'always'});
            }
        });
    }

    function adicionarEfetivo() {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        ano = parseInt($("#ano-mes-picker").attr('ano'));
        mes = parseInt($("#ano-mes-picker").attr('mes'));
        ajax.sync.html({
            dados: {ano: ano, mes: mes, grupo: grupo, item: 'add_efetivo'},
            endereco: 'carregar.php',
            reativar: true,
            sucesso: function (dados) {
                bootbox.dialog({
                    title: "Adicionar um Efetivo",
                    message: dados
                });
            }
        });
    }
    function adicionarComb() {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        ajax.sync.html({
            dados: {grupo: grupo, item: 'add_combinacao'},
            endereco: 'carregar.php',
            reativar: true,
            sucesso: function (dados) {
                bootbox.dialog({
                    title: "Adicionar uma Combinação de turnos",
                    message: dados
                });
            }
        });
    }

    function adicionarRISAER() {
        ajax.sync.html({
            dados: {item: 'add_risaer'},
            endereco: 'carregar.php',
            reativar: true,
            sucesso: function (dados) {
                bootbox.dialog({
                    title: "Adicionar um Serviço RISAER",
                    message: dados
                });
            }
        });
    }
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    function gravarTurno() {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        nome = $("#nome-turno").val().toUpperCase();
        legenda = $("#legenda-turno").val();
        inicio = $("#inicio-turno").val();
        termino = $("#termino-turno").val();
        etapa = $("#etapa-turno").val();
        periodo = $("#periodo-turno").val();
        posNoturno = $("#pos_noturno-turno").val();
        if (nome == "") {
            bootalerta.erro("#alerta-erro", "O nome do turno não pode ficar em branco");
        } else if (legenda == "") {
            bootalerta.erro("#alerta-erro", "A legenda do turno não pode ficar em branco");
        } else if (legenda.toUpperCase() == "IS") {
            bootalerta.erro("#alerta-erro", "A legenda do turno não pode ter este valor pois o mesmo está alocado para o sistema.");
        } else if (inicio == "") {
            bootalerta.erro("#alerta-erro", "Por favor selecione o horário de início do turno.");
        } else if (termino == "") {
            bootalerta.erro("#alerta-erro", "Por favor selecione o horário de término do turno.");
        } else {
            $("#alerta-erro").html("");
            ajax.async.obj({
                dados: {
                    nome: nome,
                    legenda: legenda,
                    inicio: inicio,
                    termino: termino,
                    etapa: etapa,
                    grupo: grupo,
                    item: "add_turno"
                },
                endereco: 'checar.php',
                sucesso: function (dados) {
                    if (dados.existe) {
                        bootalerta.erro("#alerta-erro", dados.texto);
                    } else {
                        ajax.async.obj({
                            dados: {nome: nome, legenda: legenda, inicio: inicio, termino: termino, etapa: etapa, grupo: grupo, periodo: periodo, posNoturno: posNoturno, item: "add_turno"},
                            endereco: 'inserir.php',
                            sucesso: function (dados) {
                                bootbox.hideAll();
                                bootbox.alert("Turno salvo com sucesso!");
                                ajax.async.html({
                                    dados: {grupo: grupo, item: 'lista_turnos'},
                                    endereco: 'carregar.php',
                                    sucesso: function (dados) {
                                        $("#panel-turnos").html(dados);
                                        $("#display_param").attr('style', "display:block;");
                                    }
                                });
                            }
                        });
                    }
                }
            });
        }
    }

    function gravarEscala() {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        nome = $("#nome-esc").val().toUpperCase();
        legenda = $("#legenda-esc").val().toUpperCase();
        turnos = $("#turno-esc").val();
        ch = $("#ch").val().split(",");
        svc = $("#svc").val();
        if (nome == "") {
            bootalerta.erro("#alerta-erro", "O nome da escala não pode ficar em branco");
        } else if (legenda == "") {
            bootalerta.erro("#alerta-erro", "A legenda da escala não pode ficar em branco");
        } else if (turnos == null) {
            bootalerta.erro("#alerta-erro", "É necessário selecionar pelo menos um turno");
        } else {
            $("#alerta-erro").html("");
            ajax.async.obj({
                dados: {nome: nome, legenda: legenda, grupo: grupo, item: "add_escala"},
                endereco: 'checar.php',
                sucesso: function (dados) {
                    if (dados.existe) {
                        bootalerta.erro("#alerta-erro", dados.texto);
                    } else {
                        ajax.async.obj({
                            dados: {nome: nome, legenda: legenda, turnos: turnos, ch: ch, grupo: grupo, item: "add_escala", svc: svc},
                            endereco: 'inserir.php',
                            sucesso: function (dados) {
                                bootbox.hideAll();
                                bootbox.alert("Escala salva com sucesso!");
                                ajax.async.html({
                                    dados: {grupo: grupo, item: 'lista_escalas'},
                                    endereco: 'carregar.php',
                                    sucesso: function (dados) {
                                        $("#panel-escalas").html(dados);
                                        $("#display_param").attr('style', "display:block;");
                                    }
                                });
                            }
                        });
                    }
                }
            });
        }
    }

    function gravarEfetivo() {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        usuario = $("#operador-efetivo").val();
        legenda = $("#legenda-efetivo").val().toUpperCase();
        escalas = $("#escalas-efetivo").val();
        funcao = $("#funcao-efetivo").val();
        manutencao = $("#manutencao-efetivo").val();
        if (usuario == "") {
            bootalerta.erro("#alerta-erro", "O nome do usuário não pode ficar em branco");
        } else if (legenda == "") {
            bootalerta.erro("#alerta-erro", "A legenda da escala não pode ficar em branco");
        } else if (escalas == null) {
            bootalerta.erro("#alerta-erro", "É necessário selecionar pelo menos uma escala");
        } else if (funcao == "") {
            bootalerta.erro("#alerta-erro", "A função não pode ficar em branco");
        } else {
            $("#alerta-erro").html("");
            ajax.async.obj({
                dados: {usuario: usuario, legenda: legenda, grupo: grupo, item: "add_efetivo"},
                endereco: 'checar.php',
                sucesso: function (dados) {
                    if (dados.existe) {
                        bootalerta.erro("#alerta-erro", dados.texto);
                    } else {
                        ajax.async.obj({
                            dados: {usuario: usuario, legenda: legenda, escalas: escalas, funcao: funcao, grupo: grupo, item: "add_efetivo", manutencao: manutencao},
                            endereco: 'inserir.php',
                            sucesso: function (dados) {
                                bootbox.hideAll();
                                bootbox.alert("Operador salvo no efetivo com sucesso!");
                                ajax.async.html({
                                    dados: {grupo: grupo, item: 'lista_efetivo'},
                                    endereco: 'carregar.php',
                                    reativar: true,
                                    sucesso: function (dados) {
                                        $("#panel-efetivo").html(dados);
                                        $("#display_param").attr('style', "display:block;");
                                    }
                                });
                            }
                        });
                    }
                }
            });
        }
    }

    function gravarCombinacao() {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        turno1 = $("#turno1_restricoes").val();
        turno2 = $("#turno2_restricoes").val();
        if (turno1 == "" || turno2 == "") {
            bootalerta.erro("#alerta-erro", "Deve-se selecionar os dois turnos");
        } else if (turno1 == turno2) {
            bootalerta.erro("#alerta-erro", "Selecione dois turnos distintos");
        } else {
            $("#alerta-erro").html("");
            ajax.async.obj({
                dados: {turno1: turno1, turno2: turno2, grupo: grupo, item: "add_combinacao"},
                endereco: 'checar.php',
                sucesso: function (dados) {
                    if (dados.existe) {
                        bootalerta.erro("#alerta-erro", dados.texto);
                    } else {
                        ajax.async.obj({
                            dados: {turno1: turno1, turno2: turno2, grupo: grupo, item: "add_combinacao"},
                            endereco: 'inserir.php',
                            sucesso: function (dados) {
                                bootbox.hideAll();
                                bootbox.alert("Combinação adiconada com sucesso!");
                                ajax.async.html({
                                    dados: {grupo: grupo, item: 'lista_restricoes'},
                                    endereco: 'carregar.php',
                                    sucesso: function (dados) {
                                        $("#panel-restricoes").html(dados);
                                        $("#display_param").attr('style', "display:block;");
                                    }
                                });
                            }
                        });
                    }
                }
            });
        }
    }

    function gravarRISAER() {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        nome = $("#nome-risaer").val().toUpperCase();
        legenda = $("#legenda-risaer").val();
        inicio = $("#inicio-risaer").val();
        termino = $("#termino-risaer").val();
        maior24 = $("#maiorq24").val();
        etapa = $("#etapa-risaer").val();
        dant = $("#danterior-risaer").val();
        dpost = $("#dposterior-risaer").val();

        if (nome == "") {
            bootalerta.erro("#alerta-erro", "O nome do serviço não pode ficar em branco");
        } else if (legenda == "") {
            bootalerta.erro("#alerta-erro", "A legenda do serviço não pode ficar em branco");
        } else if (legenda.toUpperCase() == "IS") {
            bootalerta.erro("#alerta-erro", "A legenda do serviço não pode ter este valor pois o mesmo está alocado para o sistema.");
        } else if (inicio == "") {
            bootalerta.erro("#alerta-erro", "Por favor selecione o horário de início do serviço.");
        } else if (termino == "") {
            bootalerta.erro("#alerta-erro", "Por favor selecione o horário de término do serviço.");
        } else {
            $("#alerta-erro").html("");
            ajax.async.obj({
                dados: {
                    nome: nome,
                    legenda: legenda,
                    grupo: grupo,
                    item: "add_risaer"
                },
                endereco: 'checar.php',
                sucesso: function (dados) {
                    if (dados.existe) {
                        bootalerta.erro("#alerta-erro", dados.texto);
                    } else {
                        ajax.async.obj({
                            dados: {
                                nome: nome,
                                legenda: legenda,
                                inicio: inicio,
                                termino: termino,
                                maior24: maior24,
                                etapa: etapa,
                                grupo: grupo,
                                dant: dant,
                                dpost: dpost,
                                item: "add_risaer"
                            },
                            endereco: 'inserir.php',
                            sucesso: function (dados) {
                                bootbox.hideAll();
                                bootbox.alert("Serviço salvo com sucesso!");
                                ajax.async.html({
                                    dados: {grupo: grupo, item: 'lista_risaer'},
                                    endereco: 'carregar.php',
                                    sucesso: function (dados) {
                                        $("#panel-risaer").html(dados);
                                        $("#display_param").attr('style', "display:block;");
                                    }
                                });
                            }
                        });
                    }
                }
            });
        }
    }
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    function edtTurn(id) {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        ajax.async.html({
            dados: {grupo: grupo, item: 'editar_turno', id: id},
            endereco: 'carregar.php',
            reativar: true,
            sucesso: function (dados) {
                bootbox.dialog({
                    title: "Editar Turno",
                    message: dados
                });
            }
        });
    }

    function edtTipoEsc(id) {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        ajax.async.html({
            dados: {grupo: grupo, id: id, item: 'editar_escala'},
            endereco: 'carregar.php',
            reativar: true,
            sucesso: function (dados) {
                bootbox.dialog({
                    title: "Editar Escala",
                    message: dados
                });
                $("#svc").slider({});
                $("#ch").slider({});
            }
        });
    }

    function edtEfetivo(id) {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        ajax.sync.html({
            dados: {grupo: grupo, item: 'editar_efetivo', id: id},
            endereco: 'carregar.php',
            reativar: true,
            sucesso: function (dados) {
                bootbox.dialog({
                    title: "Editar Efetivo",
                    message: dados
                });
            }
        });
    }
    function edtRest() {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        ajax.sync.html({
            dados: {grupo: grupo, item: 'editar_restricoes'},
            endereco: 'carregar.php',
            reativar: true,
            sucesso: function (dados) {
                bootbox.dialog({
                    title: "Editar Restrições",
                    message: dados
                });
            }
        });
    }

    function edtComb(id) {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        ajax.async.html({
            dados: {grupo: grupo, item: 'editar_combinacao', id: id},
            endereco: 'carregar.php',
            reativar: true,
            sucesso: function (dados) {
                bootbox.dialog({
                    title: "Editar Combinação",
                    message: dados
                });
            }
        });
    }

    function edtRISAER(id) {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        ajax.async.html({
            dados: {grupo: grupo, item: 'editar_risaer', id: id},
            endereco: 'carregar.php',
            reativar: true,
            sucesso: function (dados) {
                bootbox.dialog({
                    title: "Editar Turno",
                    message: dados
                });
            }
        });
    }
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    function alterarTurno(id) {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        nome = $("#nome-turno").val().toUpperCase();
        legenda = $("#legenda-turno").val();
        inicio = $("#inicio-turno").val();
        termino = $("#termino-turno").val();
        etapa = $("#etapa-turno").val();
        periodo = $("#periodo-turno").val();
        posNoturno = $("#pos_noturno-turno").val();
        if (nome == "") {
            bootalerta.erro("#alerta-erro", "O nome do turno não pode ficar em branco");
        } else if (legenda == "") {
            bootalerta.erro("#alerta-erro", "A legenda do turno não pode ficar em branco");
        } else if (legenda.toUpperCase() == "A" || legenda.toUpperCase() == "IS") {
            bootalerta.erro("#alerta-erro", "A legenda do turno não pode ter este valor pois o mesmo está alocado para o sistema.");
        } else if (inicio == "") {
            bootalerta.erro("#alerta-erro", "Por favor selecione o horário de início do turno.");
        } else if (termino == "") {
            bootalerta.erro("#alerta-erro", "Por favor selecione o horário de término do turno.");
        } else {
            $("#alerta-erro").html("");
            ajax.async.obj({
                dados: {nome: nome, legenda: legenda, grupo: grupo, item: "alterar_turno", id: id},
                endereco: 'checar.php',
                sucesso: function (dados) {
                    if (dados.existe) {
                        bootalerta.erro("#alerta-erro", dados.texto);
                    } else {
                        ajax.async.obj({
                            dados: {nome: nome, legenda: legenda, inicio: inicio, termino: termino, etapa: etapa, periodo: periodo, posNoturno: posNoturno, item: "alterar_turno", id: id},
                            endereco: 'alterar.php',
                            sucesso: function (dados) {
                                bootbox.hideAll();
                                bootbox.alert("Turno alterado com sucesso!");
                                ajax.async.html({
                                    dados: {grupo: grupo, item: 'lista_turnos'},
                                    endereco: 'carregar.php',
                                    sucesso: function (dados) {
                                        $("#panel-turnos").html(dados);
                                        $("#display_param").attr('style', "display:block;");
                                    }
                                });
                            }
                        });
                    }
                }
            });
        }
    }

    function alterarEscala(id) {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        nome = $("#nome-esc").val().toUpperCase();
        legenda = $("#legenda-esc").val().toUpperCase();
        turnos = $("#turno-esc").val();
        ch = $("#ch").val().split(",");
        svc = $("#svc").val()
        if (nome == "") {
            bootalerta.erro("#alerta-erro", "O nome da escala não pode ficar em branco");
        } else if (legenda == "") {
            bootalerta.erro("#alerta-erro", "A legenda da escala não pode ficar em branco");
        } else if (turnos == null) {
            bootalerta.erro("#alerta-erro", "É necessário selecionar pelo menos um turno");
        } else {
            $("#alerta-erro").html("");
            ajax.async.obj({
                dados: {nome: nome, legenda: legenda, grupo: grupo, item: "alterar_escala", id: id},
                endereco: 'checar.php',
                sucesso: function (dados) {
                    if (dados.existe) {
                        bootalerta.erro("#alerta-erro", dados.texto);
                    } else {
                        ajax.async.obj({
                            dados: {nome: nome, legenda: legenda, turnos: turnos, ch: ch, item: "alterar_escala", id: id, grupo: grupo, svc: svc},
                            endereco: 'alterar.php',
                            sucesso: function (dados) {
                                bootbox.hideAll();
                                bootbox.alert("Turno alterado com sucesso!");
                                ajax.async.html({
                                    dados: {grupo: grupo, item: 'lista_escalas'},
                                    endereco: 'carregar.php',
                                    sucesso: function (dados) {
                                        $("#panel-escalas").html(dados);
                                        $("#display_param").attr('style', "display:block;");
                                    }
                                });
                            }
                        });
                    }
                }
            });
        }
    }

    function alterarEfetivo(id, alterar) {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        legenda = $("#legenda-efetivo").val().toUpperCase();
        escalas = $("#escalas-efetivo").val();
        funcao = $("#funcao-efetivo").val();
        manutencao = $("#manutencao-efetivo").val();
        if (!alterar) {
            if (legenda == "") {
                bootalerta.erro("#alerta-erro", "A legenda do operador não pode ficar em branco");
            } else if (escalas == null) {
                bootalerta.erro("#alerta-erro", "É necessário selecionar pelo menos uma escala");
            } else if (funcao == "") {
                bootalerta.erro("#alerta-erro", "É necessário selecionar pelo menos uma função");
            } else {
                $("#alerta-erro").html("");
                alterar = true;
                ajax.sync.obj({
                    dados: {legenda: legenda, grupo: grupo, escalas: escalas, item: "alterar_efetivo", id: id},
                    endereco: 'checar.php',
                    sucesso: function (dados) {
                        if (dados.existe) {
                            bootalerta.erro("#alerta-erro", dados.texto);
                            alterar = false;
                        } else if (dados.aviso) {
                            bootbox.confirm(
                                    dados.texto,
                                    function (result) {
                                        if (result) {
                                            alterarEfetivo(id, true);
                                        }
                                    }
                            );
                        } else {
                            alterarEfetivo(id, true);
                        }
                    }
                });
            }
        } else {
            ajax.async.obj({
                dados: {legenda: legenda, escalas: escalas, funcao: funcao, item: "alterar_efetivo", id: id, grupo: grupo, manutencao: manutencao},
                endereco: 'alterar.php',
                sucesso: function (dados) {
                    bootbox.hideAll();
                    bootbox.alert("Operador alterado com sucesso!");
                    ajax.async.html({
                        dados: {grupo: grupo, item: 'lista_efetivo'},
                        endereco: 'carregar.php',
                        reativar: true,
                        sucesso: function (dados) {
                            $("#panel-efetivo").html(dados);
                            $("#display_param").attr('style', "display:block;");
                        }
                    });
                }
            });
        }
    }

    function alterarConsec() {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        folgas = $("#folgas_restricoes").val();
        trocas = $("#trocas_restricoes").val();
        ajax.async.obj({
            dados: {
                grupo: grupo,
                folgas: folgas,
                trocas: trocas,
                item: "alterar_consecutivos"
            },
            endereco: 'alterar.php',
            sucesso: function (dados) {
                bootbox.hideAll();
                bootbox.alert("Valores alterados com sucesso!");
                ajax.async.html({
                    dados: {grupo: grupo, item: 'lista_restricoes'},
                    endereco: 'carregar.php',
                    sucesso: function (dados) {
                        $("#panel-restricoes").html(dados);
                        $("#display_param").attr('style', "display:block;");
                    }
                });
            }
        });
    }

    function alterarCombinacao(id) {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        turno1 = $("#turno1_restricoes").val();
        turno2 = $("#turno2_restricoes").val();
        if (turno1 == turno2) {
            bootalerta.erro("#alerta-erro", "Selecione dois turnos distintos");
        } else {
            $("#alerta-erro").html("");
            ajax.async.obj({
                dados: {turno1: turno1, turno2: turno2, grupo: grupo, item: "alterar_combinacao", id: id},
                endereco: 'checar.php',
                sucesso: function (dados) {
                    if (dados.existe) {
                        bootalerta.erro("#alerta-erro", dados.texto);
                    } else {
                        ajax.async.obj({
                            dados: {turno1: turno1, turno2: turno2, id: id, item: "alterar_combinacao"},
                            endereco: 'alterar.php',
                            sucesso: function (dados) {
                                bootbox.hideAll();
                                bootbox.alert("Combinação alterada com sucesso!");
                                ajax.async.html({
                                    dados: {grupo: grupo, item: 'lista_restricoes'},
                                    endereco: 'carregar.php',
                                    sucesso: function (dados) {
                                        $("#panel-restricoes").html(dados);
                                        $("#display_param").attr('style', "display:block;");
                                    }
                                });
                            }
                        });
                    }
                }
            });
        }
    }

    function alterarRISAER(id) {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        nome = $("#nome-risaer").val().toUpperCase();
        legenda = $("#legenda-risaer").val();
        inicio = $("#inicio-risaer").val();
        termino = $("#termino-risaer").val();
        maior24 = $("#maiorq24").val();
        etapa = $("#etapa-risaer").val();
        dant = $("#danterior-risaer").val();
        dpost = $("#dposterior-risaer").val();
        if (nome == "") {
            bootalerta.erro("#alerta-erro", "O nome do serviço não pode ficar em branco");
        } else if (legenda == "") {
            bootalerta.erro("#alerta-erro", "A legenda do serviço não pode ficar em branco");
        } else if (legenda.toUpperCase() == "IS") {
            bootalerta.erro("#alerta-erro", "A legenda do serviço não pode ter este valor pois o mesmo está alocado para o sistema.");
        } else if (inicio == "") {
            bootalerta.erro("#alerta-erro", "Por favor selecione o horário de início do serviço.");
        } else if (termino == "") {
            bootalerta.erro("#alerta-erro", "Por favor selecione o horário de término do serviço.");
        } else {
            $("#alerta-erro").html("");
            ajax.async.obj({
                dados: {
                    nome: nome,
                    legenda: legenda,
                    grupo: grupo,
                    item: "alterar_risaer"
                },
                endereco: 'checar.php',
                sucesso: function (dados) {
                    if (dados.existe) {
                        bootalerta.erro("#alerta-erro", dados.texto);
                    } else {
                        ajax.async.obj({
                            dados: {
                                id: id,
                                nome: nome,
                                legenda: legenda,
                                inicio: inicio,
                                termino: termino,
                                maior24: maior24,
                                etapa: etapa,
                                dant: dant,
                                dpost: dpost,
                                item: "alterar_risaer"
                            },
                            endereco: 'alterar.php',
                            sucesso: function (dados) {
                                bootbox.hideAll();
                                bootbox.alert("Serviço alterado com sucesso!");
                                ajax.async.html({
                                    dados: {grupo: grupo, item: 'lista_risaer'},
                                    endereco: 'carregar.php',
                                    sucesso: function (dados) {
                                        $("#panel-risaer").html(dados);
                                        $("#display_param").attr('style', "display:block;");
                                    }
                                });
                            }
                        });
                    }
                }
            });
        }
    }
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    function delTurn(id, legenda) {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        bootbox.confirm("Tem certeza que deseja deletar o Turno " + legenda + "?", function (result) {
            if (result) {
                ajax.async.obj({
                    dados: {id: id, item: "remover_turno"},
                    endereco: 'checar.php',
                    sucesso: function (dados) {
                        if (dados.existe) {
                            bootbox.alert(dados.texto);
                        } else {
                            ajax.async.obj({
                                dados: {id: id, item: "turno"},
                                endereco: 'remover.php',
                                sucesso: function (dados) {
                                    bootbox.alert("Turno Removido com sucesso!");
                                    ajax.sync.html({
                                        dados: {grupo: grupo, item: 'lista_turnos'},
                                        endereco: 'carregar.php',
                                        sucesso: function (dados) {
                                            $("#panel-turnos").html(dados);
                                            $("#display_param").attr('style', "display:block;");
                                        }
                                    });

                                }
                            });
                        }
                    }
                });
            }
        });
    }

    function delTipoEsc(id, nome) {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        bootbox.confirm("Tem certeza que deseja deletar a Escala " + nome + "?", function (result) {
            if (result) {
                ajax.async.obj({
                    dados: {id: id, item: "escala"},
                    endereco: 'remover.php',
                    sucesso: function (dados) {
                        bootbox.alert("Escala Removida com sucesso!");
                        ajax.sync.html({
                            dados: {grupo: grupo, item: 'lista_escalas'},
                            endereco: 'carregar.php',
                            sucesso: function (dados) {
                                $("#panel-escalas").html(dados);
                                $("#display_param").attr('style', "display:block;");
                            }
                        });
                    }
                });
            }
        });
    }

    function delEfetivo(id, nome) {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        bootbox.confirm("Tem certeza que deseja deletar " + nome + "?", function (result) {
            if (result) {
                ajax.async.obj({
                    dados: {id: id, item: "efetivo"},
                    endereco: 'remover.php',
                    sucesso: function (dados) {
                        bootbox.alert("Operador removido com sucesso!");
                        ajax.async.html({
                            dados: {grupo: grupo, item: 'lista_efetivo'},
                            endereco: 'carregar.php',
                            sucesso: function (dados) {
                                $("#panel-efetivo").html(dados);
                                $("#display_param").attr('style', "display:block;");
                            }
                        });
                    }
                });
            }
        });
    }

    function delComb(id) {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        bootbox.confirm("Tem certeza que deseja deletar a combinação de turnos selecionada?", function (result) {
            if (result) {
                ajax.async.obj({
                    dados: {id: id, item: "restricao"},
                    endereco: 'remover.php',
                    sucesso: function (dados) {
                        bootbox.alert("Combinação removida com sucesso!");
                        ajax.async.html({
                            dados: {grupo: grupo, item: 'lista_restricoes'},
                            endereco: 'carregar.php',
                            sucesso: function (dados) {
                                $("#panel-restricoes").html(dados);
                                $("#display_param").attr('style', "display:block;");
                            }
                        });
                    }
                });
            }
        });
    }

    function delRISAER(id, legenda) {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        bootbox.confirm("Tem certeza que deseja deletar o serviço " + legenda + "?", function (result) {
            if (result) {
                ajax.async.obj({
                    dados: {id: id, item: "risaer"},
                    endereco: 'remover.php',
                    sucesso: function (dados) {
                        bootbox.alert("Serviço removido com sucesso!");
                        ajax.sync.html({
                            dados: {grupo: grupo, item: 'lista_risaer'},
                            endereco: 'carregar.php',
                            sucesso: function (dados) {
                                $("#panel-risaer").html(dados);
                                $("#display_param").attr('style', "display:block;");
                            }
                        });

                    }
                });
            }
        });
    }
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    function chegarSePodeLimparGrupo() {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        ajax.async.obj({
            dados: {grupo: grupo, item: 'limpar'},
            endereco: 'checar.php',
            sucesso: function (dados) {
                if (dados.bloquear) {
                    $("#limpar-li").addClass('disabled');
                    $("#limpar-a").removeAttr('onclick');
                    $("#limpar-a").attr('title', 'Essa função fica indisponível após a criação da escala PREVISTA');
                } else {
                    $("#limpar-li").removeClass('disabled');
                    $("#limpar-a").attr('onclick', 'limparGrupo();');
                    $("#limpar-a").attr('title', 'Exclui todas as informações do grup. Incluindo serviços alocados.');
                }
            }
        });
    }

    function limparGrupo() {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        ano = $("#ano-mes-picker").attr('ano');
        mes = $("#ano-mes-picker").attr('mes');
        mesN = correcaoMes.meses[mes];

        bootbox.confirm("Todas as informações existentes do mês de " + mesN + " de " + ano + " serão deletadas." +
                "<br>Estas ação não poderá ser desfeita." +
                "<br><br>Tem certeza que deseja deletar tudo deste grupo?", function (result) {
                    if (result) {
                        ajax.async.obj({
                            dados: {id: grupo, item: "grupo"},
                            endereco: 'remover.php',
                            sucesso: function (dados) {
                                bootbox.alert({
                                    message: "Informações do grupo removidas com sucesso.",
                                    callback: function () {
                                        window.location.reload();
                                    }
                                });
                            }
                        });
                    }
                });
    }
</script>