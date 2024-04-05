<?php
error_reporting(E_ALL);

$dbHost = '186.226.58.115'; // MySQL Hostname
$dbUser = 'law_fakini'; // MySQL Username
$dbPass = 'J6~[1K@UEjfE'; // MySQL Password
$dbName = 'law_fakini'; // MySQL Database Name
/* Change the database character set to something that supports the language you'll
   be using. Example, set this to utf16 if you use Chinese or Vietnamese characters */
$charset = 'utf8mb4';

/* Set this if you use a non-standard MySQL port. */
$dbPort = 3306;
// Definir ano inicial e final
$anoAtual = (int)date("Y");
$anoIni = $anoAtual;
$anoFim = $anoAtual + 25;
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
// $page_file_path = 'pageFornecedoresDuplicatas.txt';
// $page = file_exists($page_file_path) ? intval(file_get_contents($page_file_path)) : 1;
$page = 2000;
// URL do endpoint
$url = 'https://consistem.fakini.com.br:8080/api/financeiro/v10/contasPagar?situacao=0&origem=0,1&tipoDocumento=0&dataEmissaoIni=' . $anoIni . '-01-01&dataEmissaoFim=' . $anoFim . '-12-31&paginacao=' . $page;
while (true) {
    // echo $url = $base_url . $page;
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

    // Decodifica a resposta JSON em um array associativo
    $data = json_decode($response, true);


    // Verifica se houve erro na decodificação
    if ($data === null) {
        echo 'Erro ao decodificar a resposta JSON.';
        exit;
    } else {
        if (!$data['continuationToken']) {
            //     echo 'continuationToken= ' . $data['continuationToken'];
            //    exit;
        }
        // $page++;
        $new_content =  $page;

        // Salvar o novo conteúdo no arquivo
        // if (file_put_contents($page_file_path, $new_content) !== false) {
        //     echo "File saved successfully.";
        // } else {
        //     echo "Error saving file.";
        // }
        // Itera sobre os elementos do array usando foreach
        $continuationToken = $data['continuationToken'];

        $url = 'https://consistem.fakini.com.br:8080/api/financeiro/v10/contasPagar?situacao=0&origem=0,1&tipoDocumento=0&dataEmissaoIni=' . $anoIni . '-01-01&dataEmissaoFim=' . $anoFim . '-12-31&paginacao=' . $page . '&continuationToken=' . $data['continuationToken'];
        foreach ($data as $item) {
            // Faça o que desejar com cada item
            $countExistence1 = '';
            $DADOSFORNECEDOR = '';
            foreach ($item as $r) {
                if ($r['categoriaDoc'] !== 1 || $r['categoriaDoc'] !== 6 || $r['categoriaDoc'] !== 7 || $r['categoriaDoc'] !== 9 || $r['categoriaDoc'] !== 12 || $r['categoriaDoc'] !== 27) {




                    $fornecedorCNPJ =  $r['dadosCustomizados'][1]["valor"];

                    $stmtCheckExistence1 = $pdo->prepare('SELECT COUNT(*) FROM fornecedores WHERE cnpj = ?');
                    $stmtCheckExistence1->execute([$fornecedorCNPJ]);
                    $countExistence1 = $stmtCheckExistence1->fetchColumn();


                    $DADOSFORNECEDOR = $pdo->prepare('SELECT * FROM fornecedores WHERE cnpj = ?');
                    $DADOSFORNECEDOR->execute([$fornecedorCNPJ]);
                    $DADOSFORNECEDOR = $DADOSFORNECEDOR->fetch();
                    if ($countExistence1 > 0) {
                        $x =   str_replace(['.', ','], ['', '.'], $r['valorDocumento']);
                        $valor = number_format($x, 2, '.', '');
                        // Verifique se o fornecedor já existe pelo CNPJ
                        $stmtCheckExistence = $pdo->prepare('SELECT * FROM `operacoes` WHERE `cnpj` =? and `nota` =? and tipo = ?');
                        $stmtCheckExistence->execute([$fornecedorCNPJ,  $r['numDocumento'], 'fornecedor']);
                        $countExistence = $stmtCheckExistence->fetchColumn();

                        if ($countExistence > 0) {
                            echo json_encode(['error' => 'Nota já existe', 'Nota' => $r['numDocumento']]);
                        } else {

                            $stmt = $pdo->prepare('INSERT INTO `operacoes`( `cnpj`, `nota`, `vencimento`, `valor`, `dataOPE`, `tipo`, `status`, `clicksign_key`, `confirmada`) VALUES (?,?,?,?,?,?,?,?,?)');
                            $stmt->execute([$fornecedorCNPJ,  $r['numDocumento'], $r['dataVencimento'], $valor, $r['dataEmissao'], 'fornecedor', 0, NULL, 0]);
                            ///////////////////////////////////////
                            $dt = $r['dataVencimento'];
                            $nf = explode('/', $r['numDocumento']);
                            $juros = (floatval($DADOSFORNECEDOR['juros']) === 0) ? 2.5 : floatval($DADOSFORNECEDOR['juros']);
                            $ano = date('Y'); // Obtém o ano atual
                            $mes = date('n'); // Obtém o mês atual sem zero à esquerda

                            // Obter o número de dias no mês atual
                            $diasMes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
                            $diaSem = intval(date('N', strtotime($dt)));
                            $diasAd = 0;

                            switch ($diaSem) {
                                case 0:
                                    $diasAd += 2;
                                    break;
                                case 5:
                                    $diasAd += 3;
                                    break;
                                case 6:
                                    $diasAd += 3;
                                    break;
                            }
                            $dias = floor((strtotime($dt) - strtotime(date('Y-m-d'))) / (60 * 60 * 24)) + $diasAd;

                            $jurosDia = number_format($juros / $diasMes, 2);

                            $valorDesconto = number_format((floatval($valor) * ($jurosDia / 100)) * $dias, 2);


                            $trHTML = '<thead>
                                        <tr style="background-color:#e0e0e0;color:#000;text-align:center;font-weight:bold;text-transform:uppercase">
                                            <td style="padding:10px; white-space: nowrap;text-align:center;">Nota Fiscal</td>
                                            <td style="padding:10px; white-space: nowrap;text-align:center;">Parcela</td>
                                            <td style="padding:10px; white-space: nowrap;text-align:center;">A receber</td>
                                            <td style="padding:10px; white-space: nowrap;text-align:center;">Vencimento</td>
                                            <td style="padding:10px; white-space: nowrap;text-align:center;">Juros/mês</td>
                                            <td style="padding:10px; white-space: nowrap;text-align:center;">Dias</td>
                                            <td style="padding:10px; white-space: nowrap;text-align:center;">Descontos</td>
                                        </tr>
                                      </thead>
                                      <tbody>

                                      <tr style="background-color:#fff;color:#000;text-align:left;border:0px solid white">                                   
                                        <td  style="padding:10px">
                                            
                                            <span class="text-dark fw-bolder d-block fs-5">' . $nf[0] . '</span>
                                        </td>
                                        <td  style="padding:10px">
                                            
                                            <span class="text-dark fw-bolder d-block fs-5">' . $nf[1] . '</span>
                                        </td>
                                        <td  style="padding:10px">
                                            
                                            <span class="text-dark fw-bolder d-block fs-5">' . number_format($valor, 2) . '</span>
                                        </td>
                                        <td  style="padding:10px">
                                            
                                            <span class="text-dark fw-bolder d-block fs-5">' . date('d/m/Y', strtotime($r['dataVencimento'])) . '</span>
                                        </td>
                                        <td  style="padding:10px">
                                            
                                            <span class="text-dark fw-bolder d-block fs-5">' . $juros . '%</span>
                                        </td>
                                        <td  style="padding:10px">
                                            
                                            <span class="text-dark fw-bolder d-block fs-5">' . $dias . '</span>
                                        </td>
                                        <td  style="padding:10px">
                                            
                                            <span class="text-dark fw-bolder d-block fs-5">' . number_format((floatval($valor) * ($jurosDia / 100)) * $dias, 2) . '</span>
                                        </td>
                                        
                                        </tr>
                                        </tbody>

                                    ';

                            $VAR_NOME = $DADOSFORNECEDOR['razao'];
                            $LINK     =  'https://cinque.lawsmart.com.br/login.php';
                            if ($DADOSFORNECEDOR['tipo'] == 'fornecedor') {
                                $template = file_get_contents('../../../fakini/Class/fornecedores/aguardando-recebiveis.html');
                            }
                            if ($DADOSFORNECEDOR['tipo'] == 'cliente') {
                                $template = file_get_contents('../../../fakini/Class/clientes/aguardando-recebiveis.html');
                            }
                            $ANCORA   = 'CINQUE CAPITAL SECURITIZADORA S.A';
                            $template = str_replace('{VAR_NOME}', $VAR_NOME, $template);
                            $template = str_replace('{ANCORA}', $ANCORA, $template);
                            $template = str_replace('{LINK}', $LINK, $template);
                            $template = str_replace('{TABELA}',  $trHTML, $template);
                            $recipient_email = $DADOSFORNECEDOR['email'];
                            $subject         = 'Aguardando Recebíveis';
                            $body            =  $template;
                            $status          = 'pendente';
                            $type            = 'Disponíveis';
                            $created_at      = date('Y-m-d H:i:s');
                            $sent_at         = null;
                            $emails = $pdo->prepare('INSERT INTO `emails`(`recipient_email`, `subject`, `body`, `status`, `type`, `created_at`, `sent_at`) VALUES (?,?,?,?,?,?,?)');
                            $emails->execute([$recipient_email,  $subject, $body, $status, $type, $created_at, $sent_at]);
                            /////////////////////////////////////

                            echo json_encode(['success' => true, 'Nota' => $r['numDocumento'], 'message' => 'Nota adicionado com sucesso']);
                        }
                    } else {
                        echo json_encode(['error' => 'Fornecedor não encontrado', 'Nota' => $r['numDocumento']]);
                    }
                }
            }
        }
    }
}
