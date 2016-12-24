<?php
	include 'session_check.php';
	
	if (!isset($_POST['keywords']) || !isset($_POST['categoria']))
		header("location:preferiti.php");
	
	$response = array();
	
	//Apertura database
	include 'db_connect.php';
	
	//Selezione ID dell'utente dal nickname
		$strSQL = "SELECT IDUtente AS ID FROM Autenticazione WHERE NomeUtente = '".$_SESSION['username']."'";
		$query_result = mysql_query($strSQL) or die(mysql_error());
		$row = mysql_fetch_array($query_result);
	
	//Selezione oggetti tutte le categorie
	if($_POST['categoria'] == 0){
		$strSQL = "SELECT Provincia, O.CodOgg AS COBJ, O.Nome AS ONome, Prezzo, Data, Ora, C.Nome AS CNome ";
		$strSQL .= "FROM Preferiti P, Utente U, Oggetto O, Categoria C ";
		$strSQL .= "WHERE P.Utente = U.IDUtente AND O.CodOgg = P.Oggetto AND C.IDCategoria = O.Categoria ";
		$strSQL .= "AND U.IDUtente = '".$row['ID']."' AND O.Nome LIKE '%".$_POST['keywords']."%'";
		}
		
	//Selezione oggetti categoria definita	
	else{	
		$strSQL = "SELECT Provincia, O.CodOgg AS COBJ, O.Nome AS ONome, Prezzo, Data, Ora, C.Nome AS CNome ";
		$strSQL .= "FROM Preferiti P, Utente U, Oggetto O, Categoria C ";
		$strSQL .= "WHERE P.Utente = U.IDUtente AND O.CodOgg = P.Oggetto AND C.IDCategoria = O.Categoria ";
		$strSQL .= "AND U.IDUtente = '".$row['ID']."' AND O.Nome LIKE '%".$_POST['keywords']."%' AND C.IDCategoria = ".$_POST['categoria']."";
		}
		
	$query_result = mysql_query($strSQL) or die(mysql_error());
	
	for ($i = 0; $i < mysql_num_rows($query_result); $i++) {
		$obj = mysql_fetch_array($query_result);
		// Immagine dell'oggetto
		$strSQL = "SELECT Immagine FROM Oggetto WHERE CodOgg = ".$obj['COBJ'];
		$query_result2 = mysql_query($strSQL);
		$pic = mysql_fetch_array($query_result2);
		$response[] = array('ID'=>$obj['COBJ'], 'Titolo'=>$obj['ONome'], 'Categoria'=>$obj['CNome'], 'Prezzo'=>$obj['Prezzo'], 'Luogo'=>$obj['Provincia'], 'Data'=>$obj['Data'], 'Ora'=>$obj['Ora'], 'Immagine'=>"pics/".$pic['Immagine']);
		}
		
	mysql_close($db);
	
	// Invio risposta al client
	if (!empty($response)) {
		echo json_encode($response);
		die();
		}
	
	// Invio risposta al client
	$response[]= array('ID'=>'vuoto');
	echo json_encode($response);
	die();
?>