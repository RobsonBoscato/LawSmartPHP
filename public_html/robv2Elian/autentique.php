<?php

function idAntecipacao($str) {
  $s1 = explode('_', $str);
  $s2 = explode('.', $s1[2]);
  
  return $s2[0];
}

$con = new mysqli('localhost', 'lawsmart_robv2', 'dire0300', 'lawsmart_robv2');
mysqli_set_charset($con, 'utf8');


function prepared_query($mysqli, $sql, $params, $types = "") {

  if (is_null($mysqli)) {
  
    $mysqli = $con;
  }

  $stmt = $mysqli->prepare($sql);
  if (sizeof($params) > 0) {
    $types = $types ?: str_repeat("s", count($params));
    $stmt->bind_param($types, ...$params);
  }    
  $stmt->execute();
  return $stmt;
}

$dados = json_decode(file_get_contents('php://input'), true);
$arq = json_encode($dados['arquivo']);

$ant = idAntecipacao(json_encode($arq));
$email = $dados['emailEmpresa'];
$id_operacao = $con->real_escape_string($dados['id_operacao']);

if (is_null($id_operacao)) {
  echo json_encode("{}");
  return;
}
// chave certsign
$access_token = 'adf1d531-65de-4213-b0f7-947443bfd863';
$env = 'app';

// $env = 'sandbox';
// $access_token = "9db5ccda-9186-4d71-afbf-17e73efb23d4";
$filename = $dados["arquivo"];
$data = file_get_contents($filename);

// echo "ANTECIPAÇÃO ID IS".$ant;
// echo $data;


$fooObject = (object) null;
$fooObject->document = (object) array("path" => "/".$filename);
$fooObject->document->content_base64 = "data:application/pdf;base64,".base64_encode("$data");
$fooObject->document->block_after = false;
$jsonBody = json_encode($fooObject, JSON_UNESCAPED_SLASHES);
// echo "curl  'https://app.clicksign.com/api/v1/documents?access_token=$access_token' \
// --header 'Accept: application/json' \
// --header 'Content-Type: application/json;' \
// --header 'Host: app.clicksign.com' \
// --data-raw '$jsonBody'";
exec("curl 'https://$env.clicksign.com/api/v1/documents?access_token=$access_token' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json;' \
--header 'Host: $env.clicksign.com' \
-d '$jsonBody'", $output);  
$phpObj = json_decode($output[0], true);
$document_key = $phpObj["document"]["key"];
// var_dump($phpObj["document"]["key"]);
//    Nome = gilberto e.

$sql = "SELECT clicksign_key FROM assinante where 1 = 1";
$stmt = prepared_query($con, $sql, [], '');
$retrieve = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close(); 

$signer1_retrieved = $retrieve[0]["clicksign_key"];
// echo "RETREIVED THEN THE FIRST SIGNER >>>".var_dump($signer1_retrieved);
if (is_null($signer1_retrieved)) {
// create or retrieve signer 1
  $signer1 = (object) null;
  // $signer1->signer = (object) array("email" => "allan.murara@gmail.com");
  $signer1->signer = (object) array("email" => "gilberto@lawsecsa.com.br");
  $signer1->signer->auths = ["api"];
  $signer1->signer->delivery = "none";
  $signer1->signer->has_documentation = false;   
  // $signer1->signer->selfie_enabled = false;
  // $signer1->signer->handwritten_enabled = false;
  // $signer1->signer->liveness_enabled = false;
  // $signer1->signer->facial_biometrics_enabled = false;
  $signer1->signer->name = "Gilberto e.";
  $signer1Body = json_encode($signer1, JSON_UNESCAPED_SLASHES);
  // echo "curl'https://$env.clicksign.com/api/v1/signers?access_token=$access_token' \
  // --header 'Accept: application/json' \
  // --header 'Content-Type: application/json' \
  // --header 'Host: $env.clicksign.com' \
  // -d '$signer1Body'";

  exec("curl 'https://$env.clicksign.com/api/v1/signers?access_token=$access_token' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --header 'Host: $env.clicksign.com' \
  -d '$signer1Body'", $outputAddSignature1);  
  // echo "RESULT IS".var_dump($outputAddSignature1);
  $signer1_retrieved_output = json_decode($outputAddSignature1[0], true);
  $signer1_retrieved = $signer1_retrieved_output["signer"]["key"];
  // var_dump($signer1_retrieved_output);
  // echo "update assinante set clicksign_key = '$signer1_retrieved'";
  // $sql = "update assinante set clicksign_key = $signer1_retrieved";
  $query = mysqli_query($con, "update assinante set clicksign_key = '$signer1_retrieved'");
  // echo "SIGNER 1 ADDED IS _____________________________".var_dump($signer1_retrieved);
  // echo " >>>>> ASSINANTE UPDATED >>>>>>".$query;
  // $stmt = prepared_query($con, $sql, [$signer1_retrieved], 's');
  // $retrieve = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  // $stmt->close(); 
}
// echo 1;
// add signer 1 to document_key
$addListSigner1 = (object) null;
$addListSigner1->list = (object) array("document_key" => $document_key);
$addListSigner1->list->signer_key = $signer1_retrieved;
$addListSigner1->list->sign_as = "party";
$addListSigner1->list->message = "Por favor, assine o documento.";
$addListSigner1->list->name = "Gilberto E.";
$addListBody = json_encode($addListSigner1, JSON_UNESCAPED_SLASHES);

exec("curl 'https://$env.clicksign.com/api/v1/lists?access_token=$access_token' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Host: $env.clicksign.com' \
-d '$addListBody'", $outputSigner1);  
$signer1_added = json_decode($outputSigner1[0], true);
// echo "SIGNER".var_dump($signer1_added);
// add signer 1 to document END 

// sing through API



  // return;
  $sql = "SELECT antecDet.operacao as operacao, fornecedores.updated as updated, fornecedores.razao as razao, fornecedores.email as email, fornecedores.clicksign_key as clicksign_key, fornecedores.cnpj as cnpj, fornecedores.cpf as cpf, fornecedores.representante as nome, antec.data as data_oper, antec.valor as valor, antec.id as id FROM antecipadas antec inner join antecipadasDetalhes antecDet on antecDet.antecipada = antec.id  inner join fornecedores on fornecedores.id = antec.fornecedor WHERE antec.id = ?";
  // echo "NOW ADD SIGNER 2>>>>";
  // $sql = " FROM antecipadas antec inner join fornecedores on fornecedores.tipo = 'cliente' and fornecedores.id = antec.fornecedor WHERE antec.id = ?";
  $stmt = prepared_query($con, $sql, [$id_operacao], 'i');
  $fornecedor = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  // echo "FORNEC IS".var_dump($fornecedor);
  $stmt->close(); 
  
$fornec_cnpj = $fornecedor[0]['cnpj'];
$signer2_retrieved = $fornecedor[0]["clicksign_key"];
foreach($fornecedor as $oper) {
  $operid = $oper['operacao'];
  $query = mysqli_query($con, "update operacoes set clicksign_key = '$document_key' where id = '$operid'");  
}
$oper = $fornecedor[0]['operacao'];

// echo "FOUND OPER".$oper;
// echo "UPDATING"."update operacoes set clicksign_key = '$document_key' where id = $oper";



if (is_null($signer2_retrieved) || $fornecedor[0]['updated'] == 1) {
  if (is_null($fornecedor[0]["email"])) {
    die();
  }
  $signer2 = (object) null;
  $signer2->signer = (object) array("email" => $fornecedor[0]["email"]);
  // $signer2->signer = (object) array("email" => "mrlobulo@gmail.com");
  $signer2->signer->auths = array("icp_brasil");
  // $signer2->signer->auths = array("email");
  $signer2->signer->name = $fornecedor[0]["nome"];  
  $signer2->signer->documentation = $fornecedor[0]["cpf"];
  $signer2->signer->selfie_enabled = false;
  $signer2->signer->handwritten_enabled = false;
  $signer2->signer->liveness_enabled = false;
  $signer2->signer->facial_biometrics_enabled = false;
  $signer2Body = json_encode($signer2, JSON_UNESCAPED_SLASHES);
  
  exec("curl --location --request POST 'https://$env.clicksign.com/api/v1/signers?access_token=$access_token' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --header 'Host: $env.clicksign.com' \
  -d '$signer2Body'", $outputAddSignature2);  
  
  // echo "LALLALA >>>> TRY TO ADD.".var_dump($outputAddSignature2);
  $signer2_retrieved = json_decode($outputAddSignature2[0], true);
  if ($signer2_retrieved['errors']) {
    $error = $signer2_retrieved['errors'][0];
    echo json_encode("{ erro : $error }");
    die();
  }
  $signer2_retrieved = $signer2_retrieved["signer"]["key"];
  $sql = "update fornecedores set clicksign_key = '$signer2_retrieved', updated = 0 where fornecedores.tipo = 'cliente' and fornecedores.cnpj = $fornec_cnpj";
  // echo "UPDATING FORNECEDOR >>>>>>>>".$sql;
  $query = mysqli_query($con, $sql);
  // $stmt = prepared_query($con, $sql, [$signer2_retrieved, $fornecedor[0]["cnpj"]], 'si');
  // echo "UPDAERT IS".$query;
  // $retrieve = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  // $stmt->close(); 
}

// add signer 2 to document_key
$addListSigner2 = (object) null;
$addListSigner2->list = (object) array("document_key" => $document_key);
$addListSigner2->list->signer_key = $signer2_retrieved;
$addListSigner2->list->sign_as = "party";
$addListSigner2->list->message = "Por favor, assine o documento.";
// $addListSigner2->list->name = "Allan Felipe Murara";
$addListBody2 = json_encode($addListSigner2, JSON_UNESCAPED_SLASHES);
// echo "ADD LIST BODY 2".$addListBody2;
exec("curl --location --request POST 'https://$env.clicksign.com/api/v1/lists?access_token=$access_token' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Host: $env.clicksign.com' \
-d '$addListBody2'", $outputSigner2);  
// echo "SIGNER ADDED 2 OUTPUT".var_dump($outputSigner2);
$signer2_added = json_decode($outputSigner2[0], true);
// add signer 1 to document END
$request_sign_body = (object) null;
$request_sign_body->request_signature_key = $signer2_added["list"]["request_signature_key"];
$request_sign_body->message = 'Por favor assine o documento.';


if ($signer2_added['errors']) {
  // echo  "ERROR>>>>".$signer2_retrieved_output['errors'];
  $error = $signer2_added['errors'][0];
  echo json_encode("{ erro : 'Erro ao adicionar assinante 2, contrato ficará pendente.' }");
  die();
}

if (is_null($signer2_added["list"])) {
  echo json_encode("{}");
  die();
} else {
  $request_signature_body = json_encode($request_sign_body, JSON_UNESCAPED_SLASHES);
  // echo "ADD LIST BODY 2".$addListBody2;
  exec("curl --location --request POST 'https://$env.clicksign.com/api/v1/notifications?access_token=$access_token' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --header 'Host: $env.clicksign.com' \
  -d '$request_signature_body'", $requestedSignature);  

  echo json_encode([]);
  
  require_once('/home/lawsmart/send_mail.php');
  $fornecedor[0]['clicksign_key'] = $document_key;
  $fornecedor[0]["fornecedor"] = $fornecedor[0]["razao"];
  $sql = "SELECT *, antecipadasDetalhes.valor as valor_antecip, ( select antecipadas.valor from antecipadas where id = ? ) as valor_antecip_liq FROM antecipadasDetalhes 
  inner join boletos on boletos.operacao = antecipadasDetalhes.operacao
  inner join operacoes on operacoes.id = antecipadasDetalhes.operacao
  inner join fornecedores on fornecedores.cnpj = operacoes.cnpj 
  WHERE antecipadasDetalhes.antecipada = ?";
    // return;
  $stmt = prepared_query($con, $sql, [$id_operacao,$id_operacao], 'ii');
  $antecip = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
  // 
	dispatch_event_mail('antecipacao_criada', $fornecedor[0]["email"], $fornecedor[0]["razao"], $fornecedor[0], $antecip);
}
// echo var_dump($outputSigner2);
// echo $arq;
// $signers = "{\"email\":\"gilberto@lawsecsa.com.br\",\"action\":\"SIGN\"},{\"email\":\"$email\",\"action\":\"SIGN\"}";
// $query = "\"query\": \"mutation CreateDocumentMutation(\$document: DocumentInput!, \$signers: [SignerInput!]!, \$file: Upload!) { createDocument(app: false, document: \$document, signers: \$signers, file: \$file) { id name refusable sortable created_at signatures { public_id name email created_at action { name } link { short_link } user { id name email }}}}\"";
// $variables = "\"variables\":{\"document\":{\"name\":\"Contrato Antecipação $ant\", \"qualified\":true},\"signers\":[$signers], \"file\": null }";
// $map = "{ \"0\": [\"variables.file\"] }";
// $file = "0=@$arq";
// exec("curl https://api.autentique.com.br/v2/graphql -H 'Connection: keep-alive' -H 'Authorization: Bearer 4ca37a7d5880040736b507e3c71ecf4b128a2c16be59a2c289f41278c7edae8d' -F operations='{ $query, $variables }' -F map='$map' -F $file --compressed 2>&1", $output);
// echo json_encode($output);

$sql = "SELECT secret_signerkey FROM assinante where 1 = 1";
$stmt = prepared_query($con, $sql, [], '');
$retrieve = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close(); 
$secret_signerkey = $retrieve[0]["secret_signerkey"];
$hmac_secret = hash_hmac('sha256', $signer1_added["list"]["request_signature_key"], $secret_signerkey);

$signBody = (object) null;
$signBody->request_signature_key = $signer1_added["list"]["request_signature_key"];
$signBody->secret_hmac_sha256 = $hmac_secret;

$signData = json_encode($signBody);

exec("curl 'https://$env.clicksign.com/api/v1/sign?access_token=$access_token' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Host: $env.clicksign.com' \
-d '$signData'", $outputTryToSign);  

?>