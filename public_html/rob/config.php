<?php
$mysql_server = "localhost"; // endere�o do servidor mysql
$mysql_login  = "lawsmart_rob"; // login para conex�o ao banco de dados
$mysql_senha  = "dire0300"; // senha para conex�o ao banco de dados
$mysql_db     = "lawsmart_rob"; // nome da base de dados a ser utilizada 
$lawsmt = mysqli_connect($mysql_server, $mysql_login, $mysql_senha, $mysql_db);
mysqli_query($lawsmt, "SET NAMES 'utf8'");
?>