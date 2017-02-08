<?php
if (!isset($_GET['key']))
    header("location:index.php");

// Apertura database
include 'db_connect.php';

$strSQL = "UPDATE Autenticazione SET Attivo = 1 WHERE Chiave_Conferma = '" . $_GET['key'] . "'";
mysql_query($strSQL);
if (mysql_affected_rows() == -1)
    echo "Errore durante la conferma";
else {
    echo "L\'account &egrave; stato attivato, tra 5 secondi sarai reindirizzato alla pagina principale. ";
    echo "<br>Se la pagina non viene caricata clicca <a href='index.php'>qui</a>.";
    header('Refresh: 5; URL=index.php');
}

mysql_close($db);
?>