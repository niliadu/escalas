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
    <title>Item da Escala</title>
    <?php
    $itemMenu = 6;
    include $sessao['raiz'] . 'scripts_css.php';
    include $sessao['raiz'] . 'cabecalho.php';
    ?>
</head>
<body>
    <div class="container">
        <div class="row collapse in" id='cabecalho_subs'>
            <div class=" col-xs-12">
                <h3>Item da Escala</h3>
            </div>
        </div>
        <div class="row">
            <div col-xs-12>
                <div class="col-xs-2" align="left" id='calendario'>
                    <?php include $sessao['raiz'] . 'calendario.php'; ?>
                </div>
                <div id="display_tipo_escala" class="col-xs-2 y-overflow" style="display: none;">
                    <?php include './htmls/lista_tipos.php'; ?>
                </div>

                <div class="col-xs-2 pull-right" id="loader" align="right"></div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-xs-12" id="alerta-erro"></div>          
        </div>
        <br>
        <div class="row">
            <div class="col-xs-3" id="escalas"></div>
            <div class="col-xs-3" id='btn-gerar'>
                <button class="btn btn-xs btn-primary" title="GERAR ITEM" onclick="gerarItem();">
                    Gerar Item &nbsp;
                    <span class="glyphicon glyphicon-list-alt"></span>
                </button>
            </div>

        </div>
        <div class="row">
            <div class="col-xs-12" id="item"></div>
        </div>
</body>

<script>

///////////////////////////
    ativarPlugins();

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
        $("#alerta-erro").html("");
        $("#item").html("");
        $("#escalas").html("");
        $("#display_tipo_escala").attr('style', 'display:none;');
        $("#btn-gerar").attr('style', 'display:none;');

        ano = parseInt($("#ano-mes-picker").attr('ano'));
        mes = parseInt($("#ano-mes-picker").attr('mes'));
        orgao = "<?php echo $_SESSION['orgao_usu_id']; ?>";
        mesNome = correcaoMes.meses[mes];
        ajax.sync.obj({
            dados: {ano: ano, mes: mes, orgao: orgao, item: 'tipos_escala_param'},
            endereco: 'checar.php',
            sucesso: function (dados) {
                if (!dados.existe) {
                    bootbox.alert("Ainda não existem escalas para visualização no mês em questão");
                } else {
                    $("#ano-mes-picker").attr('grupo', dados.grupo);
                    $("#display_tipo_escala").attr('style', "display:block;");
                    verificarTipoExiste();
                }
            }
        });
    }

    function verificarTipoExiste() {
        $("#alerta-erro").html("");
        $("#item").html("");
        $("#escalas").html("");
        $("#btn-gerar").attr('style', 'display:none;');

        tipo = $("#tipo_escala").val();
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        ajax.sync.obj({
            dados: {tipo: tipo, grupo: grupo, item: 'tipo_existe'},
            endereco: 'checar.php',
            sucesso: function (dados) {
                if (!dados.existe) {
                    bootalerta.erro('#alerta-erro', "Este tipo de escala ainda não existe no mês");
                } else {
                    carregarEscalas();
                }
            }
        });
    }

    function carregarEscalas() {
        grupo = parseInt($("#ano-mes-picker").attr('grupo'));
        ajax.sync.html({
            dados: {grupo: grupo, item: 'escalas'},
            reativar: true,
            endereco: 'carregar.php',
            sucesso: function (dados) {
                $("#escalas").html(dados);
                $("#btn-gerar").attr('style', 'display:block;');
            }
        });
    }

    function gerarItem() {
        tipo = $("#tipo_escala").val();
        escalas = $("#escalas_sel").val();
        if (escalas == null) {
            bootbox.alert("É necessário selecionar pelo menos uma escala.")
        } else {
            ajax.sync.html({
                dados: {escalas: escalas, tipo: tipo, item: 'item'},
                reativar: true,
                endereco: 'carregar.php',
                sucesso: function (dados) {
                    $("#item").html(dados);
                }
            });
        }
    }

    $(document).on('change', '#tipo_escala', function () {
        verificarTipoExiste();
    });
</script>