<?php
require_once("PHPMailer/src/PHPMailer.php");
require_once("PHPMailer/src/SMTP.php");

use PHPMailer\PHPMailer\PHPMailer;

function dispatch_event_mail($event, $dest_address, $dest_name, $operacao, $titulos = null)
{
  // case event = 'antecipacao'
  // send antecipacao email

  // case event = 'postergacao'
  // send prorrogacao email

  // case event = 'pagamento'
  // send pagamentorealizado email

  if ($event == 'antecipacao_criada') {
    // $operacao_reference_query = $con->query("SELECT * FROM operacoes WHERE operacoes.nota like '{$doc}' and operacoes.id != {$op['id_oper']}");
    // $operacao_reference = $operacao_reference_query->fetch_assoc();

    // $sql = "SELECT * FROM antecipadasDetalhes WHERE antecipada = ?";
    // $stmt = prepared_query($con, $sql, [$operacao['id']], 'i');
    // $antecip = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    // $stmt->close();

    // foreach ($antecip as $a) {

    // }

    // $operacao = array("clicksign_key" => '98d5523c-432c-4ac9-8ce6-5557d0f82500');
    $operacao['sign_url'] = get_sign_url($operacao['clicksign_key']);
    // $operacao['fornecedor'] = 'Goku SSJ4';
    send_antecipacao_criada_mail($dest_address, $dest_name, $operacao, $titulos);
    send_antecipacao_assinar_mail($dest_address, $dest_name, $operacao);
  }

  if ($event == 'postergacao_criada') {
    $operacao['sign_url'] = get_sign_url($operacao['clicksign_key']);
    send_postergacao_criada_mail($dest_address, $dest_name, $operacao, $titulos);
    send_postergacao_assinar_mail($dest_address, $dest_name, $operacao);
    // $operacao['sign_url'] = get_sign_url($operacao['clicksign_key']);
    // $sign_url = null;
  }

  if ($event == 'postergacao_paga') {
    send_postergacao_pagamento_mail($dest_address, $dest_name, $operacao);
  }

  if ($event == 'antecipacao_paga') {
    send_antecipacao_pagamento_mail($dest_address, $dest_name, $operacao);
  }
}

// function get_sign_url($doc_key) {

//   $access_token = 'adf1d531-65de-4213-b0f7-947443bfd863';
//   // echo "https://app.clicksign.com/api/v1/documents/$doc_key?access_token=$access_token";
//   exec("curl 'https://app.clicksign.com/api/v1/documents/$doc_key?access_token=$access_token' \
//   --header 'Accept: application/json' \
//   --header 'Content-Type: application/json' \
//   --header 'Host: app.clicksign.com'", $doc_data);  
//   $doc_return = json_decode($doc_data[0], true);

//   return $doc_return["document"]["signers"][1]["url"];
// }
function get_sign_url($doc_key)
{
  $access_token = 'adf1d531-65de-4213-b0f7-947443bfd863';
  $url = "https://app.clicksign.com/api/v1/documents/$doc_key?access_token=$access_token";

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
      return $doc_return["document"]["signers"][1]["url"];
    } else {
      // Handle missing URL
      return null;
    }
  } else {
    // Handle HTTP request failure
    return null;
  }
}
function send_antecipacao_assinar_mail($dest_address, $dest_name, $operacao)
{
  ob_start();
  include '/home/lawsmart/layout/antecipacao-assinatura.html';
  $msg = ob_get_clean();
  // $operacao['numero'] = 1234;
  // 
  // get_sign_url($operacao['clicksign_key']);
  send_mail($dest_address, $dest_name, "Lembrete de assinatura solicitação de antecipação", $msg);
}

function send_antecipacao_criada_mail($dest_address, $dest_name, $operacao, $antecip)
{

  $trs = '';

  $total_juros = 0;
  $total_liquido = 0;


  foreach ($antecip as $a) {
    $trs .= ' 
        <tr style="background-color: #FFF; color: #000; text-align: left; border: 0px solid white;">
        <td style="padding: 10px;">' . $a['razao'] . '</td>
        <td style="padding: 10px;">' . $a['nota'] . '</td>
        <td style="padding: 10px;">' . $a['vencimento'] . '</td>
        <td style="padding: 10px;">' . number_format($a['valorOriginal'], 2, ",", ".") . '</td>
        <td style="padding: 10px;">R$ ' . number_format($a['descontoJuros'], 2, ",", ".") . '</td>
        <td style="padding: 10px;">R$ ' . number_format($a['valor_antecip'], 2, ",", ".") . '</td>
        </tr>
      ';

    $total_juros += $a['descontoJuros'];
    $total_liquido += $a['valor_antecip'];
  }



  $table = '<table style="border: hidden 0px #FFF; font-size: 12px;">
      <thead>
        <tr style="background-color: #E0E0E0; color: #000; text-align: center; font-weight: bold; text-transform: uppercase;">
          <td style="padding: 10px; ">Emitente/Sacado</td>
          <td style="padding: 10px; ">Documento</td>
          <td style="padding: 10px; ">Vencimento</td>
          <td style="padding: 10px; ">Valor</td>
          <td style="padding: 10px; ">Deságio</td>
          <td style="padding: 10px; ">Líquido</td>
        </tr>
      </thead>
      <tbody>
        ' . $trs . '
      </tbody>
      <tfoot style="">
        <tr style="background-color: #E0E0E0; color: #000; text-align: center; font-weight: bold; text-transform: uppercase;">
          <td style="padding: 10px; ">Total Deságio</td>
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
          <td style="padding: 10px;"> </td>
          <td style="padding: 10px;">Conta</td>
          <td> </td>
          <td style="padding: 10px;">' . $antec[0]['conta'] . '-' . $antec[0]['agencia'] . '</td>
          <td> </td>
          <td> </td>
        </tr>
      </tfoot>
    </table>
    ';
  ob_start();
  include '/home/lawsmart/layout/antecipacao-sucesso.html';
  $msg = ob_get_clean();

  send_mail($dest_address, $dest_name, "Solicitação de antecipação recebida com sucesso", $msg);
}

function send_antecipacao_pagamento_mail($dest_address, $dest_name, $operacao)
{
  ob_start();
  include '/home/lawsmart/layout/antecipacao-final.html';
  $msg = ob_get_clean();

  // return;
  send_mail($dest_address, $dest_name, "Depósito confirmado", $msg);
}

function send_postergacao_assinar_mail($dest_address, $dest_name, $operacao)
{
  ob_start();
  include '/home/lawsmart/layout/postergacao-assinar.html';
  $msg = ob_get_clean();
  send_mail($dest_address, $dest_name, "Lembrete de assinatura da solicitação de postergação", $msg);
}

function send_postergacao_criada_mail($dest_address, $dest_name, $operacao, $antecip)
{
  $trs = '';

  $total_juros = 0;
  $total_liquido = 0;


  foreach ($antecip as $a) {
    $trs .= ' 
        <tr style="background-color: #FFF; color: #000; text-align: left; border: 0px solid white;">
        <td style="padding: 10px;">' . $a['razao'] . '</td>
        <td style="padding: 10px;">' . $a['nota'] . '</td>
        <td style="padding: 10px;">' . $a['vencimento'] . '</td>
        <td style="padding: 10px;">' . number_format($a['valorOriginal'], 2, ",", ".") . '</td>
        <td style="padding: 10px;">R$ ' . number_format($a['juros'], 2, ",", ".") . '</td>
        <td style="padding: 10px;">R$ ' . number_format($a['valor_antecip'], 2, ",", ".") . '</td>
        </tr>
      ';

    $total_juros += $a['juros'];
    $total_liquido += $a['valor_antecip'];
  }



  $table = '<table style="border: hidden 0px #FFF; font-size: 12px;">
      <thead>
        <tr style="background-color: #E0E0E0; color: #000; text-align: center; font-weight: bold; text-transform: uppercase;">
          <td style="padding: 10px; ">Emitente/Sacado</td>
          <td style="padding: 10px; ">Documento</td>
          <td style="padding: 10px; ">Vencimento</td>
          <td style="padding: 10px; ">Valor</td>
          <td style="padding: 10px; ">Deságio</td>
          <td style="padding: 10px; ">Líquido</td>
        </tr>
      </thead>
      <tbody>
        ' . $trs . '
      </tbody>
      <tfoot style="">
        <tr style="background-color: #E0E0E0; color: #000; text-align: center; font-weight: bold; text-transform: uppercase;">
          <td style="padding: 10px; ">Total Deságio</td>
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
          <td style="padding: 10px;"> </td>
          <td style="padding: 10px;">Conta</td>
          <td> </td>
          <td style="padding: 10px;">' . $a['conta'] . '-' . $a['agencia'] . '</td>
          <td> </td>
          <td> </td>
        </tr>
      </tfoot>
    </table>
    ';

  ob_start();
  include '/home/lawsmart/layout/postergacao-sucesso.html';
  $msg = ob_get_clean();
  send_mail($dest_address, $dest_name, "Solicitação de postergação", $msg);
}

function send_postergacao_pagamento_mail($dest_address, $dest_name, $operacao)
{
  ob_start();
  include '/home/lawsmart/layout/postergacao-final.html';
  $msg = ob_get_clean();

  send_mail($dest_address, $dest_name, "Pagamento Confirmado", $msg);
}

function send_mail($dest_address, $dest_name, $subject, $body)
{
  $mail = new PHPMailer();
  $mail->CharSet = "UTF-8";
  // Configurações do servidor de e-mail
  $mail->isSMTP();
  $mail->Host = 'mail.lawsmart.com.br'; // Substitua pelo seu servidor SMTP
  $mail->SMTPAuth = true;
  $mail->Username = 'no-reply@lawsmart.com.br'; // Substitua pelo seu e-mail
  $mail->Password = 'hD@=&$o*eSdd';
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $mail->Port = 587; // Substitua pela porta do seu servidor SMTP
  // Configurações do remetente
  $mail->setFrom('no-reply@lawsmart.com.br', 'Law Smart');
  // $mail->AddAddress($busca_email['email'], $busca_email['nome']);
  $mail->isHTML(true); 

  $mail->AddAddress($dest_address, $dest_name);

  $mail->Subject  = $subject;

  $mail->Body = $body;
  $enviado = $mail->Send();

  $mail->ClearAllRecipients();
  $mail->ClearAttachments();
}
