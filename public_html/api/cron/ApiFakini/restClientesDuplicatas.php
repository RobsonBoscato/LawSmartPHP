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
$base_url = 'http://protheustst.agricopel.com.br:1782/rest_hml/dupli_cli?Page=';
function registrarLog($mensagem) {
    // Caminho do arquivo de log
    $caminhoArquivoLog = 'ClientesDuplilog.txt';

    // Adiciona data e hora à mensagem
    $mensagemFormatada = date('Y-m-d H:i:s') . ' - ' . $mensagem . PHP_EOL;

    // Registra a mensagem no arquivo de log
    error_log($mensagemFormatada, 3, $caminhoArquivoLog);
}
registrarLog("Executado dia: ".date('d/m/Y H:i:s'));
// Função para verificar se uma string é um JSON válido
function isJson($string)
{
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

// Nome do arquivo para armazenar o valor de $page
$page_file_path = 'pageClientesDuplicatas.txt';

// Carrega o valor atual de $page a partir do arquivo
$page = file_exists($page_file_path) ? intval(file_get_contents($page_file_path)) : 1;
echo $page;

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

                $input_data['duplicataclienteCNPJ'];
                $input_data['duplicataNota'];
                $input_data['duplicataVencimento'];
                $stmtCheckExistence1 = $pdo->prepare('SELECT COUNT(*) FROM fornecedores WHERE cnpj = ?');
                $stmtCheckExistence1->execute([$input_data['duplicataclienteCNPJ']]);
                $countExistence1 = $stmtCheckExistence1->fetchColumn();
                if ($countExistence1 > 0) {


                    // Convert the string to a float before passing it to number_format
                    $valor = number_format($input_data['duplicataValor'], 2, '.', '');
                    // Verifique se o fornecedor já existe pelo CNPJ
                    $stmtCheckExistence = $pdo->prepare('SELECT * FROM `operacoes` WHERE `cnpj` =? and `nota` =? and tipo = ?');
                    $stmtCheckExistence->execute([$input_data['duplicataclienteCNPJ'], $input_data['duplicataNota'], 'cliente']);
                    $countExistence = $stmtCheckExistence->fetchColumn();

                    if ($countExistence > 0) {
                        echo json_encode(['error' => 'Nota já existe', 'Page' => $page]);
                    } else {
                        $dateString = $input_data['duplicataVencimento'];
                        $originalFormat = 'Ymd';
                        $desiredFormat = 'Y-m-d';

                        $dateTime = DateTime::createFromFormat($originalFormat, $dateString);

                        if ($dateTime instanceof DateTime && $dateTime->format($originalFormat) === $dateString) {
                            // Valid date
                            $formattedDate = $dateTime->format($desiredFormat);
                            $stmt = $pdo->prepare('INSERT INTO `operacoes`( `cnpj`, `nota`, `vencimento`, `valor`, `dataOPE`, `tipo`, `status`, `clicksign_key`, `confirmada`) VALUES (?,?,?,?,?,?,?,?,?)');
                            $stmt->execute([$input_data['duplicataclienteCNPJ'],  $input_data['duplicataNota'], $formattedDate, $valor, $formattedDate, 'cliente', 0, NULL, 0]);
                            echo json_encode(['success' => true, 'Page' => $page, 'message' => 'Nota adicionado com sucesso']);
                        } else {
                            echo json_encode(['error' => 'duplicataVencimento Null', 'Page' => $page]);
                        }
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
