<?php
	include 'session_check.php';
	
	if (!isset($_POST['id']))
		header("location:messaggi.php");
	
	$response = array();
	
	///Apertura database
	include 'db_connect.php';
	
	// Selezione del messaggio
	$strSQL = "SELECT * ";
	$strSQL .= "FROM Messaggio M ";
	$strSQL .= "WHERE Codice = ".$_POST['id']." ";
	$query_result = mysql_query($strSQL);
	$obj = mysql_fetch_array($query_result);
	
	// Mittente
	$strSQL = "SELECT NomeUtente AS NM FROM Autenticazione WHERE IDUtente = ".$obj['Mittente'];
	$query_result = mysql_query($strSQL);
	$tmp = mysql_fetch_array($query_result);
	
	// Destinatario
	$strSQL = "SELECT NomeUtente AS ND FROM Autenticazione WHERE IDUtente = ".$obj['Destinatario'];
	$query_result = mysql_query($strSQL);
	$tmp2 = mysql_fetch_array($query_result);

	$response[] = array('Codice'=>$obj['Codice'], 'Oggetto'=>$obj['Oggetto'], 'Contenuto'=>$obj['Contenuto'], 'Data'=>$obj['Data'], 'Ora'=>$obj['Ora'], 'Mittente'=>$tmp['NM'], 'Destinatario'=>$tmp2['ND']);
	
	// Segnalazione messaggio letto
	$strSQL = "UPDATE Messaggio SET Letto = 1 WHERE Codice = ".$_POST['id'];
	mysql_query($strSQL);

	mysql_close($db);
	
	// Invio risposta al client
	if (!empty($response)) {
		echo json_encode($response);
		die();
		}
?>