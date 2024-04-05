<?php

  session_start();
	$con = new mysqli('localhost', 'lawsmart_db', 'dire0300', 'lawsmart_db');
	mysqli_set_charset($con, 'utf8');

	function prepared_query($mysqli, $sql, $params, $types = "") {    
    $stmt = $mysqli->prepare($sql);
		if (sizeof($params) > 0) {
			$types = $types ?: str_repeat("s", count($params));
			$stmt->bind_param($types, ...$params);
		}    
    $stmt->execute();
    return $stmt;
	}

  function tirarAcentos($string){
    return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
  }

  $sql  = "SELECT COALESCE(MAX(remessa),0) AS remessa FROM `boletos`";
  $stmt = prepared_query($con, $sql, [], '');
  $res  = $stmt->get_result()->fetch_row();
  $stmt->close();

  $remessa = floatval($res[0]) + 1;
  $nome    = '../remessas/CB'.str_pad($remessa, 6, '0', STR_PAD_LEFT).'.REM';
  $arquivo = fopen($nome, 'w+');

  $i = 1; //contador registros

  $header  = '0';                                 //tipo de registro
  $header .= '1';                                 //operação
  $header .= 'REMESSA';                           //literal remessa
  $header .= '01';                                //codigo do serviço
  $header .= str_pad('COBRANCA', 15);             //literal do serviço
  $header .= '0862';                              //agencia
  $header .= '00';                                //zeros 2x
  $header .= '99570';                             //conta
  $header .= '1';                                 //dac
  $header .= str_repeat(' ', 8);                  //brancos 8x
  $header .= str_pad('LAWSEC S/A', 30);           //nome da empresa
  $header .= '341';                               //codigo do banco
  $header .= str_pad('ITAU', 15);                 //nome do banco
  $header .= date('dmy');                         //data geração
  $header .= str_repeat(' ', 294);                //brancos 294x
  $header .= str_pad($i++, 6, '0', STR_PAD_LEFT); //nº sequencial
  $header .= "\n\n";
  fwrite($arquivo, $header);

  $sql = "SELECT * FROM boletos WHERE status=?";
  $stmt = prepared_query($con, $sql, ['P'], 's');
  $boletos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  $stmt->close();

  foreach ($boletos as $boleto) {
    
    $sql = "SELECT * FROM fornecedores WHERE cnpj=?";
    $stmt = prepared_query($con, $sql, [$boleto['cnpj']], 's');
    $cnpj = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    //reg 1
    $linha =  '1';                                                    //tipo de registro                1
    $linha .= '04';                                                   //codigo de inscrição             3
    $linha .= '13476058000158';                                       //nº inscrição empresa           17
    $linha .= '0862';                                                 //agencia                        21
    $linha .= '00';                                                   //zeros 2x                       23
    $linha .= '99570';                                                //conta                          28
    $linha .= '1';                                                    //dac                            29
    $linha .= str_repeat(' ', 4);                                     //brancos 4x                     33
    $linha .= '0000';                                                 //instrução/alegação             37
    $linha .= str_pad($boleto['operacao'], 25);                       //uso da empresa (ID operação)   62
    $linha .= str_pad($boleto['nosso_numero'], 8, '0', STR_PAD_LEFT); //nosso numero                   70
    $linha .= str_repeat('0', 13);                                    //qtde moeda                     83
    $linha .= '109';                                                  //nº da carteira                 86
    $linha .= str_repeat(' ', 21);                                    //brancos 21x                   107
    $linha .= 'I';                                                    //carteira                      108
    $linha .= '01';                                                   //código ocorrencia 09 = protestar 110
    $linha .= str_pad($boleto['documento'], 10);                      //nº documento - nf             120
    $dt = explode('-', $boleto['vencimento']);
    $linha .= date("dmy",mktime(0,0,0,$dt[1],$dt[2],$dt[0]));         //vencimento                    126
    $vl = str_replace('.', '', $boleto['valor']);
    $linha .= str_pad($vl, 13, '0', STR_PAD_LEFT);                    //valor do boleto               139
    $linha .= '341';                                                  //codigo do banco               142
    $linha .= '00000';                                                //agencia cobradora             147
    $linha .= '01';                                                   //especie                       149
    $linha .= 'N';                                                    //aceite                        150
    $linha .= date('dmy');                                            //data emissão                  156
    $linha .= '00';                                                   //instrução 1                   158
    $linha .= '00';                                                   //instrução 2                   160
    $linha .= str_pad('745', 13, '0', STR_PAD_LEFT);                  //juros de 1 dia                173
    $linha .= date("dmy",mktime(0,0,0,$dt[1],$dt[2],$dt[0]));         //desconto até data vcto        179
    $linha .= str_pad('', 13, '0', STR_PAD_LEFT);                     //valor do desconto             192
    $linha .= str_pad('', 13, '0', STR_PAD_LEFT);                     //valor de iof                  205
    $linha .= str_pad('', 13, '0', STR_PAD_LEFT);                     //abatimento                    218
    $linha .= '02';                                                   //codigo inscrição 02 = cnpj    220
    $linha .= $cnpj['cnpj'];                                          //nº inscrição pagador cnpj cliente 234
    $nm = substr($cnpj['razao'], 0, 30);
    $linha .= str_pad(strtoupper($nm), 30);                           //nome pagador                  264
    $linha .= str_repeat(' ', 10);                                    //brancos 10x                   274
    $ed = substr(($cnpj['rua'].', '.$cnpj['numero']), 0, 40);
    $linha .= str_pad(strtoupper($ed), 40);                           //logradouro                    314
    $br = substr($cnpj['bairro'], 0, 12);
    $linha .= str_pad(strtoupper($br), 12);                           //bairro                        326
    $linha .= str_pad($cnpj['cep'], 8);                               //cep                           334
    $cd = tirarAcentos(substr($cnpj['cidade'], 0, 15));
    $linha .= str_pad(strtoupper($cd), 15);                           //cidade                        349
    $linha .= str_pad(strtoupper($cnpj['estado']), 2);                //estado                        351
    $linha .= str_pad(strtoupper('LAWSEC S/A'), 30);                  //beneficiario final            381
    $linha .= str_repeat(' ', 4);                                     //brancos 4x                    385
    $linha .= str_repeat('0', 6);                                     //data mora                     391
    $linha .= '00';                                                   //prazo                         393
    $linha .= ' ';                                                    //brancos 1x                    394
    $linha .= str_pad($i++, 6, '0', STR_PAD_LEFT);                    //nº sequencial                 400
    $linha .= "\n";

    //reg2
    $linha .= '2';                                              //tipo de registro                                                                        1
    $linha .= '2';                                              //cod multa 2 = VALOR EM PERCENTUAL COM DUAS CASAS DECIMAIS CONFORME ESTRUTURA DO CAMPO   2
    $linha .= date("dmY",mktime(0,0,0,$dt[1],$dt[2],$dt[0]));   //data da multa                                                                          10
    $linha .= str_pad('500', 13, '0', STR_PAD_LEFT);            //multa 5%                                                                               23
    $linha .= str_repeat(' ', 371);                             //brancos 370x                                                                          393
    $linha .= str_pad($i++, 6, '0', STR_PAD_LEFT);              //nº sequencial                                                                         399
    $linha .= "\n";

    //reg5
    $linha .= '5';                                                    //tipo de registro              1
    $linha .= str_repeat(' ', 120);                                   //email                       121
    $linha .= '02';                                                   //codigo inscrição 02 = cnpj  123
    $linha .= '13476058000158';                                       // nº inscrição = cnpj        137
    $linha .= str_pad(strtoupper('Rua Jorge Czerniewicz, 99'), 40);   //logradouro                  177
    $linha .= str_pad(strtoupper('Czerniewicz'), 12);                 //bairro                      189
    $linha .= '89255000';                                             //cep                         197
    $linha .= str_pad(strtoupper('JARAGUA DO SUL'), 15);              //cidade                      212
    $linha .= 'SC';                                                   //estado                      214
    $linha .= str_repeat(' ', 180);                                   //brancos 180x                394
    $linha .= str_pad($i++, 6, '0', STR_PAD_LEFT);                    //nº sequencial               400
    $linha .= "\n\n";
    fwrite($arquivo, $linha);

    $sql = "UPDATE boletos SET remessa=?, STATUS=? WHERE id=?";
    $stmt = prepared_query($con, $sql, [$remessa, 'E', $boleto['id']], 'isi');
    $stmt->close();
  }

  $trailer  = '9';
  $trailer .= str_repeat(' ', 393);
  $trailer .= str_pad($i++, 6, '0', STR_PAD_LEFT);
  fwrite($arquivo, $trailer);
  fclose($arquivo);

  // header('Content-disposition: attachment; filename='.$nome);
  // header('Content-type: application/txt');
  // header('Content-Transfer-Encoding: binary');
  // header('Content-Description: File Transfer');
  // header('Content-Transfer-Encoding: binary');
  // header('Cache-Control: must-revalidate');
  // ob_clean();
  // flush();
  // readfile('CB'.str_pad($remessa, 6, '0', STR_PAD_LEFT).'.REM');
?>