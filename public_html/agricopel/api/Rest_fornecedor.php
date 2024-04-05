<?php
$dbHost = 'localhost'; // MySQL Hostname
$dbUser = 'law_agricopel'; // MySQL Username
$dbPass = '4(xE}snlIP.w85po'; // MySQL Password
$dbName = 'law_agricopel'; // MySQL Database Name
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
$base_url = 'http://protheustst.agricopel.com.br:1782/rest_hml/Fornecedor_law?Page=';

// Função para verificar se uma string é um JSON válido
function isJson($string)
{
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

// Inicializa a página como 1
$page = 1;

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
                $tipo = 'fornecedor';
                $fornecedorRazao = $input_data['fornecedorRazao'];
                $fornecedorCNPJ = $input_data['fornecedorCNPJ'];
                $fornecedorEmail = $input_data['fornecedorEmail'];
                $fornecedorTelefone = $input_data['fornecedorTelefone'];
                $fornecedorTaxaJuros =  str_replace([' ', ','], '', $input_data['fornecedorTaxaJuros']);
                $fornecedorLimiteRaw = $input_data['fornecedorLimite'];

                // Removemos espaços em branco e vírgulas da string
                $fornecedorLimiteTrimmed = str_replace([' ', ',','.'], '', $fornecedorLimiteRaw);

                // Verificamos se a string resultante está vazia
                // Se estiver vazia, atribuímos o valor 0; caso contrário, usamos o valor original
                $fornecedorLimite = empty($fornecedorLimiteTrimmed) ? 0 : $fornecedorLimiteRaw;
                $fornecedorCustoBoleto = !empty($input_data['fornecedorCustoBoleto']) ? $input_data['fornecedorCustoBoleto'] : 0;
                $fornecedorTAC = !empty($input_data['fornecedorTAC']) ? $input_data['fornecedorTAC'] : 0;
                $fornecedorTED = !empty($input_data['fornecedorTED']) ? $input_data['fornecedorTED'] : 0;
                $fornecedorTED  = preg_replace("/[^0-9]/", "", $fornecedorTED);
                $fornecedorTAC  = preg_replace("/[^0-9]/", "", $fornecedorTAC);
                $fornecedorCustoBoleto  = preg_replace("/[^0-9]/", "", $fornecedorCustoBoleto);
                $fornecedorTaxaJuros  = preg_replace("/[^0-9]/", "", $fornecedorTaxaJuros);
                // Verifique se o fornecedor já existe pelo CNPJ
                $stmtCheckExistence = $pdo->prepare('SELECT COUNT(*) FROM fornecedores WHERE cnpj = ?');
                $stmtCheckExistence->execute([$fornecedorCNPJ]);
                $countExistence = $stmtCheckExistence->fetchColumn();

                if ($countExistence > 0) {
                    echo json_encode(['error' => 'Fornecedor com o mesmo CNPJ já existe','Page'=> $page]);
                } else {
                    // Inserir no banco de dados
                    $stmt = $pdo->prepare('INSERT INTO fornecedores (razao, cnpj,tipo, email, telefone, juros, limite, boleto, tac, ted) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?)');
                    $stmt->execute([$fornecedorRazao,  $fornecedorCNPJ, $tipo, $fornecedorEmail, $fornecedorTelefone, $fornecedorTaxaJuros, $fornecedorLimite, $fornecedorCustoBoleto, $fornecedorTAC, $fornecedorTED]);

                    echo json_encode(['success' => true, 'Page'=> $page, 'message' => 'Fornecedor adicionado com sucesso']);
                }
            }

            echo 'Consulta da página ' . $page . ' salva com sucesso em: ' . $file_path . '<br>';

            // Incrementa o número da página
            $page++;

            // Verifica se há mais páginas disponíveis
            if (empty($data)) {
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
