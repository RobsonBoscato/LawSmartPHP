<?php
$dbHost = '186.226.58.115'; // MySQL Hostname
$dbUser = 'law_fakini'; // MySQL Username
$dbPass = 'J6~[1K@UEjfE'; // MySQL Password
$dbName = 'law_fakini'; // MySQL Database Name
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
// URL do endpoint
$url = 'https://consistem.fakini.com.br:8080/api/cadastrosgerais/v10/fornecedor';
// Nome do arquivo para armazenar o valor de $page
$page_file_path = 'pageFornecedores.txt';

// Carrega o valor atual de $page a partir do arquivo
$page = file_exists($page_file_path) ? intval(file_get_contents($page_file_path)) : 1;
function registrarLog($mensagem)
{
    // Caminho do arquivo de log
    $caminhoArquivoLog = 'Fornecedoreslog.txt';

    // Adiciona data e hora à mensagem
    $mensagemFormatada = date('Y-m-d H:i:s') . ' - ' . $mensagem . PHP_EOL;

    // Registra a mensagem no arquivo de log
    error_log($mensagemFormatada, 3, $caminhoArquivoLog);
}
registrarLog("Executado dia: " . date('d/m/Y H:i:s'));
// Função para verificar se uma string é um JSON válido
function isJson($string)
{
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}
$continuationToken ='';
while (true) {
    // Headers da requisição
    $headers = array(
        'accept: application/json',
        'empresa: 1',
        'Authorization: eyJhbGciOiJFUzI1NiJ9.eyJzdWIiOiJpbnRlZ3JhY2FvMSIsImNzd1Rva2VuIjoicjhKQ0hjSmciLCJkYk5hbWVTcGFjZSI6ImNvbnNpc3RlbSIsImlzcyI6ImFwaSIsImF1ZCI6ImFwaSIsImV4cCI6MTg0ODQwODI5OH0.2TG3XQatYwxkhc8HMmGtIa9BS_WSE20F9BKrgcDlejq0WPl6SBKIjMCfa_7esoTzVfOeVvyJpRNV74pJk1jBvg'
    );

    // Inicialização da sessão cURL
    $ch = curl_init();

    // Configurações da requisição
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


    // Execução da requisição
    $response = curl_exec($ch);
    // Verifica erros
    if (curl_errno($ch)) {
        echo 'Erro na requisição cURL: ' . curl_error($ch);
    }

    // Fecha a sessão cURL
    curl_close($ch);

    if (!empty($response)) {
   
        // Verifica se a resposta é um JSON válido
        if (isJson($response)) {
            // Decodifica a resposta JSON
            $data = json_decode($response, true);
          
            if(!$data['continuationToken']){
                echo 'continuationToken = Null  ' ;
                exit;
            }
            
            $url = 'https://consistem.fakini.com.br:8080/api/cadastrosgerais/v10/fornecedor?continuationToken='.$data['continuationToken'];
         
            foreach ($data['data'] as $input_data) {
         
              

                // Acesso às variáveis do POST
                $tipo = 'fornecedor';
                $fornecedorRazao = $input_data['nome'];
                $fornecedorCNPJ = $input_data['cpfCnpj'];
                $fornecedorEmail = $input_data['email'];
                if($fornecedorEmail  ==''){
                    $fornecedorEmail = $input_data['codFornecedor'].'@alteraremail.teste';
                }
                $fornecedorTelefone = $input_data['telefone'];
                // $fornecedorTaxaJuros =  str_replace([' ', ','], '', $input_data['fornecedorTaxaJuros']);
                $fornecedorLimiteRaw = 0.00;
                $fornecedorLimite = number_format(floatval($fornecedorLimiteRaw), 2, '', '.');
                $fornecedorTAC = 60.00; //!empty($input_data['fornecedorTAC']) ? $input_data['fornecedorTAC'] : 0;
                $fornecedorTED = 15.00; //!empty($input_data['fornecedorTED']) ? $input_data['fornecedorTED'] : 0;
                $fornecedorCustoBoleto  = 4.00; //preg_replace("/[^0-9]/", "", $fornecedorCustoBoleto);
                $fornecedorTaxaJuros  = 2.80; //preg_replace("/[^0-9]/", "", $fornecedorTaxaJuros);
                // Verifique se o fornecedor já existe pelo CNPJ
                $stmtCheckExistence = $pdo->prepare('SELECT COUNT(*) FROM fornecedores WHERE cnpj = ?');
                $stmtCheckExistence->execute([$fornecedorCNPJ]);
                $countExistence = $stmtCheckExistence->fetchColumn();

                if ($countExistence > 0) {
                    echo json_encode(['error' => 'Fornecedor com o mesmo CNPJ já existe', 'Page' => $page]);
                } else {
                    $ANCORA   = 'CINQUE CAPITAL SECURITIZADORA S.A';
                    $VAR_NOME = $fornecedorRazao;
                    $LINK     =  'https://cinque.lawsmart.com.br';
                    $template = file_get_contents('../../../fakini/Class/fornecedores/law-chain.html');
                    $template = str_replace('{VAR_NOME}', $VAR_NOME, $template);
                    $template = str_replace('{ANCORA}', $ANCORA, $template);
                    $template = str_replace('{LINK}', $LINK, $template);
                    $template = str_replace('{VAR_PARAM1}',  $fornecedorEmail, $template);
                    $template = str_replace('{VAR_PARAM2}',  $fornecedorCNPJ, $template);
                    $recipient_email = $fornecedorEmail;
                    $subject         = 'Conheça a LAW Smart Chain';
                    $body            =  $template;
                    $status          = 'pendente';
                    $type            = 'SmartChain';   
                    $created_at      = date('Y-m-d H:i:s');
                    $sent_at         = null;
                    $stmt = $pdo->prepare('INSERT INTO `emails`(`recipient_email`, `subject`, `body`, `status`, `type`, `created_at`, `sent_at`) VALUES (?,?,?,?,?,?,?)');
                    //Descomentar Assim que form pra producao
                    $stmt->execute([$recipient_email,  $subject, $body, $status, $type, $created_at, $sent_at]);


                    // Inserir no banco de dados
                    $stmt = $pdo->prepare('INSERT INTO fornecedores (razao, cnpj,tipo, email, telefone, juros, limite, boleto, tac, ted,status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)');
                    $status = 1;
                    $stmt->execute([$fornecedorRazao,  $fornecedorCNPJ, $tipo, $fornecedorEmail, $fornecedorTelefone, $fornecedorTaxaJuros, $fornecedorLimite, $fornecedorCustoBoleto, $fornecedorTAC, $fornecedorTED,$status]);

                    echo json_encode(['success' => true, 'Page' => $page, 'message' => 'Fornecedor adicionado com sucesso']);
                }
          
                
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
