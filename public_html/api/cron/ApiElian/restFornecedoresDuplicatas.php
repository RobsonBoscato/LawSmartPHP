<?php
$dbHost = '186.226.58.115'; // MySQL Hostname
$dbUser = 'law_fintex'; // MySQL Username
$dbPass = 'ltr4fi@MPA*f'; // MySQL Password
$dbName = 'law_elian'; // MySQL Database Name
/* Change the database character set to something that supports the language you'll
   be using. Example, set this to utf16 if you use Chinese or Vietnamese characters */
$charset = 'utf8mb4';

/* Set this if you use a non-standard MySQL port. */
$dbPort = 3306;

/* Domain of cookie (99.99% chance you don't need to edit this at all) */
define('COOKIE_DOMAIN', '');
$page = 2000;
try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=$charset;port=$dbPort", $dbUser, $dbPass);

    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



} catch (PDOException $e) {
    // Handle connection errors
    echo "Connection failed: " . $e->getMessage();
}
// URL do endpoint
// Obter o ano atual
$anoAtual = date('Y');
$new_content = '';
// Calcular o ano dois anos à frente
$anoFuturo = $anoAtual + 5;

// Construir a URL com os anos atual e futuro
$url = 'https://csw.elian.com.br:5558/api/financeiro/v10/contasPagar?continuationToken=' . $data['continuationToken'];
// $url = 'https://csw.elian.com.br:5558/api/financeiro/v10/contasPagar?dataVencimentoIni=' . $anoAtual . '-01-01&dataVencimentoFim=' . $anoFuturo . '-12-31&continuationToken=' . $data['continuationToken'];

// $page_file_path = 'pageClientesDuplicatas.txt';

// // Carrega o valor atual de $page a partir do arquivo
// $page = file_exists($page_file_path) ? intval(file_get_contents($page_file_path)) : '';
// if ($page != '') {
//     $url .  '?continuationToken=' . $data['continuationToken'];
// }

while (true) {
    // Headers da requisição
    $headers = array(
        'accept: application/json',
        'empresa: 1',
        'Authorization: eyJhbGciOiJFUzI1NiJ9.eyJpc3MiOiJhcGkiLCJhdWQiOiJhcGkiLCJleHAiOjE4Njg3OTI0MzksInN1YiI6Imxhd3NlYy5lIiwiY3N3VG9rZW4iOiJCcDhDcTNaSiIsImRiTmFtZVNwYWNlIjoiY29uc2lzdGVtIn0.RW0ijPcx_dcUBiqrEz2leI19x22aXgB3fGcsPaxcRs0hQhCqMiJJ-Yn9hTbn18SHl7wK4DicW39ZC_oj2eqS8A'
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

    // Decodifica a resposta JSON em um array associativo
    $data = json_decode($response, true);
    // Verifica se houve erro na decodificação
    if ($data === null) {
        echo 'Erro ao decodificar a resposta JSON.';
    } else {
        $url = 'https://csw.elian.com.br:5558/api/financeiro/v10/contasPagar?continuationToken=' . $data['continuationToken'];
        // $url = 'https://csw.elian.com.br:5558/api/financeiro/v10/contasPagar?dataVencimentoIni=' . $anoAtual . '-01-01&dataVencimentoFim=' . $anoFuturo . '-01-01&continuationToken=' . $data['continuationToken'];
        
            // Itera sobre os elementos do array usando foreach
            foreach ($data as $item) {
                // Faça o que desejar com cada item
                // $countExistence1 = '';
                // $DADOSFORNECEDOR = '';
                if($data['continuationToken'] ==''){
           
                        echo "Ultimo token fim " .  $new_content;
                          exit;
           
                }
           
                foreach ($item as $r) {

                   
                    $fornecedorCNPJ =  $r['dadosCustomizados'][0]["valor"];
                    $possuiNF =  $r['dadosCustomizados'][1]["valor"];
                    $notaTeste = $r['numDocumento'];
                    if ($possuiNF == 1) {


                        $stmtCheckExistence1 = $pdo->prepare('SELECT COUNT(*) FROM fornecedores WHERE cnpj = ?');
                        $stmtCheckExistence1->execute([$fornecedorCNPJ]);
                        $countExistence1 = $stmtCheckExistence1->fetchColumn();


                        $DADOSFORNECEDOR = $pdo->prepare('SELECT * FROM fornecedores WHERE cnpj = ?');
                        $DADOSFORNECEDOR->execute([$fornecedorCNPJ]);
                        $DADOSFORNECEDOR = $DADOSFORNECEDOR->fetch();
                        // echo 'SELECT * FROM fornecedores WHERE cnpj = '.$fornecedorCNPJ.';'.PHP_EOL;  
                        // echo $r['numDocumento'];
                        // $item['dataVencimento'];
                        // $input_data['duplicataVencimento'];
                        //  var_dump($countExistence1);
                        if ($countExistence1 > 0) {
                            // echo $r['valorDocumento'].PHP_EOL; 
                            // Convert the string to a float before passing it to number_format
                            $valor = str_replace(['.', ','], ['', '.'], $r['valorDocumento']) . PHP_EOL;
                            // Verifique se o fornecedor já existe pelo CNPJ
                            $stmtCheckExistence = $pdo->prepare('SELECT * FROM `operacoes` WHERE `cnpj` =? and `nota` =? and tipo = ?');
                            $stmtCheckExistence->execute([$fornecedorCNPJ,  $r['numDocumento'].'/1', 'fornecedor']);
                            $countExistence = $stmtCheckExistence->fetchColumn();

                            if ($countExistence > 0) {
                                echo json_encode(['error' => 'Nota já existe', 'Nota' =>  $r['numDocumento'].'-1']);
                            } else {
                                $formattedDate = $r['dataVencimento']; //date('Y-m-d', strtotime($r['dataVencimento'])).PHP_EOL;  
                                // $originalFormat = 'Ymd';
                                // $desiredFormat = 'Y-m-d';
                                //  echo    $dateTime = DateTime::createFromFormat($originalFormat, $dateString);
                                // if ($dateTime instanceof DateTime && $dateTime->format($originalFormat) === $dateString) {
                                // $formattedDate = $dateTime->format($desiredFormat);
                                $stmt = $pdo->prepare('INSERT INTO `operacoes`( `cnpj`, `nota`, `vencimento`, `valor`, `dataOPE`, `tipo`, `status`, `clicksign_key`, `confirmada`) VALUES (?,?,?,?,?,?,?,?,?)');
                                $res =  $stmt->execute([$fornecedorCNPJ,   $r['numDocumento'].'/1', $formattedDate, $valor, $formattedDate, 'fornecedor', 0, NULL, 0]);
                                echo json_encode(['success' => true, 'Page' => $page, 'message' => 'Nota' .  $r['numDocumento'].'/1' . ' adicionado com sucesso, Vencimento dia ' . $formattedDate]);
                                // }
                                // else {
                                //     echo json_encode(['error' => 'duplicataVencimento Null', 'Data' => $formattedDate, 'Page' => $page]);
                                // }
                            }
                        }
                    }
                    else{
                        echo json_encode(['error' => 'Nota Com formato Inváido', 'Nota' => $r['numDocumento']]);
                    }
                }
            }
           
            $new_content = $data['continuationToken'];

      
  
                echo "Ultimo token " .  $new_content;
           
         
    }
}
