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

	<!--Le mie inserzioni-->	
	<form action="miei_annunci_show.php" id="ricerca" class="form-style">
		<h2> Gestisci i miei annunci </h2><br>
		<div class="row">
			<div class="span3"><input type="text" class="input" name="keywords"  placeholder="Cerca"></div>
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
	
	<!-- Modal modifica immagine -->
	<!--Inserimento immagine annuncio
		<iframe id="upload_target" name="upload_target" style="width:0;height:0;border:0px solid #fff;"></iframe>
		<form action="inserisci_annuncio_imgupload.php" id="imgupload" target="upload_target" method="post" enctype="multipart/form-data">
			<input type="file" name="file" id="file" style="visibility:hidden;position:absolute;top:0;left:0">
			<button id="subImage" style="visibility:hidden;position:absolute;top:0;left:0" type="submit"></button>
		</form>
	<div class="span4">
						<h5>Immagine</h5>
							<img id="imagePreview" class="img-rounded" style="max-height:260px"><br>
							<input type="hidden" id="immagine" name="immagine" value="default.jpg">
							<input type="button" id="getFile" value="Sfoglia" class="btn" onclick="this.form.test.click()">
					</div>-->
	
	
	<!-- Modal modifica-->
	<div id="edit" class="modal modal-style hide fade">		
		<form action="miei_annunci_edit.php" id="editform">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h5>Titolo</h5><div id="nome"><input type="text" name="nome" class="input-level"></div>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="span4">
						<div id="categoria">
							<h5>Categoria</h5>
							<select name="categoria" class="select">
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
						<div id="prezzo">
							<h5>Prezzo</h5>
							<input type="text" name="prezzo" class="span2" placeholder="Prezzo" autocomplete="off">
							<font size="5"><b>€</b></font><br>
						</div>
					</div>
					<div class="span3">
						<h5>Opzioni</h5>
						<div id="telefono"><label class='checkbox'><input type="checkbox" id="telefono" name="telefono" value="1"> Mostra il numero di telefono</label></div>
						<label class='checkbox'><input type="checkbox" name="email" value="1"> Mostra la tua email</label>
						<div id="mappa"><label class='checkbox'><input type="checkbox" name="mappa" value="1"> Mostra la mappa in cui si trova l'oggetto</label></div>
					</div>
				</div><br>				
				<div class="row">
					<div class="span5">
						<h5>Descrizione</h5>
						<div id="descrizione">
							<textarea name="descrizione" rows="6" style="min-width:540px; max-width:540px;"></textarea>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" name="id">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Annulla</button>
				<button type="submit" class="btn btn-primary" aria-hidden="true">Modifica</button>
			</div>
		</form>
	</div>
	
	<!-- Modal elimina-->
	<div id="delete" class="modal hide">
		<form action="miei_annunci_delete.php" id="deleteform">
			<div id='modal-body-delete' class="modal-body"></div>
			<div id='modal-footer-delete' class='modal-footer'></div>
		</form>
	</div>
	
	
</body>

	<script type="text/javascript" src="js/bootstrap.js"></script>	
	<script type="text/javascript" src="js/scripts.js"></script>	
	
	<script type="application/javascript">
	
	// Visualizza le mie inserzioni
	$(document).ready(showList(0, 0));
	
	// Visualizza i risultati della ricerca
	$("#ricerca").submit(function() {
		var k = $("[name='keywords']", "#ricerca").val();
		var c = $("[name='categoria']", "#ricerca").val();
		$("#loading").html("<img src='img/loading.gif' style='height:20px; width:20px;'>");
		showList(k,c);
		return false;
		});
	
	// Visualizzazione lista
	function showList(k,c) {
		$("#results").html("");
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: "miei_annunci_show.php",
			data: "keywords=" + k + "&categoria=" + c,
			success: function(response) {
				if (response[0].ID == 'vuoto') {
					$("#results").append("<div align='center'><h4>Nessuna inserzione inserita!</h4></div>");	
					}
				else {
					$("#results").html("");
					for (var i = 0, len = response.length; i < len; i++) {
						var code = "<tr><td rowspan='2' width='140' height='150'><a style='text-decoration: none' href=ricerca_showobject.php?objID=" + response[i].ID + "><img src='" + response[i].Immagine + "' class='img-rounded' style='max-height:150px'></a></td>";
						code += "<td width='300' colspan='2'><b><a style='text-decoration: none' href=ricerca_showobject.php?objID=" + response[i].ID + ">" + response[i].Titolo + "</a></b></td>";
						code += "<td width='100'>" + response[i].Categoria + "</div></td>";
						code += "<td width='50'>" + response[i].Prezzo + " €</div></td>";
						code += "<td align='center' width='20'><a href='#edit' data-id=" + response[i].ID + " class='open-edit btn' data-toggle='modal'><i class='icon-pencil'></i></a></td>";
						code += "<td align='center' width='20'><a href='#delete' data-id=" + response[i].ID + " class='open-delete btn' data-toggle='modal'><i class='icon-trash'></i></a></td></tr>";
						code += "<tr><td colspan='4'>" + response[i].Descrizione + "</td>";
						code += "<td colspan='2'>";
						if (response[i].Telefono == 1)
							code += "<label class='checkbox'><i class='icon-ok'></i> Telefono</label>";
						else
							code += "<label class='checkbox'><i class='icon-remove'></i> Telefono</label>";
						if (response[i].EMail == 1)
							code += "<label class='checkbox'><i class='icon-ok'></i> E-Mail</label>";
						else
							code += "<label class='checkbox'><i class='icon-remove'></i> E-Mail</label>";
                        if (response[i].Mappa == 1)
							code += "<label class='checkbox'><i class='icon-ok'></i> Mappa</label></td></tr>";
						else
							code += "<label class='checkbox'><i class='icon-remove'></i> Mappa</label></td></tr>";
						$("#results").append(code);	
						}
					}
				$("#loading").html("");
				}
			});		
		}
	
	
	// Recupero dati per modificare l'annuncio
	$(document).on("click", ".open-edit", function () {
		$(':input','#editform').val("");
		$(':checkbox','#editform').prop("checked", false);
		$("#editform span").remove();
		var id = $(this).data('id');
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: "miei_annunci_getdata.php",
			data: "id=" + id,
			success: function(response) {
				$("[name=nome]").val(response[0].Titolo);
				$("[name=categoria]").val(response[0].Categoria);
				$("[name=prezzo]").val(response[0].Prezzo);
				$("[name=descrizione]").val(response[0].Descrizione);
				if (response[0].Telefono == "1")
					$("[name=telefono]").prop("checked",true);
				if (response[0].EMail == "1")
					$("[name=email]").prop("checked",true);
				if (response[0].Mappa == "1")
					$("[name=mappa]").prop("checked",true);
				$("[name=id]").val(response[0].ID);
				}
			});
		});
	
	// Aggiornamento annuncio
	$("#editform").submit(function() {
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: $("#editform").attr("action"),
			data: $("#editform").serialize(),
			success: function(response) {
				if (response[0].valore == "true") {
					showList(0,0);
					$('#edit').modal('hide');
					}
				else 
					for (var i = 0, len = response.length; i < len; i++) {
						$("#" + response[i].campo).children("span").remove();
						$("#" + response[i].campo).append("<span class='help-inline' ><font color='red'>" + response[i].valore + "</font></span>");	
						}
				}
			});
		return false;
		});
			
	// Invio dati con Ajax (ELIMINA ANNUNCIO)
	$("#deleteform").submit(function() {
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: $("#deleteform").attr("action"),
			data: $("#deleteform").serialize(),
			success: function(response) {
				if (response[0].valore == "true") {
					$("#modal-body-delete").html("<i class='icon-ok'></i> Oggetto eliminato con successo!");
					$("#modal-footer-delete").html("<button class='btn' data-dismiss='modal' aria-hidden='true'>Chiudi</button>");
					}
				else {
					$("#modal-body-delete").html("<i class='icon-remove'></i> Errore nell'eliminazione dell'oggetto.");
					$("#modal-footer-delete").html("<button class='btn' data-dismiss='modal' aria-hidden='true'>Chiudi</button>");
					}
				showList(0,0);			
				}
			});
		return false;
		});
	
	// Creazione del modal di eliminazione oggetto
	$(document).on("click", ".open-delete", function () {
		$("#modal-body-delete").html("<div class='modal-body'>Sei sicuro di voler cancellare questo annuncio?</div>");
		var html = "<button class='btn' data-dismiss='modal' aria-hidden='true'>Annulla</button>";
		html += "<button type='submit' id='submit' class='btn btn-primary'>Elimina</button>";
		$("#modal-footer-delete").html(html);
		var id = $(this).data('id');
		$("#modal-body-delete").append("<input type='hidden' name='IDOggetto' value=" + id + ">");
		});		

	</script>

</html>