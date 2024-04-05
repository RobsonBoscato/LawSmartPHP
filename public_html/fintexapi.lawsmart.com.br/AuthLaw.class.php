<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include 'ConexaoMysqlAncora.php';
include 'ConexaoMysql.php';
include 'fornecedores.class.php';
include 'antecipadas.class.php';
include 'postergacoes.class.php';


require 'vendor/autoload.php';

use DateTimeImmutable;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\JwtHandler;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use function var_dump;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Validation\Constraint\RelatedTo;
use Lcobucci\JWT\Validation\Validator;

class AuthLaw extends ConexaoMysqlAncora
{
    public function authorization()
    {

        if (!isset($_SERVER['HTTP_X_CLIENT_ID'])) {
            if (!isset($_SERVER['HTTP_X_SECRET_ID'])) {
                // Se o cabeçalho Authorization não estiver presente, envia uma mensagem de erro
                header('HTTP/1.0 401 Unauthorized');
                echo 'Autenticação necessária.';
                exit;
            }
        }
        // Acessando os cabeçalhos
        $client_id = $_SERVER['HTTP_X_CLIENT_ID'] ?? '';
        $secret_id = $_SERVER['HTTP_X_SECRET_ID'] ?? '';


        $token_users = new token_users();
        $token_users->setClientId($client_id);
        $token_users->setSecretId($secret_id);
        $res = $token_users->CheckToken();
        if ($res) {
            // Acessando os dados no corpo da solicitação JSON
            $json_data = file_get_contents('php://input');
            $request_data = json_decode($json_data, true);




            $key = InMemory::plainText(
                $client_id
            );

            $token = (new JwtFacade())->issue(
                new Sha256(),
                $key,
                static fn (
                    Builder $builder,
                    DateTimeImmutable $issuedAt
                ): Builder => $builder
                    ->issuedBy('https://fintexapi.lawsmart.com.br')
                    // ->permittedFor('https://client-app.io')
                    ->relatedTo($client_id)
                    ->issuedAt($issuedAt)
                    // Configures the time that the token can be used (nbf claim)
                    ->canOnlyBeUsedAfter($issuedAt->modify('+1 minute'))
                    // Configures the expiration time of the token (exp claim)
                    ->expiresAt($issuedAt->modify('+1 hour'))
                    // Configures a new claim, called "uid"
                    ->withHeader('client_id', $client_id)
            );

            // var_dump($token->claims()->all());
            echo json_encode($token->toString());
            exit;
        } else {
            header('HTTP/1.0 401 Unauthorized');
            echo 'Autenticação inválida.';
            exit;
        }
    }
    public function validation()
    {
        $token = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
        $parser = new Parser(new JoseEncoder());
        // Analisa o token JWT corretamente
        $token = $parser->parse($token);
        $validator = new Validator();
        // Substitua 'client_id' pela variável que contém o ID real do cliente
        if (!$validator->validate($token, new RelatedTo('bfedbaf659940bbf99293fee2fa5b4a9edc89e4569a35f99ecdaec8f1e44c7ca'))) {
            return true;
        } else {
            return false;
        }
    }


    public function fornecedores()
    {
        $token = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
        // Recupere o caminho da URL
        $request_uri = $_SERVER['REQUEST_URI'];
        // Divida a URL usando a barra como delimitador
        $url_parts = explode('/', $request_uri);
        // O CNPJ está na última parte da URL
        $page = end($url_parts);
        $parser = new Parser(new JoseEncoder());
        // Analisa o token JWT corretamente
        $token = $parser->parse($token);
        $validator = new Validator();
        // Substitua 'client_id' pela variável que contém o ID real do cliente
        if ($validator->validate($token, new RelatedTo('bfedbaf659940bbf99293fee2fa5b4a9edc89e4569a35f99ecdaec8f1e44c7ca'))) {
            $fornecedores = new fornecedores();
            $fornecedores->setTipo('fornecedor');

            // Definindo o limite de resultados por página
            $limit = 20;

            // Calculando o offset com base no número da página e no limite de resultados por página
            $offset = ($page - 1) * $limit;

            // Chamando o método SelectTipo com os parâmetros de offset e limite
            $res = $fornecedores->SelectTipo($offset, $limit);

            if ($res) {
                // Define o cabeçalho Content-Type como application/json
                header('Content-Type: application/json');

                // Cria um array associativo com a mensagem desejada
                $response = array("status" => 200, "data" => $res);

                // Converte o array em JSON e o imprime
                echo json_encode($response);
                return;
            } else {
                // Define o cabeçalho Content-Type como application/json
                header('Content-Type: application/json');

                // Cria um array associativo com a mensagem desejada
                $response = array("status"=>200, "message" => 'Sem dados');

                // Converte o array em JSON e o imprime
                echo json_encode($response);
            }
        } else {
            http_response_code(401); // Unauthorized
            header('Content-Type: application/json');
            echo json_encode(array("message" => "Token JWT inválido."));
            exit();
        }
    }
    public function clientes()
    {

        $token = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
        $parser = new Parser(new JoseEncoder());
        // Analisa o token JWT corretamente
        $token = $parser->parse($token);
        $validator = new Validator();
        // Substitua 'client_id' pela variável que contém o ID real do cliente
        if ($validator->validate($token, new RelatedTo('bfedbaf659940bbf99293fee2fa5b4a9edc89e4569a35f99ecdaec8f1e44c7ca'))) {
            $fornecedores = new fornecedores();
            $fornecedores->setTipo('cliente');
            $res =  $fornecedores->SelectTipo();
            if ($res) {
                // Define o cabeçalho Content-Type como application/json
                header('Content-Type: application/json');

                // Cria um array associativo com a mensagem desejada
                $response = array("status" => 200, "data" => $res);

                // Converte o array em JSON e o imprime
                echo json_encode($response);
            } else {
                // Define o cabeçalho Content-Type como application/json
                header('Content-Type: application/json');

                // Cria um array associativo com a mensagem desejada
                $response = array("status", "message" => 'Erro 940');

                // Converte o array em JSON e o imprime
                echo json_encode($response);
            }
        } else {
            http_response_code(401); // Unauthorized
            header('Content-Type: application/json');
            echo json_encode(array("message" => "Token JWT inválido."));
            exit();
        }
    }
    public function fornecedor()
    {
        // Recupere o caminho da URL
        $request_uri = $_SERVER['REQUEST_URI'];

        // Divida a URL usando a barra como delimitador
        $url_parts = explode('/', $request_uri);

        // O CNPJ está na última parte da URL
        $cnpj = end($url_parts);


        $token = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
        $parser = new Parser(new JoseEncoder());
        // Analisa o token JWT corretamente
        $token = $parser->parse($token);
        $validator = new Validator();
        // Substitua 'client_id' pela variável que contém o ID real do cliente
        if ($validator->validate($token, new RelatedTo('bfedbaf659940bbf99293fee2fa5b4a9edc89e4569a35f99ecdaec8f1e44c7ca'))) {
            $fornecedores = new fornecedores();
            $fornecedores->setCnpj($cnpj);
            $res =  $fornecedores->SelectCnpj();
            if ($res) {
                // Define o cabeçalho Content-Type como application/json
                header('Content-Type: application/json');

                // Cria um array associativo com a mensagem desejada
                $response = array("status" => 200, "data" => $res);

                // Converte o array em JSON e o imprime
                echo json_encode($response);
            } else {
                // Define o cabeçalho Content-Type como application/json
                header('Content-Type: application/json');

                // Cria um array associativo com a mensagem desejada
                $response = array("status" => 200, "data" => null);

                // Converte o array em JSON e o imprime
                echo json_encode($response);
            }
        } else {
            http_response_code(401); // Unauthorized
            header('Content-Type: application/json');
            echo json_encode(array("message" => "Token JWT inválido."));
            exit();
        }
    }

    public function antecipadas()
    {
        // Recupere o caminho da URL
        $request_uri = $_SERVER['REQUEST_URI'];

        // Divida a URL usando a barra como delimitador
        $url_parts = explode('/', $request_uri);

        // O CNPJ está na última parte da URL
        $data = end($url_parts);


        $token = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
        $parser = new Parser(new JoseEncoder());
        // Analisa o token JWT corretamente
        $token = $parser->parse($token);
        $validator = new Validator();
        // Substitua 'client_id' pela variável que contém o ID real do cliente
        if ($validator->validate($token, new RelatedTo('bfedbaf659940bbf99293fee2fa5b4a9edc89e4569a35f99ecdaec8f1e44c7ca'))) {

            $antecipadas = new antecipadas();
            $antecipadas->setData($data);
            $res = $antecipadas->antecipadas_Boleto_Data();
                var_dump($res);

            $retorno = array();
            if ($res) {
                // Define o cabeçalho Content-Type como application/json
                header('Content-Type: application/json');

                foreach ($res as $boleto) {

                    $data_vencimento = explode('-', $boleto["vencimento"]);
                    $dadosboleto["data_vencimento"] = $data_vencimento[2] . '/' . $data_vencimento[1] . '/' . $data_vencimento[0]; //vencimento
                    $dadosboleto["valor_boleto"] =  $boleto["valor"]; //valor
                    $dadosboleto["nosso_numero"] = $boleto["nosso_numero"]; //nosso_numero
                    $dadosboleto["identificador"] = $boleto["operacao"]; //operação
                    $dadosboleto["numero_documento"] = $boleto["nosso_numero"];    // nosso_numero
                    $data_emissao =  explode('-', $boleto["emissao"]);
                    $dadosboleto["data_documento"] = $data_emissao[2] . '/' . $data_emissao[1] . '/' . $data_emissao[0]; // data emissão

                    // DADOS DO SEU CLIENTE
                    $dadosboleto["sacado"] = $boleto["razao"];
                    $dadosboleto["endereco1"] = $boleto["rua"] . "," . $boleto["bairro"] . "," . $boleto["numero"];
                    $dadosboleto["endereco2"] = $boleto["cidade"] . "," . $boleto["estado"] . " - " . $boleto["cep"];

                    // INFORMACOES PARA O CLIENTE
                    $dadosboleto["demonstrativo1"] = "";
                    $dadosboleto["demonstrativo2"] = "";
                    $dadosboleto["demonstrativo3"] = "";

                    $dadosboleto["cpf_cnpj"] = $boleto["cnpj"];
                    $dadosboleto["endereco"] = "";
                    $dadosboleto["cidade_uf"] = "";
                    $dadosboleto["cedente"] = "LAWSEC S/A";


                    // hard-coded
                    $dadosboleto["agencia"] = '0862';
                    $dadosboleto["conta"] = '99570';
                    $dadosboleto["conta_dv"] = '1';
                    $dadosboleto["carteira"] = '109';
                    $dadosboleto["especie"] = "DM";

                    $dadosboleto["instrucoes1"] = "- Cobrar multa de 3% após vencimento";
                    $dadosboleto["instrucoes2"] = "- Cobrar mora de 5% após vencimento";
                    $dadosboleto["instrucoes3"] = "";
                    $dadosboleto["instrucoes4"] = "";

                    $codigobanco = "341";



                    $codigo_banco_com_dv = geraCodigoBanco($codigobanco);
                    $nummoeda = "9";


                    $fator_vencimento = fator_vencimento($dadosboleto["data_vencimento"]);

                    //valor tem 10 digitos, sem virgula
                    $valor = formata_numero($dadosboleto["valor_boleto"], 10, 0, "valor");
                    $valor_numerico = str_pad(str_replace('.', '', $valor), 10, '0', STR_PAD_LEFT);
                    //agencia é 4 digitos
                    $agencia = formata_numero($dadosboleto["agencia"], 4, 0);
                    //conta é 5 digitos + 1 do dv
                    $conta = formata_numero($dadosboleto["conta"], 5, 0);
                    $conta_dv = formata_numero($dadosboleto["conta_dv"], 1, 0);
                    //carteira 175
                    $carteira = $dadosboleto["carteira"];
                    //nosso_numero no maximo 8 digitos
                    $nnum = formata_numero($dadosboleto["nosso_numero"], 8, 0);

                    $num_codigo_barras_campo1 = $codigobanco . $nummoeda . $carteira . substr($dadosboleto['nosso_numero'], 0, 2);
                    $num_codigo_barras_campo1 .= modulo_10($num_codigo_barras_campo1);

                    $num_codigo_barras_campo2 = substr($dadosboleto['nosso_numero'], 2, 6) . modulo_10($agencia . $conta . $carteira . $dadosboleto['nosso_numero']) . substr($agencia, 0, 3);
                    $num_codigo_barras_campo2 .= modulo_10($num_codigo_barras_campo2);

                    $num_codigo_barras_campo3 = substr($agencia, 3, 1) . $dadosboleto['conta'] . $dadosboleto['conta_dv'] . '000';
                    $num_codigo_barras_campo3 .= modulo_10($num_codigo_barras_campo3);

                    $num_codigo_barras_campo5 = $fator_vencimento . $valor_numerico;

                    $num_codigo_barras_campo4 = modulo_11($num_codigo_barras_campo1 . $num_codigo_barras_campo2 . $num_codigo_barras_campo3 . $num_codigo_barras_campo5);

                    $num_codigo_barras = $num_codigo_barras_campo1 . '.' . $num_codigo_barras_campo2 . '.' . $num_codigo_barras_campo3 . '.' . $num_codigo_barras_campo4 . '.' . $num_codigo_barras_campo5;
                    $codigo_barras = $num_codigo_barras_campo1 . '.' . $num_codigo_barras_campo2 . '.' . $num_codigo_barras_campo3 . '.' . $num_codigo_barras_campo4 . '.' . $num_codigo_barras_campo5;
                    // 43 numeros para o calculo do digito verificador
                    // $dv = digitoVerificador_barra($codigo_barras);
                    // var_dump($dv);

                    // Numero para o codigo de barras com 44 digitos
                    $linha = $codigo_barras;

                    $nossonumero = $carteira . '/' . $nnum . '-' . modulo_10($agencia . $conta . $carteira . $nnum);
                    // var_dump($nossonumero);
                    $agencia_codigo = $agencia . " / " . $conta . "-" . modulo_10($agencia . $conta);

                    $dadosboleto["codigo_barras"] = $codigo_barras;
                    $dadosboleto["linha_digitavel"] = $linha; // verificar
                    $dadosboleto["agencia_codigo"] = $agencia_codigo;
                    $dadosboleto["nosso_numero"] = $nossonumero;
                    $dadosboleto["codigo_banco_com_dv"] = $codigo_banco_com_dv;
                    // var_dump($dadosboleto);

                    $row = ['nota' => $boleto["nota"], 'cpf_cnpj' => $dadosboleto["cpf_cnpj"], 'data_vencimento' => $dadosboleto["data_vencimento"], 'data_operacao' => $boleto["data"], 'cod_bar' => $dadosboleto["codigo_barras"], 'nosso_numero' => $dadosboleto["nosso_numero"], 'linha_digitavel' => $dadosboleto["linha_digitavel"]];
                    array_push($retorno, $row);
                    // var_dump($retorno);

                }





                // Cria um array associativo com a mensagem desejada
                $response = array("status" => 200, "data" => $retorno);

                // Converte o array em JSON e o imprime
                echo json_encode($response);
            } else {
                // Define o cabeçalho Content-Type como application/json
                header('Content-Type: application/json');

                // Cria um array associativo com a mensagem desejada
                $response = array("status", "message" => 'Erro 940');

                // Converte o array em JSON e o imprime
                echo json_encode($response);
            }
        } else {
            http_response_code(401); // Unauthorized
            header('Content-Type: application/json');
            echo json_encode(array("message" => "Token JWT inválido."));
            exit();
        }
    }

    public function postergadas()
    {
        // Recupere o caminho da URL
        $request_uri = $_SERVER['REQUEST_URI'];

        // Divida a URL usando a barra como delimitador
        $url_parts = explode('/', $request_uri);

        // O CNPJ está na última parte da URL
        $data = end($url_parts);


        $token = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
        $parser = new Parser(new JoseEncoder());
        // Analisa o token JWT corretamente
        $token = $parser->parse($token);
        $validator = new Validator();
        // Substitua 'client_id' pela variável que contém o ID real do cliente
        if ($validator->validate($token, new RelatedTo('bfedbaf659940bbf99293fee2fa5b4a9edc89e4569a35f99ecdaec8f1e44c7ca'))) {

            $postergacoes = new postergacoes();
            $postergacoes->setData($data);
            $res = $postergacoes->postergadas_Boleto_Data();

            $retorno = array();
            if ($res) {
                // Define o cabeçalho Content-Type como application/json
                header('Content-Type: application/json');

                foreach ($res as $boleto) {

                    $data_vencimento = explode('-', $boleto["vencimento"]);
                    $dadosboleto["data_vencimento"] = $data_vencimento[2] . '/' . $data_vencimento[1] . '/' . $data_vencimento[0]; //vencimento
                    $dadosboleto["valor_boleto"] =  $boleto["valor"]; //valor
                    $dadosboleto["nosso_numero"] = $boleto["nosso_numero"]; //nosso_numero
                    $dadosboleto["identificador"] = $boleto["operacao"]; //operação
                    $dadosboleto["numero_documento"] = $boleto["nosso_numero"];    // nosso_numero
                    $data_emissao =  explode('-', $boleto["emissao"]);
                    $dadosboleto["data_documento"] = $data_emissao[2] . '/' . $data_emissao[1] . '/' . $data_emissao[0]; // data emissão

                    // DADOS DO SEU CLIENTE
                    $dadosboleto["sacado"] = $boleto["razao"];
                    $dadosboleto["endereco1"] = $boleto["rua"] . "," . $boleto["bairro"] . "," . $boleto["numero"];
                    $dadosboleto["endereco2"] = $boleto["cidade"] . "," . $boleto["estado"] . " - " . $boleto["cep"];

                    // INFORMACOES PARA O CLIENTE
                    $dadosboleto["demonstrativo1"] = "";
                    $dadosboleto["demonstrativo2"] = "";
                    $dadosboleto["demonstrativo3"] = "";

                    $dadosboleto["cpf_cnpj"] = $boleto["cnpj"];
                    $dadosboleto["endereco"] = "";
                    $dadosboleto["cidade_uf"] = "";
                    $dadosboleto["cedente"] = "LAWSEC S/A";


                    // hard-coded
                    $dadosboleto["agencia"] = '0862';
                    $dadosboleto["conta"] = '99570';
                    $dadosboleto["conta_dv"] = '1';
                    $dadosboleto["carteira"] = '109';
                    $dadosboleto["especie"] = "DM";

                    $dadosboleto["instrucoes1"] = "- Cobrar multa de 3% após vencimento";
                    $dadosboleto["instrucoes2"] = "- Cobrar mora de 5% após vencimento";
                    $dadosboleto["instrucoes3"] = "";
                    $dadosboleto["instrucoes4"] = "";

                    $codigobanco = "341";



                    $codigo_banco_com_dv = geraCodigoBanco($codigobanco);
                    $nummoeda = "9";


                    $fator_vencimento = fator_vencimento($dadosboleto["data_vencimento"]);

                    //valor tem 10 digitos, sem virgula
                    $valor = formata_numero($dadosboleto["valor_boleto"], 10, 0, "valor");
                    $valor_numerico = str_pad(str_replace('.', '', $valor), 10, '0', STR_PAD_LEFT);
                    //agencia é 4 digitos
                    $agencia = formata_numero($dadosboleto["agencia"], 4, 0);
                    //conta é 5 digitos + 1 do dv
                    $conta = formata_numero($dadosboleto["conta"], 5, 0);
                    $conta_dv = formata_numero($dadosboleto["conta_dv"], 1, 0);
                    //carteira 175
                    $carteira = $dadosboleto["carteira"];
                    //nosso_numero no maximo 8 digitos
                    $nnum = formata_numero($dadosboleto["nosso_numero"], 8, 0);

                    $num_codigo_barras_campo1 = $codigobanco . $nummoeda . $carteira . substr($dadosboleto['nosso_numero'], 0, 2);
                    $num_codigo_barras_campo1 .= modulo_10($num_codigo_barras_campo1);

                    $num_codigo_barras_campo2 = substr($dadosboleto['nosso_numero'], 2, 6) . modulo_10($agencia . $conta . $carteira . $dadosboleto['nosso_numero']) . substr($agencia, 0, 3);
                    $num_codigo_barras_campo2 .= modulo_10($num_codigo_barras_campo2);

                    $num_codigo_barras_campo3 = substr($agencia, 3, 1) . $dadosboleto['conta'] . $dadosboleto['conta_dv'] . '000';
                    $num_codigo_barras_campo3 .= modulo_10($num_codigo_barras_campo3);

                    $num_codigo_barras_campo5 = $fator_vencimento . $valor_numerico;

                    $num_codigo_barras_campo4 = modulo_11($num_codigo_barras_campo1 . $num_codigo_barras_campo2 . $num_codigo_barras_campo3 . $num_codigo_barras_campo5);

                    $num_codigo_barras = $num_codigo_barras_campo1 . '.' . $num_codigo_barras_campo2 . '.' . $num_codigo_barras_campo3 . '.' . $num_codigo_barras_campo4 . '.' . $num_codigo_barras_campo5;
                    $codigo_barras = $num_codigo_barras_campo1 . '.' . $num_codigo_barras_campo2 . '.' . $num_codigo_barras_campo3 . '.' . $num_codigo_barras_campo4 . '.' . $num_codigo_barras_campo5;
                    // 43 numeros para o calculo do digito verificador
                    // $dv = digitoVerificador_barra($codigo_barras);
                    // var_dump($dv);

                    // Numero para o codigo de barras com 44 digitos
                    $linha = $codigo_barras;

                    $nossonumero = $carteira . '/' . $nnum . '-' . modulo_10($agencia . $conta . $carteira . $nnum);
                    // var_dump($nossonumero);
                    $agencia_codigo = $agencia . " / " . $conta . "-" . modulo_10($agencia . $conta);

                    $dadosboleto["codigo_barras"] = $codigo_barras;
                    $dadosboleto["linha_digitavel"] = $linha; // verificar
                    $dadosboleto["agencia_codigo"] = $agencia_codigo;
                    $dadosboleto["nosso_numero"] = $nossonumero;
                    $dadosboleto["codigo_banco_com_dv"] = $codigo_banco_com_dv;
                    // var_dump($dadosboleto);

                    $row = ['nota' => $boleto["nota"], 'cpf_cnpj' => $dadosboleto["cpf_cnpj"], 'data_vencimento' => $dadosboleto["data_vencimento"], 'data_operacao' => $boleto["data"], 'limiteCliente' => $boleto["limiteCliente"], 'limite_utilizado' => $boleto["limite_utilizado"], 'total_postergacoes' => $boleto["total_postergacoes"], 'cod_bar' => $dadosboleto["codigo_barras"], 'nosso_numero' => $dadosboleto["nosso_numero"], 'linha_digitavel' => $dadosboleto["linha_digitavel"]];
                    array_push($retorno, $row);
                    // var_dump($retorno);

                }





                // Cria um array associativo com a mensagem desejada
                $response = array("status" => 200, "data" => $retorno);

                // Converte o array em JSON e o imprime
                echo json_encode($response);
            } else {
                // Define o cabeçalho Content-Type como application/json
                header('Content-Type: application/json');

                // Cria um array associativo com a mensagem desejada
                $response = array("status", "message" => 'Erro 940');

                // Converte o array em JSON e o imprime
                echo json_encode($response);
            }
        } else {
            http_response_code(401); // Unauthorized
            header('Content-Type: application/json');
            echo json_encode(array("message" => "Token JWT inválido."));
            exit();
        }
    }
}

// FUNÇÕES
// Algumas foram retiradas do Projeto PhpBoleto e modificadas para atender as particularidades de cada banco

function digitoVerificador_barra($numero)
{

    $resto2 = modulo_11($numero, 9, 1);

    $digito = 11 - $resto2;
    if ($digito == 0 || $digito == 1 || $digito == 10  || $digito == 11) {
        $dv = 1;
    } else {
        $dv = $digito;
    }
    return $dv;
}

function formata_numero($numero, $loop, $insert, $tipo = "geral")
{
    if ($tipo == "geral") {
        $numero = str_replace(",", "", $numero);
        while (strlen($numero) < $loop) {
            $numero = $insert . $numero;
        }
    }
    if ($tipo == "valor") {
        /*
		retira as virgulas
		formata o numero
		preenche com zeros
		*/
        $numero = str_replace(",", "", $numero);
        while (strlen($numero) < $loop) {
            $numero = $insert . $numero;
        }
    }
    if ($tipo == "convenio") {
        while (strlen($numero) < $loop) {
            $numero = $numero . $insert;
        }
    }
    return $numero;
}


function fbarcode($valor)
{

    $fino = 1;
    $largo = 3;
    $altura = 50;

    $barcodes[0] = "00110";
    $barcodes[1] = "10001";
    $barcodes[2] = "01001";
    $barcodes[3] = "11000";
    $barcodes[4] = "00101";
    $barcodes[5] = "10100";
    $barcodes[6] = "01100";
    $barcodes[7] = "00011";
    $barcodes[8] = "10010";
    $barcodes[9] = "01010";
    for ($f1 = 9; $f1 >= 0; $f1--) {
        for ($f2 = 9; $f2 >= 0; $f2--) {
            $f = ($f1 * 10) + $f2;
            $texto = "";
            for ($i = 1; $i < 6; $i++) {
                $texto .=  substr($barcodes[$f1], ($i - 1), 1) . substr($barcodes[$f2], ($i - 1), 1);
            }
            $barcodes[$f] = $texto;
        }
    }


    //Desenho da barra


    //Guarda inicial
?><img src=imagens/p.png width=<?php echo $fino ?> height=<?php echo $altura ?> border=0><img src=imagens/b.png width=<?php echo $fino ?> height=<?php echo $altura ?> border=0><img src=imagens/p.png width=<?php echo $fino ?> height=<?php echo $altura ?> border=0><img src=imagens/b.png width=<?php echo $fino ?> height=<?php echo $altura ?> border=0><img <?php
                                                                                                                                                                                                                                                                                                                                                            $texto = $valor;
                                                                                                                                                                                                                                                                                                                                                            if ((strlen($texto) % 2) <> 0) {
                                                                                                                                                                                                                                                                                                                                                                $texto = "0" . $texto;
                                                                                                                                                                                                                                                                                                                                                            }

                                                                                                                                                                                                                                                                                                                                                            // Draw dos dados
                                                                                                                                                                                                                                                                                                                                                            while (strlen($texto) > 0) {
                                                                                                                                                                                                                                                                                                                                                                $i = round(esquerda($texto, 2));
                                                                                                                                                                                                                                                                                                                                                                $texto = direita($texto, strlen($texto) - 2);
                                                                                                                                                                                                                                                                                                                                                                $f = $barcodes[$i];
                                                                                                                                                                                                                                                                                                                                                                for ($i = 1; $i < 11; $i += 2) {
                                                                                                                                                                                                                                                                                                                                                                    if (substr($f, ($i - 1), 1) == "0") {
                                                                                                                                                                                                                                                                                                                                                                        $f1 = $fino;
                                                                                                                                                                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                                                                                                                                                                        $f1 = $largo;
                                                                                                                                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                                                                                                                            ?> src=imagens/p.png width=<?php echo $f1 ?> height=<?php echo $altura ?> border=0><img <?php
                                                                                                                                                                                                                                                                                                                                                                    if (substr($f, $i, 1) == "0") {
                                                                                                                                                                                                                                                                                                                                                                        $f2 = $fino;
                                                                                                                                                                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                                                                                                                                                                        $f2 = $largo;
                                                                                                                                                                                                                                                                                                                                                                    }
                                                                                        ?> src=imagens/b.png width=<?php echo $f2 ?> height=<?php echo $altura ?> border=0><img <?php
                                                                                                                                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                                                                                                                            }

                                                                                                                                                                                                                                                                                                                                                            // Draw guarda final
                                                                                        ?> src=imagens/p.png width=<?php echo $largo ?> height=<?php echo $altura ?> border=0><img src=imagens/b.png width=<?php echo $fino ?> height=<?php echo $altura ?> border=0><img src=imagens/p.png width=<?php echo 1 ?> height=<?php echo $altura ?> border=0>
<?php
} //Fim da função

function esquerda($entra, $comp)
{
    return substr($entra, 0, $comp);
}

function direita($entra, $comp)
{
    return substr($entra, strlen($entra) - $comp, $comp);
}

function fator_vencimento($data)
{
    $data = explode("/", $data);
    $ano = $data[2];
    $mes = $data[1];
    $dia = $data[0];
    return (abs((_dateToDays("1997", "10", "07")) - (_dateToDays($ano, $mes, $dia))));
}

function _dateToDays($year, $month, $day)
{
    $century = substr($year, 0, 2);
    $year = substr($year, 2, 2);
    if ($month > 2) {
        $month -= 3;
    } else {
        $month += 9;
        if ($year) {
            $year--;
        } else {
            $year = 99;
            $century--;
        }
    }
    return (floor((146097 * $century)    /  4) +
        floor((1461 * $year)        /  4) +
        floor((153 * $month +  2) /  5) +
        $day +  1721119);
}

function modulo_10($num)
{
    $numtotal10 = 0;
    $fator = 2;

    // Separacao dos numeros
    for ($i = strlen($num); $i > 0; $i--) {
        // pega cada numero isoladamente
        $numeros[$i] = substr($num, $i - 1, 1);
        // Efetua multiplicacao do numero pelo (falor 10)
        // 2002-07-07 01:33:34 Macete para adequar ao Mod10 do Itaú
        $temp = $numeros[$i] * $fator;
        $temp0 = 0;
        foreach (preg_split('//', $temp, -1, PREG_SPLIT_NO_EMPTY) as $k => $v) {
            $temp0 += $v;
        }
        $parcial10[$i] = $temp0; //$numeros[$i] * $fator;
        // monta sequencia para soma dos digitos no (modulo 10)
        $numtotal10 += $parcial10[$i];
        if ($fator == 2) {
            $fator = 1;
        } else {
            $fator = 2; // intercala fator de multiplicacao (modulo 10)
        }
    }

    // várias linhas removidas, vide função original
    // Calculo do modulo 10
    $resto = $numtotal10 % 10;
    $digito = 10 - $resto;
    if ($resto == 0) {
        $digito = 0;
    }

    return $digito;
}

function modulo_11($num, $base = 9, $r = 0)
{
    /**
     *   Autor:
     *           Pablo Costa <pablo@users.sourceforge.net>
     *
     *   Função:
     *    Calculo do Modulo 11 para geracao do digito verificador 
     *    de boletos bancarios conforme documentos obtidos 
     *    da Febraban - www.febraban.org.br 
     *
     *   Entrada:
     *     $num: string numérica para a qual se deseja calcularo digito verificador;
     *     $base: valor maximo de multiplicacao [2-$base]
     *     $r: quando especificado um devolve somente o resto
     *
     *   Saída:
     *     Retorna o Digito verificador.
     *
     *   Observações:
     *     - Script desenvolvido sem nenhum reaproveitamento de código pré existente.
     *     - Assume-se que a verificação do formato das variáveis de entrada é feita antes da execução deste script.
     */

    $soma = 0;
    $fator = 2;

    /* Separacao dos numeros */
    for ($i = strlen($num); $i > 0; $i--) {
        // pega cada numero isoladamente
        $numeros[$i] = substr($num, $i - 1, 1);
        // Efetua multiplicacao do numero pelo falor
        $parcial[$i] = $numeros[$i] * $fator;
        // Soma dos digitos
        $soma += $parcial[$i];
        if ($fator == $base) {
            // restaura fator de multiplicacao para 2 
            $fator = 1;
        }
        $fator++;
    }

    /* Calculo do modulo 11 */
    if ($r == 0) {
        $soma *= 10;
        $digito = $soma % 11;
        if ($digito == 10) {
            $digito = 0;
        }
        return $digito;
    } elseif ($r == 1) {
        $resto = $soma % 11;
        return $resto;
    }
}

// Alterada por Glauber Portella para especificação do Itaú
function monta_linha_digitavel($codigo)
{
    // campo 1
    $banco    = substr($codigo, 0, 3);
    $moeda    = substr($codigo, 3, 1);
    $ccc      = substr($codigo, 19, 3);
    $ddnnum   = substr($codigo, 22, 2);
    $dv1      = modulo_10($banco . $moeda . $ccc . $ddnnum);
    // campo 2
    $resnnum  = substr($codigo, 24, 6);
    $dac1     = substr($codigo, 30, 1); //modulo_10($agencia.$conta.$carteira.$nnum);
    $dddag    = substr($codigo, 31, 3);
    $dv2      = modulo_10($resnnum . $dac1 . $dddag);
    // campo 3
    $resag    = substr($codigo, 34, 1);
    $contadac = substr($codigo, 35, 6); //substr($codigo,35,5).modulo_10(substr($codigo,35,5));
    $zeros    = substr($codigo, 41, 3);
    $dv3      = modulo_10($resag . $contadac . $zeros);
    // campo 4
    $dv4      = substr($codigo, 4, 1);
    // campo 5
    $fator    = substr($codigo, 5, 4);
    $valor    = substr($codigo, 9, 10);

    $campo1 = substr($banco . $moeda . $ccc . $ddnnum . $dv1, 0, 5) . '.' . substr($banco . $moeda . $ccc . $ddnnum . $dv1, 5, 5);
    $campo2 = substr($resnnum . $dac1 . $dddag . $dv2, 0, 5) . '.' . substr($resnnum . $dac1 . $dddag . $dv2, 5, 6);
    $campo3 = substr($resag . $contadac . $zeros . $dv3, 0, 5) . '.' . substr($resag . $contadac . $zeros . $dv3, 5, 6);
    $campo4 = $dv4;
    $campo5 = $fator . $valor;

    return "$campo1 $campo2 $campo3 $campo4 $campo5";
}

function geraCodigoBanco($numero)
{
    $parte1 = substr($numero, 0, 3);
    $parte2 = modulo_11($parte1);
    return $parte1 . "-" . $parte2;
}
