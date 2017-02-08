<?php
include 'session_check.php';

if (!isset($_POST['cap']))
    header("location:profilo.php");

$response = array();

//Apertura database
include 'db_connect.php';

//Selezione comune e provincia
$strSQL = "SELECT comune, prov FROM Italia WHERE cap = " . $_POST['cap'] . "";
$query_result = mysql_query($strSQL) or die(mysql_error());

//Controllo esistenza comune e provincia da cap
if (mysql_num_rows($query_result) == 0) {
    $response[] = array('i' => 'null');
    echo json_encode($response);
    mysql_close($db);
    die();
}

$row = mysql_fetch_array($query_result) or die(mysql_error());
$response[] = array('i' => 'ok', 'com' => $row['comune'], 'prov' => $row['prov']);
echo json_encode($response);
mysql_close($db);
die();

?>
