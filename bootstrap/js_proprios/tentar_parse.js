
// diminui o codigo para as chamadas ajax
ajax = {
    async: {
        html: function (par) {
            dados = par.dados;
            endereco = par.endereco;
            sucesso = par.sucesso;
            reativar = par.reativar;
            alvoLoader = (typeof par.alvoLoader === 'undefined') ? "#loader" : par.alvoLoader;

            resposta = null;
            loader.show(alvoLoader);
            $.ajax({
                type: 'POST',
                data: dados,
                url: endereco,
                success: function (data) {
                    try {
                        resposta = JSON.parse(data);
                        if (resposta.erro) {
                            bootbox.dialog({
                                title: "Houve um erro no sistema.<br>Por favor, print esta tela e envie para o desenvolvedor.",
                                message: resposta.desc
                            });
                        }
                    } catch (e) {
                        sucesso(data);
                        reativar ? ativarPlugins() : null;
                    }
                    loader.hide(alvoLoader);
                },
                error: function (jqXHR, data, errorThrown) {
                    bootbox.dialog({
                        title: "Houve um erro no sistema.<br>Por favor, print esta tela e envie para o desenvolvedor.",
                        message: "<h5> Tipo : " + data + "</h5><h5> - " + errorThrown + "</h5><h5> html:<br> " + jqXHR.responseText + "</h5>"
                    });
                    loader.hide(alvoLoader);
                }
            });
        },
        obj: function (par) {
            dados = par.dados;
            endereco = par.endereco;
            sucesso = par.sucesso;
            reativar = par.reativar;
            alvoLoader = (typeof par.alvoLoader === 'undefined') ? "#loader" : par.alvoLoader;

            resposta = null;
            loader.show(alvoLoader);

            $.ajax({
                type: 'POST',
                data: dados,
                url: endereco,
                success: function (data) {
                    parsed = false;
                    try {
                        resposta = JSON.parse(data);
                        parsed = true;
                    } catch (e) {
                        bootbox.dialog({
                            title: "Houve um erro no sistema.<br>Por favor, print esta tela e envie para o desenvolvedor.",
                            message: "<h5> Erro : " + e + "</h5><h5> html:<br> " + data + "</h5>"
                        });
                    }
                    if (parsed) {
                        if (resposta.erro) {
                            bootbox.dialog({
                                title: "Houve um erro no sistema.<br>Por favor, print esta tela e envie para o desenvolvedor.",
                                message: resposta.desc
                            });
                        } else {
                            if (typeof sucesso === "function") {
                                // Execute the callback function and pass the parameters to it
                                sucesso(resposta);
                                reativar ? ativarPlugins() : null;
                            }
                        }
                    }
                    loader.hide(alvoLoader);
                },
                error: function (jqXHR, data, errorThrown) {
                    bootbox.dialog({
                        title: "Houve um erro no sistema.<br>Por favor, print esta tela e envie para o desenvolvedor.",
                        message: "<h5> Tipo : " + data + "</h5><h5> - " + errorThrown + "</h5><h5> html:<br> " + jqXHR.responseText + "</h5>"
                    });
                    loader.hide(alvoLoader);
                }
            });
        },
    },
    sync: {
        html: function (par) {
            dados = par.dados;
            endereco = par.endereco;
            sucesso = par.sucesso;
            reativar = par.reativar;
            alvoLoader = (typeof par.alvoLoader === 'undefined') ? "#loader" : par.alvoLoader;

            resposta = null;
            loader.show(alvoLoader);
            $.ajax({
                type: 'POST',
                data: dados,
                url: endereco,
                async: false,
                success: function (data) {
                    try {
                        resposta = JSON.parse(data);
                        if (resposta.erro) {
                            bootbox.dialog({
                                title: "Houve um erro no sistema.<br>Por favor, print esta tela e envie para o desenvolvedor.",
                                message: resposta.desc
                            });
                        }
                    } catch (e) {

                        sucesso(data);
                        reativar ? ativarPlugins() : null;
                    }
                },
                error: function (jqXHR, data, errorThrown) {
                    bootbox.dialog({
                        title: "Houve um erro no sistema.<br>Por favor, print esta tela e envie para o desenvolvedor.",
                        message: "<h5> Tipo : " + data + "</h5><h5> - " + errorThrown + "</h5><h5> html:<br> " + jqXHR.responseText + "</h5>"
                    });
                }
            });
            loader.hide(alvoLoader);
        },
        obj: function (par) {
            dados = par.dados;
            endereco = par.endereco;
            sucesso = par.sucesso;
            reativar = par.reativar;
            alvoLoader = (typeof par.alvoLoader === 'undefined') ? "#loader" : par.alvoLoader;

            resposta = null;
            loader.show(alvoLoader);
            $.ajax({
                type: 'POST',
                data: dados,
                url: endereco,
                async: false,
                success: function (data) {
                    parsed = false;
                    try {
                        resposta = JSON.parse(data);
                        parsed = true;
                    } catch (e) {
                        bootbox.dialog({
                            title: "Houve um erro no sistema.<br>Por favor, print esta tela e envie para o desenvolvedor.",
                            message: "<h5> Erro : " + e + "</h5><h5> html:<br> " + data + "</h5>"
                        });
                    }
                    if (parsed) {
                        if (resposta.erro) {
                            bootbox.dialog({
                                title: "Houve um erro no sistema.<br>Por favor, print esta tela e envie para o desenvolvedor.",
                                message: resposta.desc
                            });
                        } else {
                            if (typeof sucesso === "function") {
                                // Execute the callback function and pass the parameters to it
                                sucesso(resposta);
                                reativar ? ativarPlugins() : null;
                            }
                        }
                    }
                },
                error: function (jqXHR, data, errorThrown) {
                    bootbox.dialog({
                        title: "Houve um erro no sistema.<br>Por favor, print esta tela e envie para o desenvolvedor.",
                        message: "<h5> Tipo : " + data + "</h5><h5> - " + errorThrown + "</h5><h5> html:<br> " + jqXHR.responseText + "</h5>"
                    });
                }
            });
            loader.hide(alvoLoader);
        },
        cel: function (par) {
            dados = par.dados;
            endereco = par.endereco;
            sucesso = par.sucesso;
            erro = par.erro;
            reativar = par.reativar;
            alvoLoader = (typeof par.alvoLoader === 'undefined') ? "#loader" : par.alvoLoader;

            resposta = null;
            loader.show(alvoLoader);
            $.ajax({
                type: 'POST',
                data: dados,
                url: endereco,
                async: false,
                success: function (data) {
                    parsed = false;
                    try {
                        resposta = JSON.parse(data);
                        parsed = true;
                    } catch (e) {
                        bootbox.dialog({
                            title: "Houve um erro no sistema.<br>A informação da escala não foi salva",
                            message: "<h5> Erro : " + e + "</h5><h5> html:<br> " + data + "</h5>"
                        });
                        if (typeof erro === "function") {
                            // Execute the callback function and pass the parameters to it
                            erro();
                            reativar ? ativarPlugins() : null;
                        }

                    }
                    if (parsed) {
                        if (resposta.erro) {
                            bootbox.dialog({
                                title: "Houve um erro no sistema.<br>A informação da escala não foi salva",
                                message: resposta.desc
                            });
                            if (typeof erro === "function") {
                                // Execute the callback function and pass the parameters to it
                                erro();
                                reativar ? ativarPlugins() : null;
                            }
                        } else {
                            if (typeof sucesso === "function") {
                                // Execute the callback function and pass the parameters to it
                                sucesso(resposta);
                                reativar ? ativarPlugins() : null;
                            }
                        }
                    }
                },
                error: function (jqXHR, data, errorThrown) {
                    bootbox.dialog({
                        title: "Houve um erro no sistema.<br>A informação da escala não foi salva",
                        message: "<h5> Tipo : " + data + "</h5><h5> - " + errorThrown + "</h5><h5> html:<br> " + jqXHR.responseText + "</h5>"
                    });
                    if (typeof erro === "function") {
                        // Execute the callback function and pass the parameters to it
                        erro();
                        reativar ? ativarPlugins() : null;
                    }
                }
            });
            loader.hide(alvoLoader);
        }
    }
};