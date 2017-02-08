<?php
// Connessione al database
$db = mysql_connect("localhost", "root", "") or die("Impossibile connettersi al database.");
mysql_select_db("affarefatto", $db) or die ("Impossibile aprire il database.");
?>