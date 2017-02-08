<?php
session_start();
if (!$_SESSION["username"])
    header("location:index.php");
if (!$_FILES['file'])
    header("location:inserisci_annuncio.php");

$response = array();

$path = "pics/";
$valid_formats = array("jpg", "png", "gif", "bmp", "jpeg");
if ($_FILES['file']['error'] > 0) {
    die("Errore nel file!");
} else {
    $name = $_FILES['file']['name'];
    $size = $_FILES['file']['size'];
    if (strlen($name)) {
        list($txt, $ext) = explode(".", $name);
        if (in_array($ext, $valid_formats)) {
            // Creazione di un nome per l'immagine
            $pictureName = md5(time() . $name) . "." . $ext;
            $tmp = $_FILES['file']['tmp_name'];
            if (move_uploaded_file($tmp, $path . $pictureName) == 0)
                die("Errore nella memorizzazione del file!");
        } else
            die("Formato non valido!");
    }
}

// Elimino l'immagine precedentemente caricata
if (isset($_SESSION['immagine'])) {
    unlink($_SESSION['immagine']);
    unset($_SESSION['immagine']);
}
$_SESSION['immagine'] = $path . $pictureName;

// Invio risposta al client
echo $pictureName;
die();
?>