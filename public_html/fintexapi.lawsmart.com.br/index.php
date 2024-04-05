<?php

// Defina o autoload para carregar automaticamente as classes
spl_autoload_register(function ($className) {
    include  $className . '.class.php';
});

// Inicializa a API
$api = new API();
$api->addRoute('GET', '/status', 'Status@index');
$api->addRoute('POST', '/authorization', 'AuthLaw@authorization');
$api->addRoute('GET', '/validation', 'AuthLaw@validation');
$api->addRoute('GET', '/fornecedores/{page}', 'AuthLaw@fornecedores');
$api->addRoute('GET', '/clientes', 'AuthLaw@clientes');
$api->addRoute('GET', '/fornecedoresCNPJ/{cnpj}', 'AuthLaw@fornecedor');
$api->addRoute('GET', '/clienteCNPJ/{cnpj}', 'AuthLaw@fornecedor');
$api->addRoute('GET', '/fornecedoresNotas/{data}', 'AuthLaw@antecipadas');
$api->addRoute('GET', '/clientesNotas/{data}', 'AuthLaw@postergadas');
$api->run();