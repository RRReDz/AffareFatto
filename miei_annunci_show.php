<?php
include 'session_check.php';

if (!isset($_POST['keywords']) || !isset($_POST['categoria']))
    header("location:miei_annunci.php");

$response = array();

//Apertura database
include 'db_connect.php';

// Selezione dei dati relativi all'oggetto
$strSQL = "SELECT CodOgg, O.Nome AS ONome, C.Nome AS CNome, Prezzo, Descrizione, O.Telefono, O.EMail, O.Mappa ";
$strSQL .= "FROM Oggetto O, Autenticazione A, Categoria C ";
$strSQL .= "WHERE NomeUtente = '" . $_SESSION['username'] . "' AND A.IDUtente = O.IDUtente AND IDCategoria = Categoria";
// Se � stato ricercato qualcosa
if (isset($_POST['keywords']) && $_POST['keywords'] != '0')
    $strSQL .= " AND O.Nome LIKE '%" . $_POST['keywords'] . "%'";
// Se � stata selezionata una specifica categoria
if (isset($_POST['categoria']) && $_POST['categoria'] != 0)
    $strSQL .= " AND IDCategoria = " . $_POST['categoria'];
$strSQL .= " ORDER BY CodOgg DESC";

$query_result = mysql_query($strSQL);
for ($i = 0; $i < mysql_num_rows($query_result); $i++) {
    $obj = mysql_fetch_array($query_result);
    // Immagine dell'oggetto
    $strSQL = "SELECT Immagine FROM Oggetto WHERE CodOgg = " . $obj['CodOgg'];
    $query_result2 = mysql_query($strSQL);
    $pic = mysql_fetch_array($query_result2);
    $response[] = array('ID' => $obj['CodOgg'], 'Titolo' => $obj['ONome'], 'Categoria' => $obj['CNome'], 'Prezzo' => $obj['Prezzo'], 'Descrizione' => $obj['Descrizione'], 'Telefono' => $obj['Telefono'], 'EMail' => $obj['EMail'], 'Mappa' => $obj['Mappa'], 'Immagine' => "pics/" . $pic['Immagine']);
}

mysql_close($db);

// Invio risposta al client
if (!empty($response)) {
    echo json_encode($response);
    die();
}

// Invio risposta al client
$response[] = array('ID' => 'vuoto');
echo json_encode($response);
die();
?>