<?php
	include 'session_check.php';

	if (!isset($_POST['id']))
		header("location:miei_annunci.php");
	
	$response = array();
	
	//Apertura database
	include 'db_connect.php';
		
	// Dati dell'oggetto
	$strSQL = "SELECT * FROM Oggetto WHERE CodOgg = ".$_POST['id'];
	$query_result = mysql_query($strSQL);
	$obj = mysql_fetch_array($query_result);
	$response[] = array('ID'=>$obj['CodOgg'], 'Titolo'=>$obj['Nome'], 'Categoria'=>$obj['Categoria'], 'Prezzo'=>$obj['Prezzo'], 'Descrizione'=>$obj['Descrizione'], 'Telefono'=>$obj['Telefono'], 'EMail'=>$obj['EMail'], 'Mappa'=>$obj['Mappa']);
		
	mysql_close($db);
	
	// Invio risposta al client
	echo json_encode($response);
	die();
?>