<?php
	include 'session_check.php';
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title> Benvenuto </title>
		
		<link href="css/bootstrap.css" rel="stylesheet">
		<link href="css/homepage.css" rel="stylesheet">
		<script type="text/javascript" src="jquery.js"></script>
		
	</head>
<body>
	
	<?php include 'navbar.html'; ?>
	
	<!-- inserimento annuncio-->
	<form action="inserisci_annuncio_process.php" id="insAnnuncio" class="form-style" method="post">
		<h2> Inserisci il tuo annuncio </h2>
		<div class="row">
			<div class="span6">
				<h5>Immagine</h5>
					<input type="hidden" id="immagine" name="immagine" value="default.jpg">
					<img id="imagePreview" src="pics/400x140.png" class="img-rounded" style="max-height:260px"><br><br>
					<input type="button" id="getFile" value="Sfoglia" class="btn" onclick="this.form.test.click()">
				<h5>Descrizione</h5>
					<div id="descrizione"><textarea name="descrizione" rows="6" style="min-width:400px; max-width:400px;" placeholder="Inserisci la descrizione..."></textarea></div>
			</div>
			<div class="span6">
				<div id="nome"><h5>Titolo</h5><input type="text" name="nome" class="input-block-level" placeholder="Titolo" autocomplete="off"></div>
				<div id="categoria">
					<h5>Categoria</h5>
					<select name="categoria" class="span5">
						<?php
						//Apertura database
						include 'db_connect.php';
						
						$strSQL = "SELECT * FROM Categoria";
						$query_result = mysql_query($strSQL);
						// Creazione categorie
						for ($i = 0; $i < mysql_num_rows($query_result); $i++) {
							$tmp = mysql_fetch_array($query_result);
							echo "<option value=".$tmp['IDCategoria'].">".$tmp['Nome']."</option>";
							}
						mysql_close($db);
						?>
					</select>
				</div>
				<div id="prezzo">
					<h5>Prezzo</h5>
					<input type="text" name="prezzo" class="span2" placeholder="Prezzo" autocomplete="off">
					<font size="5"><b>€</b></font><br>
				</div>
				<br><br>
				<div id="telefono"><label class='checkbox'><input type="checkbox" name="telefono" value="1"> Mostra il numero di telefono</label></div>
				<label class='checkbox'><input type="checkbox" name="email" value="1"> Mostra la tua email</label>
				<div id="mappa"><label class='checkbox'><input type="checkbox" name="mappa" value="1"> Mostra la mappa di dove si trova l'oggetto</label></div>
				<div align="right">
					<br><br><br><button class="btn btn-large btn-primary" style="width:200px;" type="submit">Inserisci Annuncio</button>
				</div>
			</div>
		</div>
	</form>
	
	<!--Inserimento immagine annuncio-->
	<iframe id="upload_target" name="upload_target" style="width:0;height:0;border:0px solid #fff;"></iframe>
	<form action="inserisci_annuncio_imgupload.php" id="imgupload" target="upload_target" method="post" enctype="multipart/form-data">
		<input type="file" name="file" id="file" style="visibility:hidden;position:absolute;top:0;left:0">
		<button id="subImage" style="visibility:hidden;position:absolute;top:0;left:0" type="submit"></button>
	</form>
	
	<!-- Modal Avviso -->
	<div id="alert" class="modal hide alertModal">
		<div class="modal-body">
			<div id="output"></div>
			<div id="output2"></div>
		</div>
		<div class="modal-footer">
			<button id="modalclose" class="btn" data-dismiss="modal" aria-hidden="true">Chiudi</button>
		</div>
	</div>

</body>
	
	<script type="text/javascript" src="js/bootstrap.js"></script>	
	<script src="js/scripts.js"></script>	
	
	<script type="application/javascript">
	
	// Invio dati con Ajax (ANNUNCI)
	$(".alertModal").on('hide', function () {
		window.location = "inserisci_annuncio.php";
		});

	// Invio dati con Ajax (ANNUNCI)
	$("#insAnnuncio").submit(function() {
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: $("#insAnnuncio").attr("action"),
			data: $("#insAnnuncio").serialize(),
			success: function(response) {
				if (response[0].campo == "output") {
					$("#alert").modal("show");
					$("#output").html(response[0].valore);
					}
				else 
					for (var i = 0, len = response.length; i < len; i++) {
						$("#" + response[i].campo).children("span").remove();
						$("#" + response[i].campo).append("<span class='help-inline' style='display: inline-block; margin-top: -1em;'><font color='red'>" + response[i].valore + "</font></span>");	
						}
				}
			});
		return false;
		});
		
	// Gestione bottone sfoglia	
	$("#getFile").click(function () {
		$("#file").trigger('click');
		});
	
	// Submit form quando si sceglie un'immagine	
	$("#file").change(function () {
		$("#imgupload").submit();
		});
	
	// Quando l'iframe si carica si attende la risposta dal server
	$("#upload_target").load(function (){
        var response = $("#upload_target").contents().find('body').text();
		$("#imagePreview").attr('src', "pics/" + response);
		$("#immagine").attr('value', response);
		});		

	</script>
		
</html>