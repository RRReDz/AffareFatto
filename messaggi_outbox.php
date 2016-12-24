<?php
	include 'session_check.php';
	
	if (!isset($_POST['page']))
		header("location:messaggi.php");
	
	$response = array();
	
	$inizio = $_POST['page'] * 10;
	$fine = 10;
	
	//Apertura database
	include 'db_connect.php';
	
	// Selezione dei messaggi in uscita
	$strSQL = "SELECT Codice, T.NomeUtente, Oggetto, Data, Ora, Letto, CestinoM ";
	$strSQL .= "FROM Autenticazione A, Messaggio M, (SELECT IDUtente, NomeUtente FROM Autenticazione, Messaggio WHERE IDUtente = Destinatario GROUP BY NomeUtente) T ";
	$strSQL .= "WHERE Mittente = A.IDUtente AND A.NomeUtente = '".$_SESSION['username']."' AND T.IDUtente = Destinatario AND CestinoM = 0 ORDER BY Codice DESC";
	$query_result = mysql_query($strSQL);
	$conta = mysql_num_rows($query_result);
	
	// Selezione dei messaggi in uscita
	$strSQL = "SELECT Codice, T.NomeUtente, Oggetto, Data, Ora, Letto, CestinoM ";
	$strSQL .= "FROM Autenticazione A, Messaggio M, (SELECT IDUtente, NomeUtente FROM Autenticazione, Messaggio WHERE IDUtente = Destinatario GROUP BY NomeUtente) T ";
	$strSQL .= "WHERE Mittente = A.IDUtente AND A.NomeUtente = '".$_SESSION['username']."' AND T.IDUtente = Destinatario AND CestinoM = 0 ";
	$strSQL .= "ORDER BY Codice DESC LIMIT ".$inizio.",".$fine;
	$query_result = mysql_query($strSQL);
	for ($i = 0; $i < mysql_num_rows($query_result); $i++) {
		$obj = mysql_fetch_array($query_result);
		$response[] = array('Codice'=>$obj['Codice'], 'Utente'=>$obj['NomeUtente'], 'Oggetto'=>$obj['Oggetto'], 'Data'=>$obj['Data'], 'Ora'=>$obj['Ora'], 'Letto'=>1);
		}
		
	mysql_close($db);
	
	// Invio risposta al client
	if (!empty($response)) {
		echo json_encode(array("data"=>$response, "num"=>$conta, "page"=>$_POST['page']));
		die();
		}
	
	// Invio risposta al client
	$response[]= array('ID'=>'vuoto');
	echo json_encode(array("data"=>$response));
	die();
?>