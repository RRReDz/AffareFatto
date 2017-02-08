<!DOCTYPE html>
<html lang='en'>
<head>
    <title> Affare Fatto Recupero Password</title>
    <link href="css/bootstrap.css" rel="stylesheet">
    <style type="text/css">

        html,
        body {
            height: 100%;
        }

        /* Wrapper for page content to push down footer */
        #wrap {
            min-height: 100%;
            height: auto !important;
            height: 100%;
            /* Negative indent footer by it's height */
            margin: 0 auto -60px;
        }

        /* Set the fixed height of the footer here */
        #push,
        #footer {
            height: 60px;
        }

        #footer {
            background-color: #f5f5f5;
        }

        /* Lastly, apply responsive CSS fixes as necessary */
        @media (max-width: 767px) {
            #footer {
                margin-left: -20px;
                margin-right: -20px;
                padding-left: 20px;
                padding-right: 20px;
            }
        }

    </style>
</head>
<body>

<div id="wrap">
    <div class="header" style="background-color: #f5f5f5;">
        <div class="container">
            <img src="img/logo.png" alt="" style="width:auto; height:60px;">
            <img src="img/title.png" style="width:auto; height:80px;">

        </div>
    </div>

    <?php
    require __DIR__ . '/vendor/autoload.php';

    echo "<div class='container'><br> <a href='index.php'><-- Torna indietro</a></div>";

    if (!isset($_POST['chiave']) && !isset($_POST['user'])) {
        echo "<br><div class='container'>
				<h2> Recupero Password </h2>
				<form action='account_new_password.php' method='post'>
					<div class='row'>
						<div class='span5'><h5>Inserisci la mail o nome utente</h5><input type='text' name='chiave' class='input-level' placeholder='E-mail o username'></div>
					</div>
					<div class='row'>
						<div class='span3'><button type='submit' class='btn btn-block btn-primary'>Invia</button></div>
					</div>
				</form>
			</div>";
        echo "<br><br><div class='container'>
				<h2> Cambio Password </h2>
				<form action='account_new_password.php' method='post' class='form-inline'>
					<div class='row'>
						<div class='span4'><h5>Nome Utente</h5><input type='text' name='user' class='input-level' placeholder='Username'></div>
						<div class='span4'><h5>Password Attuale</h5><input type='password' name='old_pass' class='input-level' placeholder='Password'></div>
					</div>
					<div class='row'>
						<div class='span4'><h5>Nuova Password</h5><input type='password' name='new_pass1' class='input-level' placeholder='Nuova Password'></div>
						<div class='span4'><h5>Ripeti Nuova Password</h5><input type='password' name='new_pass2' class='input-level' placeholder='Nuova Password'></div>
					</div>
					<div class='row'>
						<div class='span3'><br><button type='submit' class='btn btn-block btn-primary'>Modifica</button></div>
						<div class='span4'></div>
					</div>
				</form>
			</div>";
    } else if (isset($_POST['chiave'])) {
        // Apertura del database
        include 'db_connect.php';

        // Creazione di una password casuale
        $password = substr(md5(uniqid(rand(), true)), 0, 8);
        $sha1_pass = sha1(utf8_encode($password));

        // Se la stringa ricevuta è un e-mail
        if (filter_var($_POST['chiave'], FILTER_VALIDATE_EMAIL)) {
            $strSQL = "SELECT IDUtente FROM Utente WHERE EMail = '" . $_POST['chiave'] . "'";
            $query_result = mysql_query($strSQL);
            $row = mysql_fetch_array($query_result);
            $strSQL = "UPDATE Autenticazione SET Password = '" . $sha1_pass . "' WHERE IDUtente = " . $row['IDUtente'];
            mysql_query($strSQL);
            $row = array("EMail" => $_POST['chiave']);
        } // Altrimenti se è un username
        else {
            $strSQL = "UPDATE Autenticazione SET Password = '" . $sha1_pass . "' WHERE NomeUtente = '" . $_POST['chiave'] . "'";
            mysql_query($strSQL);
            $strSQL2 = "SELECT EMail FROM Utente U, Autenticazione A WHERE A.IDUtente = U.IDUtente AND NomeUtente = '" . $_POST['chiave'] . "'";
            $query_result = mysql_query($strSQL2);
            $row = mysql_fetch_array($query_result);
        }

        $configs = include('configs.php');

        // Invio email con nuova password
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
        $mail->addAddress($row['EMail']);

        $mail->Subject = 'Affare Fatto recupero password';
        $mail->Body = 'Recupero password di Affare fatto! <br><br> La tua nuova password &egrave;: ' . $password;

        if (!$mail->send()) {
            mysql_close($db);
            $response[] = array('campo' => 'output', 'valore' => 'Errore nell\'invio della mail.');
            echo json_encode($response);
            die();
        }

        echo "<div class='container'>Un e-mail con una nuova password &egrave; stata inviata<br>Sarai reindirizzato alla pagina principale in 5 secondi.</div>";
        header('Refresh: 5; URL=index.php');

        mysql_close($db);
    } // Modifica della password
    else {
        // Apertura del database
        include 'db_connect.php';

        $sha1_old_pass = sha1(utf8_encode(trim($_POST['old_pass'])));
        $strSQL = "SELECT * FROM Autenticazione WHERE NomeUtente = '" . $_POST['user'] . "' AND Password = '" . $sha1_old_pass . "'";
        $query_result = mysql_query($strSQL);
        // Se l'utente è presente
        if (mysql_num_rows($query_result) > 0) {
            // Se le password corrispondono
            if ($_POST['new_pass1'] == $_POST['new_pass2']) {
                $sha1_pass = sha1(utf8_encode($_POST['new_pass1']));
                $row = mysql_fetch_array($query_result);
                $strSQL = "UPDATE Autenticazione SET Password = '" . $sha1_pass . "' WHERE IDUtente = " . $row['IDUtente'];
                mysql_query($strSQL);

                if (mysql_affected_rows() == -1)
                    die(mysql_error());

            } else
                die("Le password non corrispondono.");
        } else
            die("Username o password errati.");

        echo "<div class='container'>La password &egrave; stata modificata correttamente.<br>Sarai reindirizzato alla pagina principale in 5 secondi.</div>";
        header('Refresh: 5; URL=index.php');

        mysql_close($db);
    }
    ?>

    <div id="push"></div>
</div>
<div id="footer">
    <div class="container" style="max-height:60px;">
        <p style="margin:20px"><a href="#">Progetto di Marco Van e Riccardo Rossi della V A Informatica I.I.S.
                Biella.</a></p>
    </div>
</div>

</body>

</html>