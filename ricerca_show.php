<?php
include 'session_check.php';

$response = array();

//Apertura database
include 'db_connect.php';

//Selezione oggetti tutte le categorie
if ($_POST['categoria'] == 0 && $_POST['city'] == '0') {
    $strSQL = "SELECT NomeUtente, Provincia, CodOgg AS COBJ, O.Nome AS ONome, Prezzo, Data, Ora, C.Nome AS CNome ";
    $strSQL .= "FROM Autenticazione A, Utente U, Oggetto O, Categoria C ";
    $strSQL .= "WHERE O.IDUtente = U.IDUtente AND C.IDCategoria = O.Categoria AND A.IDUtente = U.IDUtente ";
    $strSQL .= "AND O.Nome LIKE '%" . $_POST['keywords'] . "%' ORDER BY CodOgg DESC";
} //Selezione oggetti per categoria di ogni provincia
else if ($_POST['categoria'] != 0 && $_POST['city'] == '0') {
    $strSQL = "SELECT NomeUtente, Provincia, CodOgg AS COBJ, O.Nome AS ONome, Prezzo, Data, Ora, C.Nome AS CNome ";
    $strSQL .= "FROM Autenticazione A, Utente U, Oggetto O, Categoria C ";
    $strSQL .= "WHERE O.IDUtente = U.IDUtente AND C.IDCategoria = O.Categoria AND A.IDUtente = U.IDUtente ";
    $strSQL .= "AND O.Nome LIKE '%" . $_POST['keywords'] . "%' AND C.IDCategoria = " . $_POST['categoria'] . " ORDER BY CodOgg DESC";
} //Selezione oggetti per provincia di ogni categoria
else if ($_POST['categoria'] == 0 && $_POST['city'] != '0') {
    $strSQL = "SELECT NomeUtente, Provincia, CodOgg AS COBJ, O.Nome AS ONome, Prezzo, Data, Ora, C.Nome AS CNome ";
    $strSQL .= "FROM Autenticazione A, Utente U, Oggetto O, Categoria C ";
    $strSQL .= "WHERE O.IDUtente = U.IDUtente AND C.IDCategoria = O.Categoria AND A.IDUtente = U.IDUtente ";
    $strSQL .= "AND O.Nome LIKE '%" . $_POST['keywords'] . "%' AND Provincia = '" . $_POST['city'] . "' ORDER BY CodOgg DESC";
} // Selezione dei dati relativi alla provincia e alla categoria
else {
    $strSQL = "SELECT NomeUtente, Provincia, CodOgg AS COBJ, O.Nome AS ONome, Prezzo, Data, Ora, C.Nome AS CNome ";
    $strSQL .= "FROM Autenticazione A, Utente U, Oggetto O, Categoria C ";
    $strSQL .= "WHERE O.IDUtente = U.IDUtente AND C.IDCategoria = O.Categoria AND A.IDUtente = U.IDUtente ";
    $strSQL .= "AND O.Nome LIKE '%" . $_POST['keywords'] . "%' AND C.IDCategoria = " . $_POST['categoria'] . " AND Provincia = '" . $_POST['city'] . "' ORDER BY CodOgg DESC";
}

$query_result = mysql_query($strSQL);

for ($i = 0; $i < mysql_num_rows($query_result); $i++) {
    $obj = mysql_fetch_array($query_result);
    // Immagine dell'oggetto
    $strSQL = "SELECT Immagine FROM Oggetto WHERE CodOgg = " . $obj['COBJ'];
    $query_result2 = mysql_query($strSQL);
    $pic = mysql_fetch_array($query_result2);
    $response[] = array('ID' => $obj['COBJ'], 'Titolo' => $obj['ONome'], 'User' => $obj['NomeUtente'], 'Categoria' => $obj['CNome'], 'Prezzo' => $obj['Prezzo'], 'Luogo' => $obj['Provincia'], 'Data' => $obj['Data'], 'Ora' => $obj['Ora'], 'Immagine' => "pics/" . $pic['Immagine']);
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