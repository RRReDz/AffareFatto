<?php
	session_start();
	if (!$_SESSION['username'])
		header("location:index.php");
	
	if (!isset($_POST['email']))
		header("location:profilo.php");
	
	$response = array();
	
	//Apertura database
	include 'db_connect.php';
	
	//Controllo presenza Email
	if (strlen($_POST['email']) == 0) 
		$response[] = array('campo'=>'email', 'valore'=>'Devi compilare il campo E-Mail');
		
	// Invio risposta al client
	if (!empty($response)) {
		echo json_encode($response);
		die();
	}
	
	//Ricavo Email da Nomeutente
	$strSQL = "SELECT EMail FROM Utente WHERE IDUtente = ( SELECT IDUtente FROM Autenticazione WHERE NomeUtente = '".$_SESSION['username']."' )";
	$query_result = mysql_query($strSQL);
	$row = mysql_fetch_array($query_result) or die(mysql_error());
	
	// Controllo presenza E-Mail nel database
	if ($_POST['email'] != $row['EMail']){
		$strSQL = "SELECT * FROM Utente WHERE EMail LIKE '".$_POST['email']."' LIMIT 1";
		$query_result = mysql_query($strSQL);
		if (mysql_num_rows($query_result) > 0 ) {
			$response[] = array('campo'=>'email','valore'=>'Inserisci una nuova mail');
			echo json_encode($response);
			die();
		}
	}
	
	//Inserimento nuovi valori
	$strSQL = "UPDATE Utente SET Indirizzo = '".$_POST['addr']."', Paese = '".$_POST['town']."', CAP = '".$_POST['CAP']."', Provincia = '".$_POST['distr']."', EMail = '".$_POST['email']."', Telefono = '".$_POST['tphone']."', Immagine = '".$_POST['immagine']."' ";
	$strSQL .= "WHERE IDUtente = ( SELECT IDUtente FROM Autenticazione WHERE NomeUtente = '".$_SESSION['username']."')";
	mysql_query($strSQL);
	mysql_close($db);
	
	if (isset($_SESSION['immagine']))
		unset($_SESSION['immagine']);
	
	// Invio risposta al client
	$response[]= array('campo'=>'ok', 'valore'=>'profilo.php');
	echo json_encode($response);
	die();	?>