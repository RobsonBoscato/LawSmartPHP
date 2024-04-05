<?php
	session_start();
    try {
        $dsn = 'mysql:host=localhost;dbname=lawsmart_robv2;charset=utf8';
        $username = 'lawsmart_rob';
        $password = 'dire0300';
        
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo = $_POST['tipo']; // Assuming 'cnpj' is the POST parameter
            
            $sql = "SELECT * FROM `fornecedores` ";
            // $sql = "SELECT * FROM `fornecedores` WHERE `tipo` = :tipo";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
            $stmt->execute();
            
            $forn = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($forn) {
                echo json_encode($forn);
            } else {
                echo json_encode(['error' => 'Fornecedor not found']);
            }
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
    ?>
    