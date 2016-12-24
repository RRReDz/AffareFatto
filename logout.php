<?php
	session_start();
	
	echo "Attendi...";
	
	//Apertura database
	include 'db_connect.php';
	
	/* Disconnetto dalla sessione
	$strSQL = "UPDATE Autenticazione SET Sessione = 0 WHERE NomeUtente ='".$_SESSION['username']."'";
	mysql_query($strSQL);
	mysql_close($db);*/
	
	//Elimino il cookie
	if(isset($_COOKIE['KLogIn']))
	setcookie ("KLogIn", $_POST['username'], time()-1);
	
	session_destroy();
	header("Location:index.php");
?>