<?php
/*
	Exemplo de arquivo para a conexão com o banco de dados.
	Substituir os seguintes dados:
	$dbhost pelo host do banco
	$dbuser pelo usuário
	$dbpass pela senha
	$dbname pelo nome do banco de dados
*/
	
$dbhost = "localhost";
$dbuser = "usr";
$dbpass = "pwd";
$dbname = "db";

$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if(mysqli_connect_errno()) {
	die("Database connection failed: " .
	     mysqli_connect_error() .
	     " (" . mysqli_connect_errno() . ")"
	);
}
?>
