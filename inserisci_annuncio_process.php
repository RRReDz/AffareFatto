<?php	
	session_start();
	
	if (!isset($_POST['nome']) || !isset($_POST['prezzo']) || !isset($_POST['categoria']) || !isset($_POST['descrizione']))
		header("location:inserisci_annuncio.php");
	
	$response = array();
	
	//Controllo compilazione campi
	foreach ($_POST as $key => $value) {
		if ($value == "") 
			$response[] = array('campo'=>$key, 'valore'=>'Devi compilare questo campo.');
		}
	// Controllo nome annuncio
	if (strlen($_POST['nome']) < 10) 
		$response[] = array('campo'=>'nome', 'valore'=>'Deve contenere almeno 10 caratteri.');
	// Controllo prezzo
	if ($_POST['prezzo'] == "")
		$response[] = array('campo'=>'prezzo', 'valore'=>'Devi inserire un prezzo');
	else if (!is_numeric($_POST['prezzo']) || $_POST['prezzo'] < 0) {
		$response[] = array('campo'=>'prezzo', 'valore'=>'Devi inserire un prezzo valido');
		}
	
	// Invio risposta al client
	if (!empty($response)) {
		echo json_encode($response);
		die();
		}
		
	//Apertura database
	include 'db_connect.php';
	
	// Controllo per numero telefonico
	if (isset($_POST['telefono'])) {
		$strSQL = "SELECT Telefono FROM Utente U, Autenticazione A WHERE A.IDUtente = U.IDUtente AND NomeUtente = '".$_SESSION['username']."'";
		$query_result = mysql_query($strSQL);
		$row = mysql_fetch_array($query_result);
		if ($row['Telefono'] == "") {
			$response[] = array('campo'=>'telefono', 'valore'=>"Devi inserire un numero di recapito clicca <a href='profilo.php'>qui</a>.");
			echo json_encode($response);
			die();
			}
		$telefono = 1;
		}
	else 
		$telefono = 0;
	
	if (!isset($_POST['email']))
		$email = 0;
	else
		$email = 1;
	
	// Controllo per mappa e indirizzo
	if (isset($_POST['mappa'])) {
		$strSQL = "SELECT Indirizzo, Paese, CAP FROM Utente U, Autenticazione A WHERE A.IDUtente = U.IDUtente AND NomeUtente = '".$_SESSION['username']."'";
		$query_result = mysql_query($strSQL);
		$row = mysql_fetch_array($query_result);
		if ($row['Indirizzo'] == "" && $row['Paese'] == "" && $row['CAP'] == "") {
			$response[] = array('campo'=>'mappa', 'valore'=>"Devi inserire un indirizzo clicca <a href='profilo.php'>qui</a>.");
			echo json_encode($response);
			die();
			}
		$mappa = 1;
		}
	else
		$mappa = 0;
		
		
	$testo = mysql_real_escape_string($_POST['descrizione']);
	
	//Inserimento dati utente
	$strSQL = "SELECT IDUtente FROM Autenticazione WHERE NomeUtente = '".$_SESSION['username']."'";
	$user = mysql_fetch_array(mysql_query($strSQL));
	$strSQL = "INSERT Oggetto (Nome, Categoria, Prezzo, Descrizione, Immagine, Telefono, EMail, Mappa, Data, Ora, IDUtente) ";
	$strSQL .= "VALUES ('".$_POST['nome']."', '".$_POST['categoria']."', '".trim($_POST['prezzo'])."', '".$testo."', '".$_POST["immagine"]."', ";
	$strSQL .= " ".$telefono.", ".$email.", ".$mappa.", '".date("d/m/y")."', '".date("H:i")."', '".$user['IDUtente']."')";
	mysql_query($strSQL);
	//Controllo inserimento dati annuncio
	if (mysql_affected_rows() == -1) {
		mysql_close($db);
		$response[] = array('campo'=>'output', 'valore'=>"Errore nel caricamento dell'annuncio. Riprovare.");
		echo json_encode($response);
		die();
		}

	mysql_close($db);
		
	if (isset($_SESSION['immagine']))
		unset($_SESSION['immagine']);	
	
	// Invio risposta al client
	if (!empty($response)) {
		echo json_encode($response);
		die();
		}
	
	// Invio risposta al client
	$response[]= array('campo'=>'output', 'valore'=>'<i class=icon-ok></i> Annuncio inserito.');
	echo json_encode($response);
	die();
?>