<?php
include 'session_check.php';

if (!isset($_POST['nome']) || !isset($_POST['prezzo']) || !isset($_POST['categoria']) || !isset($_POST['descrizione']))
    header("location:inserisci_annuncio.php");

$response = array();

// Controllo nome annuncio
if (strlen($_POST['nome']) < 10)
    $response[] = array('campo' => 'nome', 'valore' => 'Deve contenere almeno 10 caratteri.');
// Controllo prezzo
if ($_POST['prezzo'] == "")
    $response[] = array('campo' => 'prezzo', 'valore' => 'Devi inserire un prezzo');
else if (!is_numeric($_POST['prezzo']) || $_POST['prezzo'] < 0) {
    $response[] = array('campo' => 'prezzo', 'valore' => 'Devi inserire un prezzo valido');
}
// Controllo descrizione
if (strlen($_POST['descrizione']) == "")
    $response[] = array('campo' => 'descrizione', 'valore' => 'Devi compilare questo campo.');

// Invio risposta al client
if (!empty($response)) {
    echo json_encode($response);
    die();
}

// Apertura database
include 'db_connect.php';

// Controllo per numero telefonico
if (isset($_POST['telefono'])) {
    $strSQL = "SELECT Telefono FROM Utente U, Autenticazione A WHERE A.IDUtente = U.IDUtente AND NomeUtente = '" . $_SESSION['username'] . "'";
    $query_result = mysql_query($strSQL);
    $row = mysql_fetch_array($query_result);
    if ($row['Telefono'] == "") {
        $response[] = array('campo' => 'telefono', 'valore' => "Devi inserire un numero di recapito clicca <a href='profilo.php'>qui</a>.");
        echo json_encode($response);
        die();
    }
    $telefono = 1;
} else
    $telefono = 0;

if (!isset($_POST['email']))
    $email = 0;
else
    $email = 1;

// Controllo per mappa e indirizzo
if (isset($_POST['mappa'])) {
    $strSQL = "SELECT Indirizzo, Paese, CAP FROM Utente U, Autenticazione A WHERE A.IDUtente = U.IDUtente AND NomeUtente = '" . $_SESSION['username'] . "'";
    $query_result = mysql_query($strSQL);
    $row = mysql_fetch_array($query_result);
    if ($row['Mappa'] == "") {
        $response[] = array('campo' => 'mappa', 'valore' => "Devi inserire un indirizzo clicca <a href='profilo.php'>qui</a>.");
        echo json_encode($response);
        die();
    }
    $mappa = 1;
} else
    $mappa = 0;


$testo = mysql_real_escape_string($_POST['descrizione']);

// Inserimento dati utente
$strSQL = "UPDATE Oggetto SET Nome = '" . $_POST['nome'] . "', Prezzo = '" . trim($_POST['prezzo']) . "', Categoria = " . $_POST['categoria'] . ",  ";
$strSQL .= "Descrizione = '" . $testo . "', Telefono = " . $telefono . ", EMail = " . $email . ", Mappa = " . $mappa . " ";
$strSQL .= "WHERE CodOgg = " . $_POST['id'];
$query_result = mysql_query($strSQL);

// Controllo aggiornamento annuncio
if (mysql_affected_rows() == -1) {
    mysql_close($db);
    $response[] = array('campo' => 'output', 'valore' => 'false');
    echo json_encode($response);
    die();
}

mysql_close($db);

// Invio risposta al client
$response[] = array('campo' => 'output', 'valore' => "true");
echo json_encode($response);
die();
?>