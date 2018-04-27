<?php

/* 
 * Determina se a célula selecionada deve apresentar o menu ou não da escala corrente. Essa
 * parte foi retirada do index por apresentar erro na formatação do código, impedindo a auto 
 * identação no código
 */
?>
$("#" + idDiv).handsontable("getInstance").addHook('afterOnCellMouseDown',(event, coords, TD) => {                  
    if (event.button === 2) {
        linha = coords['row'];
        coluna = coords['col'];
        conteudo = ($("#"+idDiv).handsontable('getData'));             
        if(typeof conteudo[linha][coluna]['menu'] != 'undefined'){                    
            $("#" + idDiv).handsontable("getInstance").updateSettings({             
                contextMenu: menuModificacoesEscala(idDiv, dados.diasMes)
            });              
        }
        else{
            $(document)[0].oncontextmenu = function() {return false;}
            $("#" + idDiv).handsontable("getInstance").updateSettings({
                contextMenu: false
            });
        }
    }                     
});