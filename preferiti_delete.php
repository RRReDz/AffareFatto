<?php
	include 'session_check.php';
	
	if (!isset($_POST['IDOggetto']))
		header("location:preferiti.php");
	
	$response = array();

	//Apertura database
	include 'db_connect.php';
	
	//Eliminazione Oggetto
	$strSQL = "DELETE FROM Preferiti WHERE Oggetto = ".$_POST['IDOggetto'];
	$query_result = mysql_query($strSQL);
	
	//Controllo inserimento dati annuncio
	if (mysql_affected_rows() == -1) {
		mysql_close($db);
		$response[] = array('campo'=>'output', 'valore'=>'false');
		echo json_encode($response);
		die();
		}
		
	mysql_close($db);
	
	// Invio risposta al client
	$response[]= array('campo'=>'output', 'valore'=>'true');
	echo json_encode($response);
	die();
?>