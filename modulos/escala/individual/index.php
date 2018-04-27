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
    <title>Escala Individual</title>
    <?php
    $itemMenu = 0;
    include $sessao['raiz'] . 'scripts_css.php';
    include $sessao['raiz'] . 'cabecalho.php';
    ?>
</head>
<body>
    <div class="container">
        <div class="row collapse in" id='cabecalho_subs'>
            <div class=" col-xs-12">
                <h3>Escala Individual</h3>
            </div>
        </div>
        <div class="row">
            <div col-xs-12>
                <div class="col-xs-3" align="left" id='calendario'>
                    <?php include $sessao['raiz'] . 'calendario.php'; ?>
                </div>
                <div id="display_tipo_escala" class="row col-xs-2 y-overflow" style="display: none;"></div>

                <div class="col-xs-2 pull-right" id="loader" align="right"></div>
            </div>
        </div>
        <hr style="margin-bottom: 7px; margin-top: 3px;">
        <div id="display_operadores" class="row col-xs-6 y-overflow"></div>
        <br>
        <div class="row">
            <div class="col-xs-12" id="alerta-erro"></div>          
        </div>
        <br>
        <div class="row">
            <div class="col-xs-8" id="escala"></div>
            <div class="col-xs-4" id="div_infos"></div>
        </div>
</body>

<script>

///////////////////////////
    ativarPlugins();

    $("#ano-mes-picker").datetimepicker().on('changeDate', function() {
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
        $("#alerta-erro").html("");
        $("#escala").html("");
        $("#display_operadores").html("");
        $("#display_tipo_escala").attr('style', 'display:none;')
        $("#div_infos").html('');

        ano = parseInt($("#ano-mes-picker").attr('ano'));
        mes = parseInt($("#ano-mes-picker").attr('mes'));
        orgao = "<?php echo $_SESSION['orgao_usu_id']; ?>";
        mesNome = correcaoMes.meses[mes];
        ajax.sync.obj({
            dados: {ano: ano, mes: mes, orgao: orgao, item: 'tipos_escala_param'},
            endereco: 'checar.php',
            sucesso: function(dados) {
                if (!dados.existe) {
                    bootbox.alert("Ainda não existem escalas para visualização no mês em questão");
                } else {
                    carregarTiposEscala(dados.grupo);
                }
            }
        });
        loader.hide("#loader");
    }

    function carregarTiposEscala(grupo) {
        $("#ano-mes-picker").attr('grupo', grupo);
        ajax.async.obj({
            dados: {grupo: grupo, item: 'tipos_escala'},
            endereco: 'checar.php',
            sucesso: function(dados) {
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
            reativar: true,
            sucesso: function(dados) {              
                $("#display_tipo_escala").html(dados);
                ativarPlugins();
                $("#display_tipo_escala").attr('style', "display:block;");
                carregarListaOperadores();
            }
        });
    }

    function carregarListaOperadores() {
        $("#alerta-erro").html("");
        $("#escala").html("");
        $("#div_infos").html('');

        ajax.sync.html({
            dados: {item: 'lista_operadores'},
            endereco: 'carregar.php',
            reativar: true,
            sucesso: function(dados) {
                $("#display_operadores").html(dados);

            }
        });
        pedirEscalaIndividual();
    }

    $(document).on('change', '#tipo_escala', function() {
        carregarListaOperadores();
    });

    $(document).on('change', '#operador', function() {
        pedirEscalaIndividual(false);
    });

    function pedirEscalaIndividual() {
        $("#alerta-erro").html("");
        $("#escala").html("");
        $("#div_infos").html('');

        usuario = $("#operador").val();
        if (usuario != "") {

            nomeOperador = $("#operador option:selected").html();
            tipo = parseInt($("#tipo_escala").val());
            nomeTipo = $("#tipo_escala option:selected").html();
            grupo = parseInt($("#ano-mes-picker").attr('grupo'));
            ajax.async.obj({
                dados: {grupo: grupo, tipo: tipo, nomeOperador: nomeOperador, usuario: usuario, item: 'escala'},
                endereco: 'checar.php',
                sucesso: function(dados) {
                    if (dados.existe) {
                        bootalerta.erro("#alerta-erro", dados.texto);
                    } else {
                        if (dados.aviso) {
                            bootalerta.aviso("#alerta-erro", dados.textoAviso);
                        }
                        carregarEscalaIndividual(usuario, nomeOperador, grupo, tipo, nomeTipo);
                    }
                }
            });
        }
    }


    function carregarEscalaIndividual(usuario, nomeOperador, grupo, tipo, nomeTipo) {

        ajax.sync.html({
            dados: {grupo: grupo, tipo: tipo, nomeTipo: nomeTipo, usuario: usuario, nomeOperador: nomeOperador, botao: true, item: 'escala'},
            endereco: 'carregar.php',
            sucesso: function(dados) {
                $("#escala").html(dados);

                ajax.sync.html({
                    dados: {grupo: grupo, tipo: tipo, nomeTipo: nomeTipo, usuario: usuario, nomeOperador: nomeOperador, item: 'informacoes'},
                    endereco: 'carregar.php',
                    sucesso: function(dados) {
                        $("#div_infos").html(dados);
                        $("#informacoes").height($("#tabela-impressao").height() - 64);
                    }
                });
            }
        });
    }

    function impressao(tipo) {
        var head = document.head.innerHTML;
        var contents = document.getElementById("escala-impressao").innerHTML;
        var frame1 = document.createElement('iframe');
        frame1.name = "frame1";
        frame1.style.position = "absolute";
        frame1.style.top = "-1000000px";
        document.body.appendChild(frame1);
        var frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
        frameDoc.document.open();
        frameDoc.document.write('<html><head>' + head);
        if (tipo == 'mini') {
            frameDoc.document.write('<link href="<?php echo $sessao['raiz_html']; ?>bootstrap/css_proprio/individual_mini.css" rel="stylesheet" type="text/css"/>');
        }
        frameDoc.document.write('</head><body>');
        frameDoc.document.write(contents);
        frameDoc.document.write('</body></html>');
        frameDoc.document.close();
        window.frames["frame1"].focus();
        setTimeout(function() {
            window.frames["frame1"].focus();
            window.frames["frame1"].print();
            document.body.removeChild(frame1);
        }, 500);
        return false;
    }


</script>