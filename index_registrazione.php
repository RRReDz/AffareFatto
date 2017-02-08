<?php
require __DIR__ . '/vendor/autoload.php';

$response = array();

if (!isset($_POST['nome']) || !isset($_POST['cognome']) || !isset($_POST['email']) || !isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['password_check']))
    header("location:index.php");

//Controllo compilazione campi
foreach ($_POST as $key => $value) {
    if ($value == "")
        $response[] = array('campo' => $key, 'valore' => 'Devi compilare questo campo.');
}
// Controllo username
if (strlen($_POST['username']) < 6)
    $response[] = array('campo' => 'username', 'valore' => 'Deve contenere almeno 6 caratteri.');
// Controllo password
if (strlen($_POST['password']) < 6)
    $response[] = array('campo' => 'password', 'valore' => 'Deve contenere almeno 6 caratteri.');
else if ($_POST['password'] != $_POST['password_check'])
    $response[] = array('campo' => 'password_check', 'valore' => 'Le password non corrispondono.');

// Invio risposta al client
if (!empty($response)) {
    echo json_encode($response);
    die();
}

//Apertura database
include 'db_connect.php';

// Controllo presenza E-Mail nel database
$strSQL = "SELECT * FROM Utente WHERE EMail LIKE '" . $_POST['email'] . "' LIMIT 1";
$query_result = mysql_query($strSQL);
if (mysql_num_rows($query_result) > 0) {
    mysql_close($db);
    $response[] = array('campo' => 'email', 'valore' => 'Inserisci una nuova mail');
    echo json_encode($response);
    die();
}

// Controllo presenza Nome Utente nel database
$strSQL = "SELECT * FROM Autenticazione WHERE NomeUtente LIKE '" . $_POST['username'] . "' LIMIT 1";
$query_result = mysql_query($strSQL);
if (mysql_num_rows($query_result) > 0) {
    mysql_close($db);
    $response[] = array('campo' => 'username', 'valore' => 'Nome utente gi&#224; esistente.');
    echo json_encode($response);
    die();
}

//Inserimento dati utente
$strSQL = "INSERT Utente (Cognome, Nome, EMail, Immagine) ";
$strSQL .= "VALUES ('" . trim(ucfirst($_POST['cognome'])) . "', '" . trim(ucfirst($_POST['nome'])) . "', '" . trim($_POST['email']) . "', 'default.jpg')";
mysql_query($strSQL);

//Controllo inserimento dati utente
if (mysql_affected_rows() == -1) {
    mysql_close($db);
    $response[] = array('campo' => 'output', 'valore' => 'false');
    echo json_encode($response);
    die();
}

// Inserimento dati autenticazione
$strSQL = "SELECT IDUtente FROM Utente WHERE EMail = '" . $_POST['email'] . "'";
$mysql_result = mysql_query($strSQL);
$utente = mysql_fetch_array($mysql_result);
$sha1_pass = sha1(utf8_encode($_POST['password']));
$chiave_email = md5(uniqid(rand(), true));

$strSQL = "INSERT Autenticazione (IDUtente, NomeUtente, Password, Chiave_Conferma) VALUES (" . $utente['IDUtente'] . ", '" . trim($_POST['username']) . "', '" . $sha1_pass . "', '" . $chiave_email . "')";
mysql_query($strSQL);

$configs = include('configs.php');

$mail = new PHPMailer;
//$mail->SMTPDebug = 3;
$mail->isSMTP();
$mail->Host = $configs->mailjet_configs['host'];
$mail->SMTPAuth = $configs->mailjet_configs['smtp_auth'];
$mail->SMTPSecure = $configs->mailjet_configs['smtp_secure'];
$mail->Username = $configs->mailjet_keys['username'];
$mail->Password = $configs->mailjet_keys['password'];
$mail->Port = $configs->mailjet_configs['port'];
$mail->isHTML(true);
$mail->setFrom($configs->mailjet_configs['from'], $configs->mailjet_configs['from_name']);

$mail->addAddress($_POST['email']);

$mail->Subject = 'Affare Fatto conferma account';
$mail->Body = 'Benvenuto in Affare Fatto! <br><br> Per confermare il tuo account clicca su questo link http://affarefatto.tk/account_confirm.php?key=' . $chiave_email;

if (!$mail->send()) {
    mysql_close($db);
    $response[] = array('campo' => 'output', 'valore' => 'Errore nell\'invio della mail.');
    echo json_encode($response);
    die();
}

//Controllo inserimento dati autenticazione
if (mysql_affected_rows() == -1) {
    mysql_close($db);
    $response[] = array('campo' => 'output', 'valore' => 'Record non inserito');
    echo json_encode($response);
    die();
}

mysql_close($db);

// Invio risposta al client
$response[] = array('campo' => 'true', 'valore' => $_POST['username']);

echo json_encode($response);
die();
?>
