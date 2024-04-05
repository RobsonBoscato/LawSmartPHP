<?php
session_start();

function my_autoload($pClassName)
{
	include('Class' . "/" . $pClassName . ".class.php");
}
spl_autoload_register("my_autoload");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header("Location: login.php");
        exit;
    }

    $email = htmlspecialchars($_POST["email"]);
    $senha = htmlspecialchars($_POST["senha"]);






    $usuario = new fornecedores();
    $usuario->setEmail($email);
    $usuario->setCnpj($senha);
    $result = $usuario->Login();

    if ($result) {
        $_SESSION["logado"] = true;
        $_SESSION["id"]     = $result["id"];
        $_SESSION["email"]  = $result["email"];
        $_SESSION["cnpj"]   = $result["cnpj"];
        $_SESSION["razao"]  = $result["razao"];
        $_SESSION["updated"] = $result["updated"];
      

        if (empty($result["email"]) || empty($result["cnpj"]) || empty($result["razao"]) || empty($result["representante"]) || empty($result["conta"]) || empty($result["banco"]) || empty($result["agencia"])) {
            $_SESSION["EfetuarOperacao"] = false;
        } else {
            $_SESSION["EfetuarOperacao"] = true;
        }

        if ($result["tipo"] == "fornecedor") {
            $_SESSION["tipo"] = $result["tipo"];
            header("Location: index.php");
            exit;
        } else {
            header("Location: clientes/index.php");
            $_SESSION["tipo"] = $result["tipo"];
            exit;
        }
    } else {
        $senha  =  hash('sha256',  $senha);
        $usuario = new ancora();
        $usuario->setEmail($email);
        $usuario->setSenha($senha);
        $ancora = $usuario->login();

        // var_dump($ancora);
        if ($ancora) {
            // senha é "Rm$9T#q2Xz@8P!2420"  em hash('sha256',  "Rm$9T#q2Xz@8P!2420");
            $_SESSION["client_id"] = 'bfedbaf659940bbf99293fee2fa5b4a9edc89e4569a35f99ecdaec8f1e44c7ca';
            $_SESSION["secret_id"] ='bd1d7e4a21f866ecbc7cc3cf071e48459b4dd782e2c352f92946a7a927981f19';
            $_SESSION["tipo"] = 'ancora';
            $_SESSION["logado"] = true;
            $_SESSION["razao"]  = $ancora['razao'];
            $_SESSION["id"]     = $ancora['id'];
            $_SESSION["email"]  = $ancora['email'];
            $_SESSION["cnpj"]   = $ancora['cnpj'];
            $_SESSION["updated"] = 1;
            header("Location: ancora/index.php");
            exit;
        } else {
            header("Location: login.php?msg=Senha e/ou Usuário inválido");
            exit;
        }
    }
}
