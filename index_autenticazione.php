<?php
	session_start();
	if (!isset($_POST['nomeutente']) || !isset($_POST['pass']))
		header("location:index.php");
	
	$response = array();
	
	//Controllo compilazione campi e correttezza username
	if ($_POST['nomeutente'] == "") 
		$response[] = array('campo'=>'nomeutente', 'valore'=>'Devi compilare questo campo.');
		
	//Controllo compilazione campi e correttezza password	
	if ($_POST['pass'] == "") 
		$response[] = array('campo'=>'pass', 'valore'=>'Devi compilare questo campo.');	
	
	// Invio risposta al client
	if (!empty($response)){
		echo json_encode($response);
		die();
		}

	//Apertura database
	include 'db_connect.php';
	
	//Controllo Nome utente e password
	$strSQL = "SELECT Password, NomeUtente FROM Autenticazione WHERE NomeUtente ='".$_POST['nomeutente']."'";
	$query_result = mysql_query($strSQL);
	// Se l'utente non è presente
	if (mysql_num_rows($query_result) == 0){
		$response[] = array('campo'=>'nomeutente','valore'=>'Nome utente inesistente.');
		echo json_encode($response);
		mysql_close($db);		
		die();
		}
	// Se l'utente è presente
	else {
		$dat = mysql_fetch_array($query_result);
		// Se la password corrisponde
		if($dat['Password'] == sha1(utf8_encode($_POST['pass']))){
			// Controllo se l'utente è già loggato
			/*$strSQL = "SELECT Sessione FROM Autenticazione WHERE NomeUtente ='".$_POST['nomeutente']."'";
			$query_result = mysql_query($strSQL);
			$obj = mysql_fetch_array($query_result);*/
			/* Creazione di un id sessione
			$strSQL = "SELECT Max(Sessione) AS IDMax FROM Autenticazione";
			$query_result = mysql_query($strSQL);
			$obj = mysql_fetch_array($query_result);
			$ID_session = $obj['IDMax'] + 1;
			$strSQL = "UPDATE Autenticazione SET Sessione = ".$ID_session." WHERE NomeUtente ='".$_POST['nomeutente']."'";
			mysql_query($strSQL);*/
			
			// Controllo se l'account è stato attivato via email
			$strSQL = "SELECT Attivo FROM Autenticazione WHERE NomeUtente = '".$_POST['nomeutente']."'";
			$query_result = mysql_query($strSQL);
			$obj = mysql_fetch_array($query_result);
			if ($obj['Attivo'] == 0) {
				$response[] = array('campo'=>'pass','valore'=>'Conferma il tuo account nella tua mail.');
				echo json_encode($response);
				mysql_close($db);
				die();
				}
			
			// Creazione cookie 
			if (isset($_POST['rememberMe']))
				if($_POST['rememberMe'] == 1){
				$scadenza = time() + 172800;
				setcookie('KLogIn', $_POST['nomeutente'], $scadenza);
				}
			
			// Apro la sessione con l'utente
			$_SESSION['username'] = $dat['NomeUtente'];
			//$_SESSION['id'] = $ID_session;
			$response[]= array('campo'=>'true', 'valore'=>$dat['NomeUtente']);	
			echo json_encode($response);
			mysql_close($db);
			die();
		}
		// Se la password non corrisponde
		else{
			$response[] = array('campo'=>'pass','valore'=>'Nome utente o password errati.');
			echo json_encode($response);
			mysql_close($db);
			die();
		}	
	}
?>
		
		
		