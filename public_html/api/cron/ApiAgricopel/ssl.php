<?php

// Caminho para o arquivo .p12
$p12_file = 'LAWSECSA32527198000152.p12';

// Senha para acessar o arquivo .p12
$p12_password = '1234';

// Leitura do conteúdo do arquivo .p12
$p12_content = file_get_contents($p12_file);

// Tentativa de abrir o arquivo .p12
if (!$p12_content) {
    die("Erro ao abrir o arquivo .p12");
}

// Tentativa de decodificar o arquivo .p12
if (!openssl_pkcs12_read($p12_content, $certs, $p12_password)) {
    die("Falha ao decodificar o arquivo .p12");
}

// Extraindo a chave privada
$private_key = openssl_pkey_get_private($certs['pkey'], $p12_password);

// Verificando se a chave privada foi obtida com sucesso
if (!$private_key) {
    die("Não foi possível obter a chave privada do certificado");
}

// Convertendo a chave privada para o formato PEM
$private_key_pem = "";
openssl_pkey_export($private_key, $private_key_pem);

// Exibindo a chave privada no formato PEM
echo $private_key_pem;

?>
