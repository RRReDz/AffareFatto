<?php 
	session_start();

	/*if (!$_SESSION['username'])
		header("location:index.php");
	
	/* Controllo se la sessione dell'utente è la stessa
	include 'db_connect.php';
	$strSQL = "SELECT Sessione FROM Autenticazione WHERE Sessione = ".$_SESSION['id'];
	$query_result = mysql_query($strSQL);
	$utente = mysql_fetch_array($query_result);
	// Se la sessione dell'utente è diversa da quella memorizzata sul database
	if ($_SESSION['id'] != $utente['Sessione']) {
		$strSQL = "UPDATE Autenticazione SET Sessione = 0 WHERE NomeUtente ='".$_SESSION['username']."'";
		mysql_query($strSQL);
		mysql_close($db);
		session_destroy();
		header("Location:error_multiple_login.php");
		}
	mysql_close($db);
	
	// Elimina l'immagine
	if (isset($_SESSION['immagine'])) {
		unlink($_SESSION['immagine']);
		unset($_SESSION['immagine']);
		}
	session_write_close();*/
?>