<?php

$servername = "10.228.12.140";
$username = "escopusu";
$password = "devescop#";
$dbname = "escalas";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection 1 failed: " . $conn->connect_error);
    //header("Location: " . $sessao['raiz_html'] . "erroConn.php");
}
$conn->set_charset('utf8');


$servername = "10.228.12.140";
$username = "escopusu";
$password = "devescop#";
$dbname_s = "lpna";

// Create connection
$connL = new mysqli($servername, $username, $password, $dbname_s);
// Check connection
if ($connL->connect_error) {
    die("Connection 2 failed: " . $conn->connect_error);
//    header("Location: " . $sessao['raiz_html'] . "erroConn.php");
}
$connL->set_charset('utf8');

////
//$servername = "localhost";
//$username = "escopusu";
//$password = "devescop#";
//$dbname_i = "instrucao";
//
//// Create connection
//$connI = new mysqli($servername, $username, $password, $dbname_i);
//// Check connection
//if ($connI->connect_error) {
//    //die("Connection failed: " . $conn->connect_error);
//    header("Location: ".$sessao['raiz_html']."erroConn.php");
//}
//$connI->set_charset('utf8');
////funcao para retornar os valores me forma de array

function sql_busca($sql, $connec) {
    
    //$connec->autocommit(true);    
    $stmt = $connec->prepare($sql);
    if ($stmt) {
        //work arround pra conseguir simplificar as querys
        //utilizo o eval para criar variaveis dinamicas que sao colocadas no binding do statment
        if (func_num_args() > 2) {
            $arg = func_get_arg(2);
            foreach ($arg as $i => $a) {
                if ($a == null) {
                    $cod = '$v' . $i . " = null;";
                } else {
                    $cod = '$v' . $i . " = '$a';";
                }
                eval($cod);
            }
            $cod2 = '$stmt->bind_param(';
            foreach ($arg as $i => $a) {
                $cod3 [] = '$v' . $i;
            }
            $cod2 .= implode(",", $cod3);
            $cod2 .= ');';
            eval($cod2);
        }
        $stmt->execute();

        $result = $stmt->get_result();
        $retorno = array();
        if ($result->num_rows > 0) {
            while ($r = $result->fetch_assoc()) {
                if (!empty($r)) {
                    $retorno[] = $r;
                }
            }
        }

        return $retorno;
        $stmt->close();
    } else {
        $resp['erro'] = true;
        $resp['desc'] = "Statement failed: " . $connec->error;
        exit(json_encode($resp));
    }
}

function sql_ins($sql, $connec) {
    //$connec->autocommit(true);
    //a funcao de inserção sempre retorna uma array que deve ser tratada como json nas respostas ajax
    $stmt = $connec->prepare($sql);
    if ($stmt) {
        //work arround pra conseguir simplificar as querys
        //utilizo o eval para criar variaveis dinamicas que sao colocadas no binding do statment
        if (func_num_args() > 2) {
            $arg = func_get_arg(2);
            foreach ($arg as $i => $a) {
                if ($a == null) {
                    $cod = '$v' . $i . " = null;";
                } else {
                    $cod = '$v' . $i . " = '$a';";
                }
                eval($cod);
            }
            $cod2 = '$stmt->bind_param(';
            foreach ($arg as $i => $a) {
                $cod3 [] = '$v' . $i;
            }
            $cod2 .= implode(",", $cod3);
            $cod2 .= ');';
            eval($cod2);
        }
        $stmt->execute();
        $result = $stmt->insert_id;
        $stmt->close();
        $resp['id'] = $result;
        return $resp;
    } else {
        $resp['erro'] = true;
        $resp['desc'] = "Statement failed: " . $connec->error;
        exit(json_encode($resp));
    }
}

function sql_del($sql, $connec) {
    //$connec->autocommit(true);
    //a funcao de remorção sempre retorna uma array que deve ser tratada como json nas respostas ajax
    $stmt = $connec->prepare($sql);
    if ($stmt) {
        //work arround pra conseguir simplificar as querys
        //utilizo o eval para criar variaveis dinamicas que sao colocadas no binding do statment
        if (func_num_args() > 2) {
            $arg = func_get_arg(2);
            foreach ($arg as $i => $a) {
                if ($a == null) {
                    $cod = '$v' . $i . " = null;";
                } else {
                    $cod = '$v' . $i . " = '$a';";
                }
                eval($cod);
            }
            $cod2 = '$stmt->bind_param(';
            foreach ($arg as $i => $a) {
                $cod3 [] = '$v' . $i;
            }
            $cod2 .= implode(",", $cod3);
            $cod2 .= ');';
            eval($cod2);
        }
        $stmt->execute();
        //$result = $stmt->delete_id;
        $stmt->close();
        return true;
    } else {
        $resp['erro'] = true;
        $resp['desc'] = "Statement failed: " . $connec->error;
        exit(json_encode($resp));
    }
}

function sql_up($sql, $connec) {
    //$connec->autocommit(true);
    //a funcao de atualizar sempre retorna uma array que deve ser tratada como json nas respostas ajax
    $stmt = $connec->prepare($sql);
    if ($stmt) {
        //work arround pra conseguir simplificar as querys
        //utilizo o eval para criar variaveis dinamicas que sao colocadas no binding do statment
        if (func_num_args() > 2) {
            $arg = func_get_arg(2);
            foreach ($arg as $i => $a) {
                if ($a == null) {
                    $cod = '$v' . $i . " = null;";
                } else {
                    $cod = '$v' . $i . " = '$a';";
                }
                eval($cod);
            }
            $cod2 = '$stmt->bind_param(';
            foreach ($arg as $i => $a) {
                $cod3 [] = '$v' . $i;
            }
            $cod2 .= implode(",", $cod3);
            $cod2 .= ');';
            eval($cod2);
        }
        $stmt->execute();
        $stmt->close();
        return true;
    } else {
        $resp['erro'] = true;
        $resp['desc'] = "Statement failed: " . $connec->error;
        exit(json_encode($resp));
    }
}

function sql_transaction($sql, $connec) {
    $erro = false;
    //possibilita uma sequencia de querys serem executadas, procurando-se possiveis erros
    //e executando apenas se todas as  querys nao apresentarem algum erro de
    $connec->autocommit(false);
    $connec->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
    if (is_array($sql)) {
        foreach ($sql as $j => $s) {
            //a funcao de atualizar sempre retorna uma array que deve ser tratada como json nas respostas ajax
            $stmt = $connec->prepare($s);
            if ($stmt) {
                //work arround pra conseguir simplificar as querys
                //utilizo o eval para criar variaveis dinamicas que sao colocadas no binding do statment
                if (func_num_args() > 2) {
                    $arg = func_get_arg(2);
                    $arg = $arg[$j];
                    if ($arg != null) {
                        unset($cod3);
                        foreach ($arg as $i => $a) {
                            if ($a == null) {
                                $cod = '$v' . $i . " = null;";
                            } else {
                                $cod = '$v' . $i . " = '$a';";
                            }
                            eval($cod);
                        }


                        $cod2 = '$stmt->bind_param(';
                        foreach ($arg as $i => $a) {
                            $cod3 [] = '$v' . $i;
                        }
                        $cod2 .= implode(",", $cod3);
                        $cod2 .= ');';
                        eval($cod2);
                    }
                }
                $stmt->execute();
                if (strpos($s, 'INSERT INTO') !== false) {
                    $result = $stmt->insert_id;
                    $retorno['id'] = $result;
                } else {
                    $result = $stmt->get_result();
                    $retorno = array();
                    if ($result->num_rows > 0) {
                        while ($r = $result->fetch_assoc()) {
                            if (!empty($r)) {
                                $retorno[] = $r;
                            }
                        }
                    }
                }
                $retornos[] = $retorno;
            } else {
                $erro = true;
                $erros[] = "Statement failed: " . $stmt->error;
            }
        }
    } else {
        //a funcao de atualizar sempre retorna uma array que deve ser tratada como json nas respostas ajax
        $stmt = $connec->prepare($sql);
        if ($stmt) {
            //work arround pra conseguir simplificar as querys
            //utilizo o eval para criar variaveis dinamicas que sao colocadas no binding do statment
            if (func_num_args() > 2) {
                $arg = func_get_arg(2);
                foreach ($arg as $i => $a) {
                    if ($a == null) {
                        $cod = '$v' . $i . " = null;";
                    } else {
                        $cod = '$v' . $i . " = '$a';";
                    }
                    eval($cod);
                }
                $cod2 = '$stmt->bind_param(';
                foreach ($arg as $i => $a) {
                    $cod3 [] = '$v' . $i;
                }
                $cod2 .= implode(",", $cod3);
                $cod2 .= ');';
                eval($cod2);
            }
            $stmt->execute();
            if (strpos($sql, 'INSERT INTO') !== false) {
                $result = $stmt->insert_id;
                $resp['erro'] = false;
                $retorno['id'] = $result;
            } else {

                $meta = $stmt->result_metadata();
                while ($f = $meta->fetch_field()) {
                    $variaveis[] = &$data[$f->name];
                }

                call_user_func_array(array($stmt, 'bind_result'), $variaveis);

                $i = 0;
                while ($stmt->fetch()) {
                    $retorno[$i] = array();
                    foreach ($data as $nome => $var) {
                        $retorno[$i][$nome] = $var;
                    }
                    $i++;
                }
            }
            $retornos[] = $retorno;
        } else {
            $erro = true;
            $erros[] = "Statement failed: " . $stmt->error;
        }
    }

    if ($erro) {
        $connec->rollback();
        $resp['erro'] = true;
        $resp['desc'] = implode("<br>", $erros);
        exit(json_encode($resp));
    } else {
        $connec->commit();
        $stmt->close();
        return $retornos;
    }
}

?> 