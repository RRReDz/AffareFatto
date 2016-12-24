<?php
	include 'session_check.php';
	
	if (!isset($_POST['nome']) || !isset($_POST['oggetto']) || !isset($_POST['descrizione']))
		header("location:messaggi.php");
	
	$response = array();
	
	//Controllo compilazione campi
	foreach ($_POST as $key => $value) {
		if ($value == "") 
			$response[] = array('campo'=>$key, 'valore'=>'Devi compilare questo campo.');
		}
	
	// Controllo se il destinario è il mittente
	if ($_POST['nome'] == $_SESSION['username']) 
		$response[] = array('campo'=>'nome', 'valore'=>'Non puoi inviare un messaggio a te stesso.');
		
	// Invio risposta al client
	if (!empty($response)) {
		echo json_encode($response);
		die();
		}
	
	//Apertura database
	include 'db_connect.php';
		
	$testo = mysql_real_escape_string($_POST['descrizione']);	
		
	//Inserimento dati utente
	$strSQL = "SELECT IDUtente FROM Autenticazione WHERE NomeUtente = '".$_SESSION['username']."'";
	$user = mysql_fetch_array(mysql_query($strSQL));
	$strSQL = "SELECT IDUtente FROM Autenticazione WHERE NomeUtente = '".$_POST['nome']."'";
	$query_result = mysql_query($strSQL);
	if (mysql_num_rows($query_result) == 0) {
		mysql_close($db);
		$response[] = array('campo'=>'nome', 'valore'=>'Nome utente non presente.');
		echo json_encode($response);
		die();
		}
	$user1 = mysql_fetch_array($query_result);
	$strSQL = "INSERT Messaggio (Mittente, Destinatario, Oggetto, Contenuto, Data, Ora) ";
	$strSQL .= "VALUES (".$user['IDUtente'].", ".$user1['IDUtente'].", '".$_POST['oggetto']."', '".$testo."', '".date("d/m/y")."', '".date("H:i")."') ";
	mysql_query($strSQL);
	//Controllo inserimento messaggio
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