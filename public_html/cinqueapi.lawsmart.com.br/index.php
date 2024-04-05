<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Defina o autoload para carregar automaticamente as classes
spl_autoload_register(function ($className) {
    include  $className . '.class.php';
});

// Inicializa a API
$api = new API();

//Status api
 $api->addRoute('GET', '/status', 'Status@index');


// Define as rotas
// $api->addRoute('GET', '/users', 'UserController@index');
// $api->addRoute('GET', '/users/{id}', 'UserController@show');
// $api->addRoute('POST', '/users', 'UserController@store');
// $api->addRoute('PUT', '/users/{id}', 'UserController@update');
// $api->addRoute('DELETE', '/users/{id}', 'UserController@delete');

// Executa authorization
$api->addRoute('POST', '/authorization', 'AuthLaw@authorization');
$api->addRoute('GET', '/validation', 'AuthLaw@validation');
$api->addRoute('GET', '/fornecedores/{page}', 'AuthLaw@fornecedores');
$api->addRoute('GET', '/clientes', 'AuthLaw@clientes');
$api->addRoute('GET', '/fornecedoresCNPJ/{cnpj}', 'AuthLaw@fornecedor');
$api->addRoute('GET', '/clienteCNPJ/{cnpj}', 'AuthLaw@fornecedor');
$api->addRoute('GET', '/fornecedoresNotas/{data}', 'AuthLaw@antecipadas');
$api->addRoute('GET', '/clientesNotas/{data}', 'AuthLaw@postergadas');



$api->run();
