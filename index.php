<?php
session_start();
if (isset($_SESSION['username']))
    header("Location:ricerca.php");
else if (isset($_COOKIE['KLogIn'])) {
    $_SESSION['username'] = $_COOKIE['KLogIn'];
    header("Location:ricerca.php");
}

// PDO
//Apertura database
/*include 'db_connect.php';
$query = "INSERT INTO visita (indirizzo_ip, data_visita) VALUES ('prova', 'prova')";
$statement = $pdo->query("SELECT 'Hello, dear MySQL user!' AS _message FROM DUAL");
$row = $statement->fetch(PDO::FETCH_ASSOC);
echo htmlentities($row['_message']);*/

include 'functions.php';
//Apertura database
include 'db_connect.php';

$client_ip = get_client_ip();
$timestamp = date("Y-m-d H:i:s");

/* Debug */
// echo "client_ip: " . $client_ip . ", timestamp: " . $timestamp;

/* Query con PDO -> Da implementare */
// $query = "INSERT INTO visita (indirizzo_ip, data_visita) VALUES ('$client_ip', '$timestamp')";
// $statement = $db->query($query);

//Apertura database
include 'db_connect.php';

$strSQL = "INSERT INTO visita (indirizzo_ip, data_visita) VALUES ('$client_ip','$timestamp')";
$query_result = mysql_query($strSQL);

//Chiusura database
mysql_close($db);

?>

<!DOCTYPE html>
<html lang='en'>
<head>
    <title> Affare Fatto </title>
    <link href="css/bootstrap.css" rel="stylesheet">
    <script src="jquery.js"></script>
    <script src="js/bootstrap.js"></script>

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

        .tab-pane {
            width: 300px;
        }

        .tabs-style {
            padding-top: 20px;
            padding-left: 20px;
            padding-right: 20px;
            border-right-style: solid;
            border-color: #f5f5f5;
            vertical-align: top;
        }

        .carousel-style {
            padding-top: 20px;
            padding-left: 20px;
            padding-right: 20px;
            margin-top: 20px;
            border-right-style: solid;
            color: #f5f5f5;
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

    <div class="container" style="min-width:1000px; min-height:450px;">

        <table>
            <tr>
                <td class="carousel-style" style="vertical-align:top;">
                    <!-- Carousel -->
                    <div id="myCarousel" class="carousel slide" style="margin:0 auto">
                        <div class="carousel-inner">
                            <div class="item active" style="width:550px; height:400px;">
                                <img src="img/slide1.jpg" alt="" style="width:auto; height:100%;">
                                <div class="container">
                                    <div class="carousel-caption" style="height:140px;">
                                        <h1>Non &egrave mai stato cos&igrave; semplice</h1>
                                        <p class="lead">Inserisci il tuo annuncio e inizia a vendere</p>
                                    </div>
                                </div>
                            </div>
                            <div class="item" style="width:550px; height:400px;">
                                <img src="img/slide2.jpg" alt="" style="width:100%; height:100%;">
                                <div class="container">
                                    <div class="carousel-caption" style="height:140px;">
                                        <h1>Hai bisogno di un nuovo telefono?</h1>
                                        <p class="lead">Iscriviti ora e trova il tuo preferito</p>
                                    </div>
                                </div>
                            </div>
                            <div class="item" style="width:550px; height:400px;">
                                <img src="img/slide3.jpg" alt="" style="width:100%; height:100%;">
                                <div class="container">
                                    <div class="carousel-caption" style="height:140px;">
                                        <h1>Incontra di persona il venditore</h1>
                                        <p class="lead">Una casella di posta disponibile per restare contatto</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a class="left carousel-control" href="#myCarousel" data-slide="prev">&lsaquo;</a>
                        <a class="right carousel-control" href="#myCarousel" data-slide="next">&rsaquo;</a>
                    </div>
                </td>

                <td class="span4 tabs-style">
                    <div class="tabbable tabs-above ">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#accedi" data-toggle="tab"><b> Accedi </b></a></li>
                            <li><a href="#regis" data-toggle="tab"><b> Registrati </b></a></li>
                        </ul>
                        <div class="tab-content">
                            <!-- Autenticazione -->
                            <div class="tab-pane active" id="accedi">
                                <form id="form" action="index_autenticazione.php" class="form-inline">
                                    <fieldset>
                                        <div id="nomeutente"><h5>Nome Utente</h5><input type="text" name="nomeutente"
                                                                                        class="input-block-level"
                                                                                        placeholder="Nome Utente"></div>
                                        <div id="pass"><h5>Password</h5><input type="password" name="pass"
                                                                               class="input-block-level"
                                                                               placeholder="Password"></div>
                                        <br>
                                        <label class="checkbox">
                                            <input name="rememberMe" type="checkbox" value="1"> Resta collegato
                                        </label><br><br>
                                        <a href="account_new_password.php"> Hai dimenticato la password? </a><br><br>
                                        <button type="submit" class="btn btn-block btn-primary">Accedi</button>
                                        <br><br>
                                    </fieldset>
                                </form>
                            </div>
                            <!-- Registrazione -->
                            <div class="tab-pane" id="regis">
                                <form id="signup" action="index_registrazione.php" class="form-inline">
                                    <div class="row">
                                        <div id="cognome" class="span2"><h5>Cognome</h5><input type="text"
                                                                                               name="cognome"
                                                                                               class="input-block-level"
                                                                                               placeholder="Cognome">
                                        </div>
                                        <div id="nome" class="span2"><h5>Nome</h5><input type="text" name="nome"
                                                                                         class="input-block-level"
                                                                                         placeholder="Nome"></div>
                                    </div>
                                    <div class="row">
                                        <div id="email" class="span4"><h5>E-Mail</h5><input type="email" name="email"
                                                                                            class="input-block-level"
                                                                                            placeholder="E-Mail"></div>
                                    </div>
                                    <div class="row">
                                        <div id="username" class="span3"><h5>Username</h5><input type="text"
                                                                                                 name="username"
                                                                                                 class="input-block-level"
                                                                                                 placeholder="Username">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div id="password" class="span2"><h5>Password</h5><input type="password"
                                                                                                 name="password"
                                                                                                 class="input-block-level"
                                                                                                 placeholder="Password">
                                        </div>
                                        <div id="password_check" class="span2"><h5>Verifica</h5><input type="password"
                                                                                                       name="password_check"
                                                                                                       class="input-block-level"
                                                                                                       placeholder="Password">
                                        </div>
                                    </div>
                                    <br><br>
                                    <button class="btn btn-block btn-primary" type="submit">Iscriviti</button>
                                </form>
                            </div>
                        </div>
                    </div>


                </td>
            </tr>
        </table>
    </div>
    <div id="push"></div>
</div>
<div id="footer">
    <div class="container" style="max-height:60px;">
        <p style="margin:20px"><a href="#">Progetto di Marco Van e Riccardo Rossi della V A Informatica I.I.S.
                Biella.</a></p>
    </div>
</div>


</body>

<script src="js/bootstrap.js"></script>
<script src="js/scripts.js"></script>


<script type="application/javascript">

    // Invio dati con ajax (AUTENTICAZIONE)
    $("#form").submit(function (event) {
        event.preventDefault();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: $(this).attr("action"),
            data: $(this).serialize(),
            success: function (response) {
                if (response[0].campo == "true")
                    window.location = 'ricerca.php';
                else {
                    for (var i = 0, len = response.length; i < len; i++) {
                        $("#" + response[i].campo).children("span").remove();
                        $("#" + response[i].campo).append("<span class='help-inline'><font color='red'>" + response[i].valore + "</font></span>");
                    }
                }
            }
        });
    });

    // Invio dati con ajax (REGISTRAZIONE)
    $("#signup").submit(function (event) {
        event.preventDefault();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: $(this).attr("action"),
            data: $(this).serialize(),
            success: function (response) {
                if (response[0].campo == "true")
                    $("#regis").html("<h4>Ora sei registrato " + response[0].valore + " &egrave; stata inviata una mail per confermare l\'account.</h4>");
                else
                    for (var i = 0, len = response.length; i < len; i++) {
                        $("#" + response[i].campo).children("span").remove();
                        $("#" + response[i].campo).append("<span class='help-inline' ><font color='red'>" + response[i].valore + "</font></span>");
                    }
            }
        });
    });
</script>

</html>