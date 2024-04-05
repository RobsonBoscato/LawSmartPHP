<?php
$mysql_server = "localhost"; // endere�o do servidor mysql
$mysql_login  = "lawsmart_root"; // login para conex�o ao banco de dados
$mysql_senha  = "4(xE}snlIP.w85po"; // senha para conex�o ao banco de dados
$mysql_db     = "lawsmart_db"; // nome da base de dados a ser utilizada 
$lawsmt = mysqli_connect($mysql_server, $mysql_login, $mysql_senha, $mysql_db);
mysqli_query($lawsmt, "SET NAMES 'utf8'");
?>