<?php 
// echo 1;
// 
require_once('/home/lawsmart/send_mail.php');
  $headers = apache_request_headers();
  
//   error_log($headers["Content-Hmac"]);
//   echo var_dump($headers["Content-Hmac"]);
//   return;
//   print_r('TEST');
  $body = file_get_contents('php://input');
//   error_log($body);
//   error_log(hash_hmac('sha256', $body, '88f1a0e9aa75ad4aeb6de22190b250ad'));
  $dados = json_decode($body, true);
//   error_log(json_encode($dados));
  if ($headers["Content-Hmac"] == "sha256=".hash_hmac('sha256', $body, '6844eeae042172277615114bef670ff9')) {
    // $dados = json_decode(file_get_contents('php://input'), true);
    // echo "DADOS".var_dump($dados);
    // error_log("VALID TOKEN >>>>>>>>".$dados["document"]["key"]);
    $key = $dados["document"]["key"];
    $con = new mysqli('localhost', 'lawsmart_rob', 'dire0300', 'lawsmart_robv2');
    mysqli_query($con, "update operacoes set status = 5 where clicksign_key = '$key'");
    $operacao_query = mysqli_query($con, "SELECT *, id_postergacao as id, fornecedores.razao as fornecedor, dataOPE as data_oper FROM `postergacoesDetalhes`
    inner join operacoes on operacoes.id = postergacoesDetalhes.id_operacao
    inner join fornecedores on operacoes.cnpj = fornecedores.cnpj
    where operacoes.clicksign_key = '$key'");
    $operacao = mysqli_fetch_all($operacao_query, MYSQLI_ASSOC);
    dispatch_event_mail('postergacao_paga', $operacao[0]['email'], $operacao[0]["razao"], $operacao[0]);

    http_response_code(200);

    return true;
  } else {
    // $headers = var_dump($headers);
    // die($headers);
    error_log("FALSE TOKEN");
    http_response_code(201);
  }







//   $doc_id = $dados["document"]["key"];
?>  