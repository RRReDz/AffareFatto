<?php
session_start();

$response = array();

//Apertura database
include 'db_connect.php';

// Selezione dei messaggi in arrivo
$strSQL = "SELECT COUNT(*) AS Conta ";
$strSQL .= "FROM Autenticazione A, Messaggio M, (SELECT IDUtente, NomeUtente FROM Autenticazione, Messaggio WHERE IDUtente = Mittente GROUP BY NomeUtente) T ";
$strSQL .= "WHERE Destinatario = A.IDUtente AND A.NomeUtente = '" . $_SESSION['username'] . "' AND T.IDUtente = Mittente AND CestinoD = 0 AND Letto = 0";
$query_result = mysql_query($strSQL);
$obj = mysql_fetch_array($query_result);
$response[] = array('nMessaggi' => $obj['Conta']);

mysql_close($db);

// Invio risposta al client
if (!empty($response)) {
    echo json_encode($response);
    die();
}

// Invio risposta al client
$response[] = array('nMessaggi' => '0');
echo json_encode($response);
die();
?>