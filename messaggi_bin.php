<?php
	include 'session_check.php';
	
	if (!isset($_POST['page']))
		header("location:messaggi.php");
	
	$response = array();
	
	$inizio = $_POST['page'] * 10;
	$fine = 10;
	
	//Apertura database
	include 'db_connect.php';

	// Conta i messaggi in inviati cestinati 
	$strSQL = "SELECT Codice ";
	$strSQL .= "FROM (SELECT * FROM Autenticazione, Messaggio WHERE ((IDUtente = Destinatario AND NomeUtente = '".$_SESSION['username']."' AND CestinoD = 1) OR ";
	$strSQL .= "(IDUtente = Mittente AND NomeUtente = '".$_SESSION['username']."' AND CestinoM = 1))) T ";
	$strSQL .= "ORDER BY Codice DESC";
	$query_result = mysql_query($strSQL);
	$conta = mysql_num_rows($query_result);
	
	// Messaggi cestinati 
	$strSQL = "SELECT Codice, U.NomeUtente , Oggetto, Data ";
	$strSQL .= "FROM (SELECT * FROM Autenticazione, Messaggio WHERE ((IDUtente = Destinatario AND NomeUtente = '".$_SESSION['username']."' AND CestinoD = 1) OR ";
	$strSQL .= "(IDUtente = Mittente AND NomeUtente = '".$_SESSION['username']."' AND CestinoM = 1))) T, (SELECT DISTINCT IDUtente, NomeUtente FROM Autenticazione) U ";
	$strSQL .= "WHERE U.IDUtente = T.Mittente ";
	$strSQL .= "ORDER BY Codice DESC LIMIT ".$inizio.",".$fine;
	$query_result = mysql_query($strSQL);

	for ($i = 0; $i < mysql_num_rows($query_result); $i++) {
		$obj = mysql_fetch_array($query_result);
		$response[] = array('Codice'=>$obj['Codice'], 'Utente'=>$obj['NomeUtente'], 'Oggetto'=>$obj['Oggetto'], 'Data'=>$obj['Data']);
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
