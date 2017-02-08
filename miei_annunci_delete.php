<?php
include 'session_check.php';

if (!isset($_POST['IDOggetto']))
    header("location:miei_annunci.php");

$response = array();

//Apertura database
include 'db_connect.php';

//Eliminazione immagine oggetto
$strSQL = "SELECT Immagine FROM Oggetto WHERE CodOgg = " . $_POST['IDOggetto'];
$query_result = mysql_query($strSQL);
$obj = mysql_fetch_array($query_result);
unlink("pics/" . $obj['Immagine']);

//Eliminazione Oggetto
$strSQL = "DELETE FROM Oggetto WHERE CodOgg = " . $_POST['IDOggetto'];
mysql_query($strSQL);

//Controllo inserimento dati annuncio
if (mysql_affected_rows() == -1) {
    mysql_close($db);
    $response[] = array('campo' => 'output', 'valore' => 'false');
    echo json_encode($response);
    die();
}

mysql_close($db);

// Invio risposta al client
$response[] = array('campo' => 'output', 'valore' => 'true');
echo json_encode($response);
die();
?>