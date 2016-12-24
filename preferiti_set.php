<?php
	$response = array();
	
	if (!isset($_POST['obj']) || !isset($_POST['user']))
		header("location:preferiti.php");
	
	//Apertura database
	include 'db_connect.php';
	
	if($_POST['flag'] == 0){
		$strSQL = "INSERT INTO Preferiti (Oggetto,Utente) VALUES (".$_POST['obj'].",".$_POST['user'].")";
		$query_result = mysql_query($strSQL) or die(mysql_error());
		$response[] = array("i" => "<i class='icon-ok'></i> Oggetto inserito nella lista dei preferiti!" , "flag" => 1);
		echo json_encode($response);
		}
		
	else{
		$strSQL = "DELETE FROM Preferiti WHERE Oggetto = '".$_POST['obj']."' AND Utente = '".$_POST['user']."'";
		$query_result1 = mysql_query($strSQL) or die(mysql_error());
		$response[] = array("i" => "<i class='icon-ok'></i> Oggetto rimosso dalla lista dei preferiti!" , "flag" => 0);
		echo json_encode($response);
		}
		
	mysql_close($db);

?>