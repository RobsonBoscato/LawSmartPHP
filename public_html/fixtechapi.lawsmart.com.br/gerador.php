<?php
function generateClientCredentials($email, $password) {
    // Gere o client_id usando o hash SHA256 do email
    echo $client_id = hash('sha256', $email);

    // Gere o secret_id usando o hash SHA256 da combinação de email e senha
    $secret_id = hash('sha256', $email . $password);

    // Retorna um array contendo client_id e secret_id
    return array(
        'client_id' => $client_id,
        'secret_id' => $secret_id
    );
}

// Exemplo de uso da função
$email = 'fixtech@lawsmart.com.br';
$password = 'Rm$9T#q2Xz@8P!2420';

$credentials = generateClientCredentials($email, $password);

echo "client_id: " . $credentials['client_id'] . "<br>";
echo "secret_id: " . $credentials['secret_id'];
?>
