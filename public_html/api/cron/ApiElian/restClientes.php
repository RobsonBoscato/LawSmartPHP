<?php
$dbHost = '216.59.16.157'; // MySQL Hostname
$dbUser = 'lawsmart'; // MySQL Username
$dbPass = 'dire0300'; // MySQL Password
$dbName = 'lawsmart_agricopel'; // MySQL Database Name
/* Change the database character set to something that supports the language you'll
   be using. Example, set this to utf16 if you use Chinese or Vietnamese characters */
$charset = 'utf8mb4';

/* Set this if you use a non-standard MySQL port. */
$dbPort = 3306;

/* Domain of cookie (99.99% chance you don't need to edit this at all) */
define('COOKIE_DOMAIN', '');
try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=$charset;port=$dbPort", $dbUser, $dbPass);

    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // You can now use the $pdo object to perform database operations
    // For example, you can run queries like $pdo->query('SELECT * FROM your_table');

} catch (PDOException $e) {
    // Handle connection errors
    echo "Connection failed: " . $e->getMessage();
}

// URL base da API
$base_url = 'http://protheustst.agricopel.com.br:1782/rest_hml/CONSULTACLIENTE_LAW?Page=';
// Nome do arquivo para armazenar o valor de $page
$page_file_path = 'pageClientes.txt';

// Carrega o valor atual de $page a partir do arquivo
$page = file_exists($page_file_path) ? intval(file_get_contents($page_file_path)) : 1;
echo $page;
function registrarLog($mensagem)
{
    // Caminho do arquivo de log
    $caminhoArquivoLog = 'Clienteslog.txt';

    // Adiciona data e hora à mensagem
    $mensagemFormatada = date('Y-m-d H:i:s') . ' - ' . $mensagem . PHP_EOL;

    // Registra a mensagem no arquivo de log
    error_log($mensagemFormatada, 3, $caminhoArquivoLog);
}
registrarLog("Executado dia: " . date('d/m/Y H:i:s'));
function validaEmail($email)
{
    // Use FILTER_VALIDATE_EMAIL para uma validação mais robusta
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true;
    } else {
        return false;
    }
}

// Função para verificar se uma string é um JSON válido
function isJson($string)
{
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}



// Loop enquanto houver mais páginas a serem consultadas
while (true) {
    // Constrói a URL completa com o número da página
    $url = $base_url . $page;

    // Inicializa a sessão cURL
    $ch = curl_init($url);

    // Configura as opções da requisição cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Executa a requisição cURL e obtém a resposta
    $response = curl_exec($ch);

    // Verifica por erros durante a requisição cURL
    if (curl_errno($ch)) {
        echo 'Erro na requisição cURL: ' . curl_error($ch);
        exit;
    }

    // Fecha a sessão cURL
    curl_close($ch);

    // Verifica se a resposta não está vazia
    if (!empty($response)) {
        // Verifica se a resposta é um JSON válido
        if (isJson($response)) {
            // Decodifica a resposta JSON
            $data = json_decode($response, true);
            foreach ($data['Retorno'] as $input_data) {
                // Acesso às variáveis do POST
                $tipo = 'cliente';
                $clienteRazao = $input_data['clienteRazao'];
                $clienteCNPJ = $input_data['clienteCNPJ'];
                $clienteEmail = $input_data['clienteEmail'];
                $clienteTelefone = $input_data['clienteTelefone'];



                    $clienteLimite = floatval($input_data['clienteLimite']);

                    // Validate if $clienteLimite is greater than 0
                    if ($clienteLimite > 0) {
                        // Format the number with two decimal places using number_format
                        $clienteLimite = number_format($clienteLimite, 2, '.', '');
                    } else {
                        // If $clienteLimite is 0, set it to '0.00'
                        $clienteLimite = '0.00';
                    }

                    $clienteCustoBoleto = 5.00;
                    $clienteTAC = 100.00;
                    $clienteTED = 20.00;
                    $clienteTaxaJuros  = 4.00;
                    // Verifique se o fornecedor já existe pelo CNPJ
                    $stmtCheckExistence = $pdo->prepare('SELECT COUNT(*) FROM fornecedores WHERE cnpj = ? and email =?');
                    $stmtCheckExistence->execute([$clienteCNPJ, $clienteEmail]);
                    $countExistence = $stmtCheckExistence->fetchColumn();

                    if ($countExistence > 0) {
                        echo json_encode(['error' => 'Cliente com o mesmo CNPJ já existe', 'Page' => $page]);
                    } else {
                        // Inserir no banco de dados
                        $stmt = $pdo->prepare('INSERT INTO fornecedores (razao, cnpj,tipo, email, telefone, juros, limite, boleto, tac, ted) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?)');
                        if ($stmt->execute([$clienteRazao,  $clienteCNPJ, $tipo, $clienteEmail, $clienteTelefone, $clienteTaxaJuros, $clienteLimite, $clienteCustoBoleto, $clienteTAC, $clienteTED])) {
                            echo json_encode(['success' => true, 'Page' => $page, 'message' => 'Cliente adicionado com sucesso']);
                        } else {
                            echo json_encode(['error' => 'Cliente com o mesmo CNPJ já existe', 'Page' => $page]);
                        }
                    }
                



            }


            // Incrementa o número da página
            $page++;
            $new_content =  $page;

            // Salvar o novo conteúdo no arquivo
            if (file_put_contents($page_file_path, $new_content) !== false) {
                echo "File saved successfully.";
            } else {
                echo "Error saving file.";
            }
            // Verifica se há mais páginas disponíveis
            if (empty($data['Retorno'])) {



                break; // Sai do loop se não houver mais dados
            }
        } else {
            echo 'Erro: A resposta da página ' . $page . ' não é um JSON válido.';
            exit;
        }
    } else {
        echo 'Erro: A resposta da página ' . $page . ' está vazia.';
        exit;
    }
}
