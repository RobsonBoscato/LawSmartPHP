<?php

function idAntecipacao($str) {
  $s1 = explode('_', $str);
  $s2 = explode('.', $s1[2]);
  
  return $s2[0];
}

$dados = json_decode(file_get_contents('php://input'), true);
$arq = $dados['arquivo'];
$ant = idAntecipacao($arq);
$email = $dados['emailEmpresa'];

$signers = "{\"email\":\"gilberto@lawsecsa.com.br\",\"action\":\"SIGN\"},{\"email\":\"$email\",\"action\":\"SIGN\"}";
$query = "\"query\": \"mutation CreateDocumentMutation(\$document: DocumentInput!, \$signers: [SignerInput!]!, \$file: Upload!) { createDocument(sandbox: false, document: \$document, signers: \$signers, file: \$file) { id name refusable sortable created_at signatures { public_id name email created_at action { name } link { short_link } user { id name email }}}}\"";
$variables = "\"variables\":{\"document\":{\"name\":\"Contrato Antecipação $ant\", \"qualified\":true},\"signers\":[$signers], \"file\": null }";
$map = "{ \"0\": [\"variables.file\"] }";
$file = "0=@$arq";
exec("curl https://api.autentique.com.br/v2/graphql -H 'Connection: keep-alive' -H 'Authorization: Bearer 4ca37a7d5880040736b507e3c71ecf4b128a2c16be59a2c289f41278c7edae8d' -F operations='{ $query, $variables }' -F map='$map' -F $file --compressed 2>&1", $output);
echo json_encode($output);

?>