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
			<script type="text/javascript" src="js/bootstrap.js"></script>
			
		</head>
		
		<body>

		<?php include 'navbar.html'; ?>
		
		<form action="preferiti_show.php" id="ricerca" class="form-style">
			<h2> I miei preferiti </h2><br>
			<div class="row">
				<div class="span3"><input type="text" class="input" name="keywords" placeholder="Cerca"></div>
				<div class="span3">
					<select name="categoria">
						<option value="0">Tutte la categorie</option>
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
				<div class="span2">
					<button type="submit" class="btn" style="width: 80px">Cerca</button> <span id="loading"></span>
				</div>
			</div>
			<br>
			<table class="table">
				<tbody id="results">
				
				</tbody>
			</table>
		</form>
		
		<!-- Modal elimina-->
		<div id="delete" class="modal hide">
			<form action="preferiti_delete.php" id="deleteform">
				<div id='modal-body-delete' class="modal-body"></div>
				<div id='modal-footer-delete' class='modal-footer'></div>
			</form>
		</div>
		
		</body>
		
		<script type="text/javascript" src="js/scripts.js"></script>
		<script type="application/javascript">
		
		//Richiama funzione visualizzazione preferiti al caricamento della pagina
		$(document).ready(showList());
		
		// Visualizzazione lista
		$("#ricerca").submit(function() {
			$("#loading").html("<img src='img/loading.gif' style='height:20px; width:20px;'>");
			showList();
			return false;
			});
				
		//Funzione visualizzazione lista preferiti caricamento pagina		
		function showList() {
			$("#results").html("");
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: $("#ricerca").attr("action"),
				data: $("#ricerca").serialize(),
				success: function(response) {
					if (response[0].ID == 'vuoto') {
						$("#results").html("").append("<div align='center'><h4>Nessun oggetto inserito nei preferiti!</h4></div>");	
						}
					else {
						$("#results").html("");
						for (var i = 0, len = response.length; i < len; i++) {
							var code = "<tr><td rowspan='2' width='160' height='150'><a style='text-decoration: none' href=preferiti_showobject.php?objID=" + response[i].ID + "><img src='" + response[i].Immagine + "' class='img-rounded' style='max-height:150px'></a></td>";
							code += "<td height='37' colspan='3'><b><a style='text-decoration: none' href=preferiti_showobject.php?objID=" + response[i].ID + ">" + response[i].Titolo + "</a></b></td>";
							code += "<td><div align=right><a href='#delete' data-id=" + response[i].ID + " class='open-delete btn' data-toggle='modal'><i class='icon-trash'></i> Rimuovi</a></div></td></tr>";
							code += "<tr><td style='line-height: 3' width='150'>"  + response[i].Prezzo + " &euro; </td>";
							code += "<td style='line-height: 3' width='170'>" + response[i].Categoria + "</td>";
							code += "<td style='line-height: 3' width='170'>Luogo " + response[i].Luogo + "</td>";
							code += "<td style='line-height: 3'>" + response[i].Data + " , " + response[i].Ora + "</td></tr>"; 
							$("#results").append(code);	
							}
						}
					$("#loading").html("");
					}
				})
			}
			
			// Invio dati con Ajax (ELIMINA ANNUNCIO)
			$("#deleteform").submit(function() {
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: $("#deleteform").attr("action"),
				data: $("#deleteform").serialize(),
				success: function(response) {
					if (response[0].valore == "true") {
						$("#modal-body-delete").html("<i class='icon-ok'></i> Oggetto rimosso dai preferiti con successo!");
						$("#modal-footer-delete").html("<button class='btn' data-dismiss='modal' aria-hidden='true'>Chiudi</button>");
						}
					else {
						$("#modal-body-delete").html("<i class='icon-remove'></i> Errore nell'eliminazione dell'oggetto dai preferiti.");
						$("#modal-footer-delete").html("<button class='btn' data-dismiss='modal' aria-hidden='true'>Chiudi</button>");
						}
					showList();
					}
				});
			return false;
			});
			
			// Creazione del modal di eliminazione oggetto
			$(document).on("click", ".open-delete", function () {
			$("#modal-body-delete").html("<div class='modal-body'>Sei sicuro di voler rimuovere questo oggetto dai preferiti?</div>");
			var html = "<button class='btn' data-dismiss='modal' aria-hidden='true'>Annulla</button>";
			html += "<button type='submit' id='submit' class='btn btn-primary'>Elimina</button>";
			$("#modal-footer-delete").html(html);
			var id = $(this).data('id');
			$("#modal-body-delete").append("<input type='hidden' name='IDOggetto' value=" + id + ">");
			});
					
		</script>
		
	</html>
