<?php
session_start();

if (!isset($_POST['IDMess']))
    header("location:messaggi.php");

$response = array();

//Apertura database
include 'db_connect.php';

foreach ($_POST['IDMess'] as $key => $value) {
    //Spostamento messaggio nel cestino
    $strSQL = "SELECT * FROM Messaggio WHERE Mittente = (SELECT IDUtente FROM Autenticazione WHERE NomeUtente = '" . $_SESSION['username'] . "') AND Codice = " . $value;
    $query_result = mysql_query($strSQL);
    // Se il messaggio � ricevuto
    if (mysql_num_rows($query_result) == 0) {
        $strSQL = "SELECT CestinoD FROM Messaggio WHERE Codice = " . $value;
        $query_result = mysql_query($strSQL);
        $obj = mysql_fetch_array($query_result);
        // Sposto nel cestino
        if ($obj['CestinoD'] == 0) {
            $strSQL = "UPDATE Messaggio SET CestinoD = 1 WHERE Codice = " . $value;
            mysql_query($strSQL);
        } // "Elimino" il messaggio per l'utente
        else if ($obj['CestinoD'] == 1) {
            $strSQL = "UPDATE Messaggio SET CestinoD = 2 WHERE Codice = " . $value;
            mysql_query($strSQL);
        }
    } // Se il messaggio � uno inviato
    else {
        $strSQL = "SELECT CestinoM FROM Messaggio WHERE Codice = " . $value;
        $query_result = mysql_query($strSQL);
        $obj = mysql_fetch_array($query_result);
        // Sposto nel cestino
        if ($obj['CestinoM'] == 0) {
            $strSQL = "UPDATE Messaggio SET CestinoM = 1 WHERE Codice = " . $value;
            mysql_query($strSQL);
        } // "Elimino" il messaggio per l'utente
        else if ($obj['CestinoM'] == 1) {
            $strSQL = "UPDATE Messaggio SET CestinoM = 2 WHERE Codice = " . $value;
            mysql_query($strSQL);
        }
    }

    // Elimina il messaggio se sia destinatario che mittente l'hanno spostato nel cestino
    $strSQL = "SELECT CestinoM, CestinoD FROM Messaggio WHERE Codice = " . $value;
    $query_result = mysql_query($strSQL);
    $obj = mysql_fetch_array($query_result);
    if ($obj['CestinoM'] == 2 && $obj['CestinoD'] == 2) {
        //Eliminazione Oggetto
        $strSQL = "DELETE FROM Messaggio WHERE Codice = " . $value;
        mysql_query($strSQL);
    }

    //Controllo query eseguita
    if (mysql_affected_rows() == -1) {
        mysql_close($db);
        $response[] = array('campo' => 'false', 'valore' => "false");
        echo json_encode($response);
        die();
    }
}

mysql_close($db);

// Invio risposta al client
$response[] = array('campo' => 'true', 'valore' => "true");
echo json_encode($response);
die();
?>