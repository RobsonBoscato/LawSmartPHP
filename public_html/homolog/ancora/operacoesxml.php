<?php
 
 ob_start(); // Inicie o buffer de saída

 // Seu código PHP aqui
 
 // Define os cabeçalhos HTTP
 header('Content-type: application/xml');
 header('Content-Disposition: attachment; filename="seuarquivo.xml"');
 
include("../controle_sessao.php");
include("../config.php");

// Inicialize um objeto SimpleXMLElement com o elemento raiz
$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><dataset></dataset>');

$acao = mysqli_escape_string($lawsmt, $_GET['a']);

$status = mysqli_escape_string($lawsmt, 4);
$inicio = mysqli_escape_string($lawsmt, $_GET["inicio"]);
$termino = mysqli_escape_string($lawsmt, $_GET["termino"]);

$vertice = array();
$list = array();
$multiple_ids_query = "SELECT distinct GROUP_CONCAT(id_operacao) as multiple_ids, id_postergacao as id FROM `postergacoesDetalhes` 
															inner join operacoes on ( operacoes.id like postergacoesDetalhes.id_postergada )";
if ($status) {
    if ($status == "4") {
        $multiple_ids_query .= " and operacoes.status = 5";
        // echo $query;
    } else {
        $multiple_ids_query .= " and operacoes.status = 6";
    }
}
if ($inicio) {
    $multiple_ids_query .= " and operacoes.dataOPE >= '{$inicio}'";
    // die($query);
}

if ($termino) {
    $multiple_ids_query .= " and operacoes.dataOPE <= '{$termino}'";
}

$multiple_ids_query .= " group by id_postergacao HAVING count(postergacoesDetalhes.id) > 1";
$multiple_ids = mysqli_query($lawsmt, $multiple_ids_query);
while ($multiple_row = mysqli_fetch_assoc($multiple_ids)) {
    // $antecipadas_agrupadas_data = mysqli_fetch_assoc(mysqli_query($lawsmt, "select count(*) as total from operacoes where id in ({$multiple_row['multiple_ids']}) group by id"));
    // if ($antecipadas_agrupadas_data['total'] > 0) {
    $vertice[$multiple_row['id']] = explode(',', $multiple_row['multiple_ids']);
    for ($i = 0; $i < count($vertice[$multiple_row['id']]); $i++) {
        array_push($list, $vertice[$multiple_row['id']][$i]);
    }
    // }
}

// echo "MULTIPLE IDS ARE".$multiple_ids;
// echo "SELECT distinct *, IFNULL(antecipadas.valor, operacoes.valor) as valor, operacoes.confirmada as confirmada, IFNULL(antecipadas.id, operacoes.id) as id, operacoes.id as id_oper, antecipadas.id as real_antec_id, operacoes.status as statusReal, IFNULL(antecipadas.id,UUID()) as antec_id FROM operacoes left join antecipadasDetalhes on antecipadasDetalhes.operacao = operacoes.id left join antecipadas on antecipadas.id = antecipadasDetalhes.antecipada where operacoes.status != 0 AND operacoes.status != 4  group by antec_id ORDER BY `antecipadasDetalhes`.`antecipada` ASC LIMIT 10";
// $antecipadas = mysqli_query($lawsmt, "SELECT distinct *, IFNULL(antecipadas.valor, operacoes.valor) as valor, operacoes.confirmada as confirmada, IFNULL(antecipadas.id, operacoes.id) as id, operacoes.id as id_oper, antecipadas.id as real_antec_id, operacoes.status as statusReal, IFNULL(antecipadas.id,UUID()) as antec_id FROM operacoes left join antecipadasDetalhes on antecipadasDetalhes.operacao = operacoes.id left join antecipadas on antecipadas.id = antecipadasDetalhes.antecipada where operacoes.status != 0 AND operacoes.status != 4  group by antec_id ORDER BY `antecipadasDetalhes`.`antecipada` ASC LIMIT 10");
// $antecipadas_agrupadas = mysqli_query($lawsmt, "select * from operacoes ");
foreach ($vertice as $key => $value) {
    $values = implode(',', $value);
    // echo "select * from operacoes where id in ({$values}) group by vencimento";
    $antecipadas_agrupadas = mysqli_query($lawsmt, "select *, sum(valor) as valor from operacoes where id in ({$values}) group by cnpj");
    while ($row = mysqli_fetch_assoc($antecipadas_agrupadas)) {
        // echo "OVER ROW".var_dump($row);
        // $quantidade = mysqli_fetch_assoc(mysqli_query($lawsmt, "SELECT count(*) as totaldupli FROM antecipadas as a, antecipadasDetalhes as ad WHERE a.fornecedor = '{$_SESSION["id"]}' AND ad.antecipada = '{$row['id']}'")) or die(mysqli_error());
        $fornecedor = mysqli_fetch_assoc(mysqli_query($lawsmt, "SELECT * FROM fornecedores WHERE cnpj = '{$row['cnpj']}'")) or die(mysqli_error());
        // Crie um elemento de registro para cada conjunto de dados
        $record = $xml->addChild('record');

        $record->addChild('id',  '0000'.$key);
        $record->addChild('operacao', $row['id']); // Preencha com os dados apropriados
        $record->addChild('data_original', $row["dataOPE"]); // Preencha com os dados apropriados
        $record->addChild('data_final', $row["vencimento"]);
        $record->addChild('cnpj', $fornecedor["cnpj"]); // Preencha com os dados apropriados
        $record->addChild('valor', number_format($row['valorOriginal'], 2, ',', '.')); // Preencha com os dados apropriados
        $record->addChild('taxas', $row["taxas"]); // Preencha com os dados apropriados
        $record->addChild('juros', $row["juros"]); // Preencha com os dados apropriados
        $record->addChild('valor_final', number_format($row['valor'], 2, ',', '.')); // Preencha com os dados apropriados
?>



       
    <?php }
}
$antecipadas_query = "select distinct *, (select confirmada from operacoes where operacoes.id = postergacoesDetalhes.id_postergada) as confirmada_sec, IFNULL(antecipadas.valorOriginal, operacoes.valor) as valor, operacoes.confirmada as confirmada, IFNULL(antecipadas.id, operacoes.id) as id, operacoes.id as id_oper, antecipadas.id as real_antec_id, operacoes.status as statusReal, 
															IFNULL(antecipadas.id,UUID()) as antec_id FROM operacoes 
															left join antecipadasDetalhes on antecipadasDetalhes.operacao = operacoes.id 
															left join postergacoesDetalhes on operacoes.id = postergacoesDetalhes.id_operacao
															left join antecipadas on antecipadas.id = antecipadasDetalhes.antecipada where operacoes.status != 0 AND operacoes.status != 4 ";

if ($status) {
    if ($status == "4") {
        $antecipadas_query .= " and operacoes.status = 5";
        // echo $query;
    } else {
        $antecipadas_query .= " and operacoes.status = 6";
    }
}

if ($inicio) {
    $antecipadas_query .= " and operacoes.dataOPE >= '{$inicio}'";
    // die($query);
}

if ($termino) {
    $antecipadas_query .= " and operacoes.dataOPE <= '{$termino}'";
}

 $antecipadas_query .= " group by antec_id ORDER BY `antecipadasDetalhes`.`antecipada` ASC";

$antecipadas = mysqli_query($lawsmt, $antecipadas_query);
while ($row = mysqli_fetch_assoc($antecipadas)) {
    // $quantidade = mysqli_fetch_assoc(mysqli_query($lawsmt, "SELECT count(*) as totaldupli FROM antecipadas as a, antecipadasDetalhes as ad WHERE a.fornecedor = '{$_SESSION["id"]}' AND ad.antecipada = '{$row['id']}'")) or die(mysqli_error());
    $fornecedor = mysqli_fetch_assoc(mysqli_query($lawsmt, "SELECT * FROM fornecedores WHERE cnpj = '{$row['cnpj']}'")) or die(mysqli_error());

    if (in_array($row['id_oper'], $list) == 1) {
    } else {
        // Crie um elemento de registro para cada conjunto de dados
        $record = $xml->addChild('record');

        // Adicione os elementos filho com os dados relevantes do código PHP original
        $record->addChild('id',  '0000'.$key);
        $record->addChild('operacao', $row['id']); // Preencha com os dados apropriados
        $record->addChild('data_original', $row["dataOPE"]); // Preencha com os dados apropriados
        $record->addChild('data_final', $row["vencimento"]);
        $record->addChild('cnpj', $fornecedor["cnpj"]); // Preencha com os dados apropriados
        $record->addChild('valor', number_format($row['valorOriginal'], 2, ',', '.')); // Preencha com os dados apropriados
        $record->addChild('taxas', $row["taxas"]); // Preencha com os dados apropriados
        $record->addChild('juros', $row["juros"]); // Preencha com os dados apropriados
        $record->addChild('valor_final', number_format($row['valor'], 2, ',', '.')); // Preencha com os dados apropriados
    }
    ?>

<?php }

// Define a saída do XML formatada
$dom = dom_import_simplexml($xml)->ownerDocument;
$dom->formatOutput = true;
// Define o cabeçalho HTTP para indicar que é um arquivo XML

// Saída do XML

// Saída do XML
echo $dom->saveXML();

ob_end_flush(); // Encerre o buffer de saída e envie para o navegador
?>