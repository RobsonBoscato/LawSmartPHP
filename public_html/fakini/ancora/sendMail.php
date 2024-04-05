<?php
session_start();
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
date_default_timezone_set('America/Sao_Paulo');

function my_autoload($pClassName)
{
    include('../Class' . "/" . $pClassName . ".class.php");
}
spl_autoload_register("my_autoload");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["id_fornecedor"]) && isset($_POST["type"]) && isset($_POST["operacao"])) {

        $id = htmlspecialchars($_POST["id_fornecedor"]);
        $type = htmlspecialchars($_POST["type"]);
        $operacao = htmlspecialchars($_POST["operacao"]);
        $fornecedores = new fornecedores();
        $fornecedores->setId($id);
        $result = $fornecedores->SelectID();

        if ($result) {

            switch ($type) {
                case '1':
                    #Email de boas Vindas
                    $fornecedorEmail = $result['email'];
                    $ANCORA   = 'FAKINI MALHAS LTDA';
                    $VAR_NOME = $result['razao'];
                    $LINK     =  'https://cinque.lawsmart.com.br/login.php';
                    if ($result['tipo'] == 'fornecedor') {
                        $template = file_get_contents('../Class/fornecedores/law-chain.html');
                    }
                    if ($result['tipo'] == 'cliente') {
                        $template = file_get_contents('../Class/clientes/law-chain-cli.html');
                    }

                    $template = str_replace('{VAR_NOME}', $VAR_NOME, $template);
                    $template = str_replace('{ANCORA}', $ANCORA, $template);
                    $template = str_replace('{LINK}', $LINK, $template);
                    $template = str_replace('{VAR_PARAM1}',  $result['email'], $template);
                    $template = str_replace('{VAR_PARAM2}',  $result['cnpj'], $template);

                    $recipient_email = $fornecedorEmail;
                    $subject         = 'Conheça a LAW Smart Chain';
                    $body            =  $template;
                    $status          = 'pendente';
                    $type            = 'SmartChain';
                    $created_at      = date('Y-m-d H:i:s');
                    $sent_at         = null;
                    $email = new emails();
                    $email->setRecipient_email($recipient_email);
                    $email->setSubject($subject);
                    $email->setBody($body);
                    $email->setStatus($status);
                    $email->setType($type);
                    $email->setCreated_at($created_at);
                    $email->setSent_at($sent_at);
                    $email->Insert();


                    break;
                case '2':
                    if ($result['tipo'] == 'fornecedor') {
                        // duplicatas disponíveis para esse fornecedor
                        $hoje = date('Y-m-d',  strtotime(date('Y-m-d')));
                        $operacao =  new operacoes();
                        $operacao->setCnpj($result['cnpj']);
                        $operacao->setVencimento($hoje);
                        $res = $operacao->SelectOpeDisponiveis();
                        $i = 0;
                        foreach ($res as $op) {
                            $i++;
                            $dt = $op['vencimento'];
                            $nf = explode('/', $op['nota']);
                            $juros = (floatval($result['juros']) === 0) ? 2.5 : floatval($result['juros']);
                            $ano = date('Y'); // Obtém o ano atual
                            $mes = date('n'); // Obtém o mês atual sem zero à esquerda

                            // Obter o número de dias no mês atual
                            $diasMes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
                            $diaSem = intval(date('N', strtotime($dt)));
                            $diasAd = 0;

                            switch ($diaSem) {
                                case 0:
                                    $diasAd += 2;
                                    break;
                                case 5:
                                    $diasAd += 3;
                                    break;
                                case 6:
                                    $diasAd += 3;
                                    break;
                            }
                            $dias = floor((strtotime($dt) - strtotime(date('Y-m-d'))) / (60 * 60 * 24)) + $diasAd;

                            $jurosDia = number_format($juros / $diasMes, 2);

                            $valorDesconto = number_format((floatval($op['valor']) * ($jurosDia / 100)) * $dias, 2);

                            
                            $trHTML .= '<tr>                                    
                                        <td>
                                            <span class="text-muted fw-bold d-block fs-7">nota fiscal</span>
                                            <span class="text-dark fw-bolder d-block fs-5">' . $nf[0] . '</span>
                                        </td>
                                        <td>
                                            <span class="text-muted fw-bold d-block fs-7">parcela</span>
                                            <span class="text-dark fw-bolder d-block fs-5">' . $nf[1] . '</span>
                                        </td>
                                        <td>
                                            <span class="text-muted fw-bold d-block fs-7">a receber</span>
                                            <span class="text-dark fw-bolder d-block fs-5">' . number_format($op['valor'], 2) . '</span>
                                        </td>
                                        <td>
                                            <span class="text-muted fw-bold d-block fs-7">vencimento</span>
                                            <span class="text-dark fw-bolder d-block fs-5">' . date('d/m/Y', strtotime($op['vencimento'])) . '</span>
                                        </td>
                                        <td>
                                            <span class="text-muted fw-bold d-block fs-7">juros/mês</span>
                                            <span class="text-dark fw-bolder d-block fs-5">' . $juros . '%</span>
                                        </td>
                                        <td>
                                            <span class="text-muted fw-bold d-block fs-7">dias</span>
                                            <span class="text-dark fw-bolder d-block fs-5">' . $dias . '</span>
                                        </td>
                                        <td>
                                            <span class="text-muted fw-bold d-block fs-7">descontos</span>
                                            <span class="text-dark fw-bolder d-block fs-5">' . number_format((floatval($op['valor']) * ($jurosDia / 100)) * $dias, 2) . '*</span>
                                        </td>
                                        <td style="width:50px;">
                                            <span class="d-block fs-7">&nbsp;</span>
                                        </td>
                                        </tr>
                                    ';
                            
                            
                          
                        }
                        $VAR_NOME = $result['razao'];
                        $LINK     =  'https://cinque.lawsmart.com.br/login.php';
                        if ($result['tipo'] == 'fornecedor') {
                            $template = file_get_contents('../Class/fornecedores/antecipar-duplicatas.html');
                        }
                        if ($result['tipo'] == 'cliente') {
                            $template = file_get_contents('../Class/clientes/antecipar-duplicatas.html');
                        }
                        $ANCORA   = 'CINQUE CAPITAL SECURITIZADORA S.A';
                        $template = str_replace('{VAR_NOME}', $VAR_NOME, $template);
                        $template = str_replace('{ANCORA}', $ANCORA, $template);
                        $template = str_replace('{LINK}', $LINK, $template);
                        $template = str_replace('{TABELA}',  $trHTML, $template);
                        $recipient_email = $result['email'];
                        $subject         = 'Antecipando Recebíveis';
                        $body            =  $template;
                        $status          = 'pendente';
                        $type            = 'Disponíveis';
                        $created_at      = date('Y-m-d H:i:s');
                        $sent_at         = null;



                        $email = new emails();
                        $email->setRecipient_email($recipient_email);
                        $email->setSubject($subject);
                        $email->setBody($body);
                        $email->setStatus($status);
                        $email->setType($type);
                        $email->setCreated_at($created_at);
                        $email->setSent_at($sent_at);
                        $email->Insert();
                    }
                    break;
                case '3':
                    if ($result['tipo'] == 'fornecedor') {
                        // operacao de fornecedor é antecipada e de cliente postergada
                        $antecipadasDetalhes = new antecipadasDetalhes();
                        $antecipadasDetalhes->setAntecipada($operacao);
                        $result = $antecipadasDetalhes->ResumoOp();
                        $v = 0;
                        foreach ($result as $a) {
                            //busca a url do contrato
                            $clicksign_key =   $a['clicksign_keyBuscar'];

                            $sign_url = '';
                            $access_token = 'adf1d531-65de-4213-b0f7-947443bfd863';
                            $url = "https://app.clicksign.com/api/v1/documents/$clicksign_key?access_token=$access_token";

                            $ch = curl_init($url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                                'Accept: application/json',
                                'Content-Type: application/json',
                                'Host: app.clicksign.com'
                            ]);

                            $response = curl_exec($ch);
                            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            curl_close($ch);

                            if ($httpCode === 200) {
                                $doc_return = json_decode($response, true);

                                if (isset($doc_return["document"]["signers"][1]["url"])) {
                                    $LINK  =  $doc_return["document"]["signers"][1]["url"];
                                } else {
                                    $LINK  = '';
                                }
                            } else {
                                $LINK  =  '';
                            }

                            $recipient_email = $a['email'];
                            $v += $a['Original'];
                            $DATA_OPERACAO = date('d/m/Y', strtotime($a['dataOPE']));
                            $VAR_NOME = $a['razao'];
                        }

                        $NUMERO_OPERACAO_VENDOR = $operacao;
                        $VALOR_LIQUIDO_OPERACAO = number_format($v, 2, ",", ".");

                        $template = file_get_contents('../Class/fornecedores/assinatura.html');
                        $template = str_replace('{VAR_NOME}', $VAR_NOME, $template);
                        $template = str_replace('{NUMERO_OPERACAO_VENDOR}', '0000' . $NUMERO_OPERACAO_VENDOR, $template);
                        $template = str_replace('{DATA_OPERACAO}', $DATA_OPERACAO, $template);
                        $template = str_replace('{VALOR_LIQUIDO_OPERACAO}', $VALOR_LIQUIDO_OPERACAO, $template);
                        $template = str_replace('{LINK}', $LINK, $template);


                        $subject         = 'Faça sua Assinatura';
                        $body            =  $template;
                        $status          = 'pendente';
                        $type            = 'Assinatura';
                        $created_at      = date('Y-m-d H:i:s');
                        $sent_at         = null;



                        $email = new emails();
                        $email->setRecipient_email($recipient_email);
                        $email->setSubject($subject);
                        $email->setBody($body);
                        $email->setStatus($status);
                        $email->setType($type);
                        $email->setCreated_at($created_at);
                        $email->setSent_at($sent_at);
                        $email->Insert();
                    }

                    break;
                case '4':
                    if ($result['tipo'] == 'fornecedor') {
                        // operacao de fornecedor é antecipada e de cliente postergada
                        $antecipadasDetalhes = new antecipadasDetalhes();
                        $antecipadasDetalhes->setAntecipada($operacao);
                        $result = $antecipadasDetalhes->ResumoOp();

                        $trs = '';
                        $table = '';

                        $total_juros = 0;
                        $total_liquido = 0;

                        $qtd = count($result);
                        foreach ($result as $a) {
                            $clicksign_key =   $a['clicksign_keyBuscar'];

                            $sign_url = '';
                            $access_token = 'adf1d531-65de-4213-b0f7-947443bfd863';
                            $url = "https://app.clicksign.com/api/v1/documents/$clicksign_key?access_token=$access_token";

                            $ch = curl_init($url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                                'Accept: application/json',
                                'Content-Type: application/json',
                                'Host: app.clicksign.com'
                            ]);

                            $response = curl_exec($ch);
                            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            curl_close($ch);

                            if ($httpCode === 200) {
                                $doc_return = json_decode($response, true);

                                if (isset($doc_return["document"]["signers"][1]["url"])) {
                                    $LINK  =  $doc_return["document"]["signers"][1]["url"];
                                } else {
                                    $LINK  = '';
                                }
                            } else {
                                $LINK  =  '';
                            }
                            $VAR_NOME = $a['razao'];
                            $recipient_email =  $a['email'];
                            $desscontos = 0;
                            $desscontos =   $a['descontoJuros'];
                            $trs .= ' 
                            <tr style="background-color: #FFF; color: #000; text-align: left; border: 0px solid white;">
                            <td style="padding: 10px;">' . $a['razao'] . '</td>
                            <td style="padding: 10px;">' . $a['nota'] . '</td>
                            <td style="padding: 10px;">' . $a['vencimento'] . '</td>
                            <td style="padding: 10px;">' . number_format($a['Original'], 2, ",", ".") . '</td>
                            <td style="padding: 10px;">R$ ' . number_format($desscontos, 2, ",", ".") . '</td>
                            <td style="padding: 10px;">R$ ' . number_format($a['valor_antecip'], 2, ",", ".") . '</td>
                            </tr>
                            ';

                            $total_juros =  $a['descontoTaxas'];
                            $total_liquido += $a['valor_antecip'];
                        }

                        $total_liquido =  $total_liquido - $total_juros;

                        $table = '<table style="border: hidden 0px #FFF; font-size: 12px;">
                            <thead>
                            <tr style="background-color: #E0E0E0; color: #000; text-align: center; font-weight: bold; text-transform: uppercase;">
                                <td style="padding: 10px; ">Emitente/Sacado</td>
                                <td style="padding: 10px; ">Documento</td>
                                <td style="padding: 10px; ">Vencimento</td>
                                <td style="padding: 10px; ">Valor</td>
                                <td style="padding: 10px; ">Juros</td>
                                <td style="padding: 10px; ">Líquido</td>
                            </tr>
                            </thead>
                            <tbody>
                            ' . $trs . '
                            </tbody>
                            <tfoot style="">
                            <tr style="background-color: #E0E0E0; color: #000; text-align: center; font-weight: bold; text-transform: uppercase;">
                                <td style="padding: 10px; ">Total de Tarifas</td>
                                <td></td>
                                <td> </td>
                                <td> </td>
                                <td> </td>
                                <td style="padding: 10px; "> R$' . number_format($total_juros, 2, ",", ".") . '</td>
                            </tr>
                            <tr style="background-color: #D1D1D1; color: #000; text-align: center; font-weight: bold; text-transform: uppercase;">
                                <td style="padding: 10px; "> Valor Líquido</td>
                                <td> </td>
                                <td> </td>
                                <td> </td>
                                <td> </td>
                                <td style="padding: 10px; "> R$' . number_format($total_liquido, 2, ",", ".") . '</td>
                            </tr>
                            <tr style="background-color: #010840; color: #FFF; text-align: center; font-weight: bold; text-transform: uppercase;">
                                <td style="padding: 10px;">Banco</td>
                                <td style="padding: 10px;">' . $a['banco'] . '</td>
                                <td style="padding: 10px;">Conta</td>
                                <td style="padding: 10px;">' . $a['conta'] . '</td>
                                <td style="padding: 10px;">Agencia</td>
                                <td style="padding: 10px;">' . $a['agencia'] . '</td>
                            </tr>
                            </tfoot>
                        </table>
                        ';

                        $NUMERO_OPERACAO_VENDOR = $operacao;
                        $VALOR_LIQUIDO_OPERACAO = number_format($v, 2, ",", ".");

                        $template = file_get_contents('../Class/fornecedores/operacao-concluida.html');
                        $template = str_replace('{RESUMO}', $table, $template);
                        $template = str_replace('{VAR_NOME}', $VAR_NOME, $template);
                        $template = str_replace('{LINK}', $LINK, $template);


                        $subject         = 'Resumo da Operação';
                        $body            =  $template;
                        $status          = 'pendente';
                        $type            = 'Resumo';
                        $created_at      = date('Y-m-d H:i:s');
                        $sent_at         = null;



                        $email = new emails();
                        $email->setRecipient_email($recipient_email);
                        $email->setSubject($subject);
                        $email->setBody($body);
                        $email->setStatus($status);
                        $email->setType($type);
                        $email->setCreated_at($created_at);
                        $email->setSent_at($sent_at);
                        $email->Insert();
                    }

                    break;
                default:
                    # code...
                    break;
            }

            // Se necessário, faça algo com o resultado aqui

            // Prepare o JSON de resposta
            $response = array(
                "success" => true,
                "message" => "Operação bem-sucedida"
            );
        } else {
            // Em caso de falha na operação
            $response = array(
                "success" => false,
                "message" => "Falha na operação"
            );
        }
    }
}
